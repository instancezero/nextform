<?php

namespace Abivia\NextForm;

use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface as RenderInterface;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Data\SchemaCollection;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\ContainerBinding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\Render\Block;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 *
 */
class NextForm
{

    public const CONFIRM_LABEL = '_confirm';
    public const CONTAINER_LABEL = '_container';
    public const HELP_LABEL = '_formhelp';
    public const GROUP_DELIM = ':';
    public const SEGMENT_DELIM = '.';

    /**
     * The access controller
     * @var AccessInterface
     */
    protected $access;

    /**
     * External for custom token generation (to return [name, value]),
     * @var Callable
     */
    static protected $csrfGenerator;

    /**
     * The current CSRF token [name, value]
     * @var array
     */
    static protected $csrfToken;

    /**
     * The results of generating each form.
     * @var Block[]
     */
    protected $formBlock = [];

    /**
     * The data we will put into the form, indexed by segment ('' for default).
     *
     * @var array
     */
    protected $formData = [];

    /**
     * Error messages to show on the form, indexed by segment ('' for default).
     *
     * @var array
     */
    protected $formErrors;

    /**
     * Counter used to assign HTML identifiers
     * @var int
     */
    static protected $htmlId = 0;

    /**
     * The form definitions.
     * @var LinkedForm[]
     */
    protected $boundForms = [];

    /**
     * Default class mappings in our cheap DI implementation.
     *
     * @var array [function => class]
     */
    private static $diWiringStatic = [
        'Access' => Access\NullAccess::class,
        'Form' => Form::class,
        'Render' => 'Abivia\\NextForm\\Render\\Bootstrap4',
        'Schema' => Schema::class,
    ];

    /**
     * Instance specific class mappings for DI, initialized in __construct().
     *
     * @var array [function => class]
     */
    private $diWiring;

    /**
     * Maps form names to form bindings
     * @var array
     */
    protected $nameMap;

    /**
     * A list of all bindings connected to property (indexed by segment/name).
     * @var array
     */
    protected $objectMap;

    /**
     * The form and associated data after generation.
     * @var Block
     */
    protected $pageBlock;

    /**
     * Data schemas associated with the form.
     * @var SchemaCollection
     */
    protected $schemas;

    /**
     * The segment name to drop from form names, when segment naming mode = auto.
     * @var string
     */
    protected $segmentNameDrop;

    /**
     * How segment naming is handled: 'on' = always generate segment name;
     * 'off' = Never generate segment name; 'auto' = suppress the segment listed
     * in $segmentNameDrop.
     *
     * @var string
     */
    protected $segmentNameMode = 'auto';

    /**
     * A translation service.
     * @var Translator
     */
    protected $translator;

    /**
     * Create a new NextForm.
     *
     * @param array $options See options()
     */
    public function __construct($options = [])
    {
        $this->diWiring = self::$diWiringStatic;
        $this->setOptions($options);
        $this->access = $this->diMake('Access');
        $this->schemas = new SchemaCollection();
        $this->show = '';
    }

    /**
     * Add a form definition to the form manager.
     *
     * @param Form|string $form The name of a form file or a loaded Form.
     * @param array $options Form configuration options.
     * @return \Abivia\NextForm\LinkedForm
     */
    public function addForm($form, $options = []) : LinkedForm
    {
        if (is_string($form)) {
            $form = $this->diWiring['Form']::fromFile($form);
        }
        $formName = $form->getName();
        $this->boundForms[$formName] = new LinkedForm($form, $options);
        return $this->boundForms[$formName];
    }

    /**
     * Add a schema definition to the form manager.
     *
     * @param Schema|string $schema The name of a schema file or a loaded Schema.
     * @return $this
     */
    public function addSchema($schema)
    {
        if (is_string($schema)) {
            $schema = $this->diWiring['Schema']::fromFile($schema);
        }
        $this->schemas->addSchema($schema);
        if ($this->segmentNameMode === 'auto' && $this->segmentNameDrop === null) {
            $segments = $schema->getSegmentNames();
            if (!empty($segments)) {
                $this->segmentNameDrop = $segments[0];
            }
        }

        return $this;
    }

    /**
     * Create bindings for elements in each form and map to the schemas.
     * @return $this
     */
    public function bind()
    {
        if (empty($this->boundForms)) {
            throw new \RuntimeException('No forms available.');
        }
        $this->objectMap = [];
        foreach ($this->boundForms as $boundForm) {
            $boundForm->bind($this);
        }

        return $this;
    }

    /**
     * Reset the static context
     */
    static public function boot()
    {
        self::$htmlId = 0;
        self::$csrfToken = null;
    }

    /**
     * Connect a binding to the schema and add to the object map.
     *
     * @param Binding $binding
     * @return $this
     */
    public function connectBinding(Binding $binding)
    {
        $objectRef = $binding->bindSchema($this->schemas);
        if ($objectRef !== null) {
            if (!isset($this->objectMap[$objectRef])) {
                $this->objectMap[$objectRef] = [];
            }
            $this->objectMap[$objectRef][] = $binding;
        }
        return $this;
    }

    protected function diMake($service, ...$args) {
        $handler = $this->diWiring[$service];
        if ($handler === null) {
            $object = null;
        } elseif (is_callable($handler)) {
            $object = $handler(...$args);
        } else {
            $object = new $handler(...$args);
        }
        return $object;
    }

    /**
     * Generate the forms.
     * @param Form|string $oneForm Optional: a form to generate.
     * @param array $options Optional form settings.
     * @return Block
     */
    public function generate($oneForm = null, $options = []) : Block
    {
        if ($oneForm !== null) {
            $this->addForm($oneForm, $options);
        }
        $this->bind($this);

        $this->populateForms();

        if ($this->access === null) {
            $this->access = $this->diMake('Access');
        }

        $renderer = $this->diMake('Render', $options);
        $renderer->setShow($this->show);

        $this->pageBlock = new Block();
        foreach ($this->boundForms as $boundForm) {
            $formBlock = $boundForm->generate(
                $renderer,
                $this->access,
                $this->translator
            );
            $this->pageBlock->merge($formBlock);
        }
        return $this->pageBlock;
    }

    /**
     * Generate a new CSRF token.
     *
     * @return array [token name, token value]
     */
    static public function generateCsrfToken() {
        if (is_callable(self::$csrfGenerator)) {
            self::$csrfToken = call_user_func(self::$csrfGenerator);
        } else {
            self::generateNfToken();
        }
        return self::$csrfToken;
    }

    /**
     * Native random token generator.
     *
     * @return array ['_nf_token', random token value]
     */
    static protected function generateNfToken() {
        self::$csrfToken = ['_nf_token', \bin2hex(random_bytes(32))];
        return self::$csrfToken;
    }

    /**
     * Get the current CSRF token.
     *
     * @return array [token name, token value]
     */
    static public function getCsrfToken() {
        if (self::$csrfToken === null) {
            self::generateCsrfToken();
        }
        return self::$csrfToken;
    }

    /**
     * Get the page data, optionally for a specific form.
     *
     * @param string|null $formName
     * @return ?Block
     */
    public function getBlock($formName = null) : ?Block
    {
        if ($formName !== null) {
            $form = $this->getLinkedForm($formName);
            $block = $form ? $form->getBlock() : null;
        } else {
            $block = $this->pageBlock;
        }
        return $block;
    }

    /**
     * Get the rendered body of all forms or a single form.
     *
     * @param string $formName
     * @return ?string
     */
    public function getBody($formName = null) {
        $block = $this->getBlock($formName);
        return $block ? $block->body : null;
    }

    /**
     * Get all the data objects from the forms.
     * @return Binding[] Data bindings indexed by object name
     */
    public function getData()
    {
        $data = [];
        // The first element should have the value... there should only be one value.
        foreach ($this->objectMap as $objectName => $list) {
            $data[$objectName] = $list[0]->getValue();
        }
        return $data;
    }

    /**
     * Get the rendered head section of all forms or a single form.
     *
     * @param string $formName
     * @return ?string
     */
    public function getHead($formName = null) {
        $block = $this->getBlock($formName);
        return $block ? $block->head : null;
    }

    /**
     * Retrieve a linked form by name.
     *
     * @param string $formName
     * @return ?LinkedForm
     */
    public function getLinkedForm($formName) : ?LinkedForm
    {
        return $this->boundForms[$formName] ?? null;
    }

    /**
     * Get the file links for all forms or a single form.
     *
     * @param string $formName
     * @return ?string
     */
    public function getLinks($formName = null) {
        $block = $this->getBlock($formName);
        return $block ? \implode("\n", $block->linkedFiles) : null;
    }

    /**
     * Get the script for all forms or a single form.
     *
     * @param string $formName
     * @return ?string
     */
    public function getScript($formName = null) {
        $block = $this->getBlock($formName);
        return $block ? $block->script : null;
    }

    /**
     * Get the script files for all forms or a single form.
     *
     * @param string $formName
     * @return ?string
     */
    public function getScriptFiles($formName = null) {
        $block = $this->getBlock($formName);
        return $block ? implode("\n", $block->scriptFiles) : null;
    }

    /**
     * Get all the data objects in the specified segment from the form.
     * @param type $segment
     * @return array Data bindings indexed by object name
     */
    public function getSegmentData($segment)
    {
        $prefix = $segment . NextForm::SEGMENT_DELIM;
        $prefixLen = \strlen($segment . NextForm::SEGMENT_DELIM);
        $data = [];
        // The first element should have the value... there should only be one value.
        foreach ($this->objectMap as $objectName => $list) {
            if (\substr($objectName, 0, $prefixLen) == $prefix) {
                $data[substr($objectName, $prefixLen)] = $list[0]->getValue();
            }
        }
        return $data;
    }

    /**
     * Get the name of a segment that bound forms can drop when generating
     * form names.
     *
     * @return mixed
     */
    public function getSegmentNameDrop()
    {
        return $this->segmentNameMode !== 'off' ? $this->segmentNameDrop : null;
    }

    /**
     * Get the inline styles for all forms or a single form.
     *
     * @param string $formName
     * @return ?string
     */
    public function getStyles($formName = null) {
        $block = $this->getBlock($formName);
        return $block ? $block->styles : null;
    }

    /**
     * Turn a string into a valid HTML identifier, or make one up
     * @param string $name
     * @return string
     */
    static public function htmlIdentifier($name = '', $appendId = false)
    {
        if ($name == '') {
            $name = 'nf_' . ++self::$htmlId;
        } else {
            if ($appendId) {
                $name .= '_' . ++self::$htmlId;
            }
            // Turn anything risky into a dash
            // @todo escape the set of valid but odd characters.
            $name = \preg_replace('/[^a-z0-9\_]/i', '_', $name);
            // If the first character isn't alpha, prefix with nf-
            $name = \preg_replace('/^[^a-z]/i', 'nf_\1', $name);
        }
        return $name;
    }

    /**
     *
     * @param type $segment
     * @param type $field
     * @return string
     */
    protected function normalizeField($segment, $field) {
        if ($segment === '') {
            if (
                !isset($this->objectMap[$field])
                && $this->segmentNameDrop !== null
            ) {
                $field = $this->segmentNameDrop
                    . NextForm::SEGMENT_DELIM . $field;
            }
        } else {
            $field = $segment . NextForm::SEGMENT_DELIM . $field;
        }
        return $field;
    }

    /**
     * Set values for form data.
     *
     * @param array $data Values indexed by name ([segment/]field).
     * @param string $segment Optional segment name prefix.
     * @return $this
     */
    public function populate($data, $segment = '')
    {
        $this->formData[$segment] = $data;
        return $this;
    }

    /**
     * Set validation errors for form data.
     *
     * @param array $errors Messages indexed by name ([segment/]field).
     * @param string $segment Optional segment name prefix.
     * @return $this
     */
    public function populateErrors($errors, $segment = '')
    {
        $this->formErrors[$segment] = $errors;
        return $this;
    }

    /**
     * Populate form bindings.
     *
     * @return $this
     */
    protected function populateForms()
    {
        $this->populateFormData();
        $this->populateFormValidity($this->formErrors, false);
    }

    /**
     * Populate form bindings.
     *
     * @return $this
     */
    protected function populateFormData()
    {
        foreach ($this->formData as $segment => $data) {
            foreach ($data as $field => $value) {
                $field = $this->normalizeField($segment, $field);
                if (!isset($this->objectMap[$field])) {
                    continue;
                }
                foreach ($this->objectMap[$field] as $binding) {
                    $binding->setValue($value);
                }
            }
        }
        return $this;
    }

    /**
     * Populate form bindings with validation results.
     *
     * @return $this
     */
    protected function populateFormValidity($list, $valid)
    {
        if ($list === null) {
            return $this;
        }
        $label = $valid ? 'accept' : 'error';
        foreach ($list as $segment => $data) {
            foreach ($data as $field => $text) {
                $field = $this->normalizeField($segment, $field);
                if (!isset($this->objectMap[$field])) {
                    continue;
                }
                foreach ($this->objectMap[$field] as $binding) {
                    $binding->setLabel($label, $text);
                    $binding->setValid($valid);
                }
            }
        }
        return $this;
    }

    /**
     * Set the access control object.
     *
     * @param AccessInterface $access
     * @return $this
     */
    public function setAccess(AccessInterface $access)
    {
        $this->access = $access;
        return $this;
    }

    /**
     * Set a custom CSRF token generator.
     *
     * @param Callable $gen Must return an array of [token name, token value].
     */
    static public function setCsrfGenerator(Callable $gen) {
        self::$csrfGenerator = $gen;
        self::$csrfToken = null;
    }

    public function setOptions($options = []) {
        if (
            isset($options['segmentNameMode'])
            && in_array($options['segmentNameMode'], ['auto', 'off', 'on'])
        ) {
            $this->segmentNameMode = $options['segmentNameMode'];
        }
        if (isset($options['segmentNameDrop'])) {
            $this->segmentNameDrop = $options['segmentNameDrop'];
        }
        $this->wireInstance($options['wire'] ?? []);
    }

    /**
     * Set the translation object.
     *
     * @param Translator $translator
     * @return $this
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Define the current user.
     *
     * @param mixed $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->access->setUser($user);
    }

    /**
     * Set static class diWiring.
     * @param array $services Array of [service => handler].
     * @throws RuntimeException
     */
    static function wire($services)
    {
        foreach ($services as $service => $handler) {
            self::wireCheck($service);
            self::$diWiringStatic[$service] = $handler;
        }
    }

    /**
     * Set diWiring service name.
     * @param string $service A service name.
     * @throws RuntimeException
     */
    static protected function wireCheck($service)
    {
        if (!array_key_exists($service, self::$diWiringStatic)) {
            throw new \RuntimeException(
                "Service $service must be one of "
                . implode(', ', array_keys(self::$diWiringStatic))
            );
        }

    }

    /**
     * Set diWiring for this instance.
     *
     * @param array $services Array of [service => className].
     * @throws RuntimeException
     */
    protected function wireInstance($services)
    {
        foreach ($services as $service => $className) {
            self::wireCheck($service);
            $this->diWiring[$service] = $className;
        }
    }

}
