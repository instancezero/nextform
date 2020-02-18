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
    public const SEGMENT_DELIM = '/';

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
     * The data we will put into the form, indexed by segment ('' for default)
     * @var array
     */
    protected $formData = [];

    /**
     * Counter used to assign HTML identifiers
     * @var int
     */
    static protected $htmlId = 0;

    /**
     * The form definitions.
     * @var LinkedForm[]
     */
    protected $linkedForms = [];

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
     * The form rendering engine.
     * @var RenderInterface
     */
    protected $renderer;

    /**
     * Data schemas associated with the form.
     * @var SchemaCollection
     */
    protected $schemas;

    /**
     * A translation service.
     * @var Translator
     */
    protected $translator;

    public function __construct()
    {
        $this->access = new Access\NullAccess();
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
            $form = Form::fromFile($form);
        }
        $formName = $form->getName();
        $this->linkedForms[$formName] = new LinkedForm($form, $options);
        return $this->linkedForms[$formName];
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
            $schema = Schema::fromFile($schema);
        }
        $this->schemas->addSchema($schema);

        return $this;
    }

    /**
     * Create bindings for elements in each form and map to the schemas.
     * @return $this
     */
    public function bind()
    {
        if (empty($this->linkedForms)) {
            throw new \RuntimeException('No forms available.');
        }
        $this->objectMap = [];
        foreach ($this->linkedForms as $linkedForm) {
            $linkedForm->bind($this);
        }

        return $this;
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

    /**
     * Reset the static context
     */
    static public function boot()
    {
        self::$htmlId = 0;
        self::generateCsrfToken();
    }

    /**
     * Generate the forms.
     * @return Block
     */
    public function generate() : Block
    {
        $this->bind($this);

        $this->populateForms();

        $this->renderer->setShow($this->show);

        $this->pageBlock = new Block();
        foreach ($this->linkedForms as $linkedForm) {
            $formBlock = $linkedForm->generate(
                $this->renderer,
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
        return self::$csrfToken;
    }

    /**
     * Get all the data objects from the form.
     * @return array Data bindings indexed by object name
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
     * Retrieve a linked form by name.
     *
     * @param string $formName
     * @return ?LinkedForm
     */
    public function getLinkedForm($formName) : ?LinkedForm
    {
        return $this->linkedForms[$formName] ?: null;
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

    protected function options($options)
    {
        return $this;
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
     * Populate form bindings.
     *
     * @return $this
     */
    protected function populateForms()
    {
        foreach ($this->formData as $segment => $data) {
            foreach ($data as $field => $value) {
                if ($segment !== '') {
                    $field = $segment . NextForm::SEGMENT_DELIM . $field;
                }
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
        self::generateCsrfToken();
    }

    /**
     * Set the form renderer.
     *
     * @param RenderInterface $renderer
     * @return $this
     */
    public function setRender(RenderInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
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

}
