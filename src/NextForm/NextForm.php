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
use Abivia\NextForm\Render\Attributes;
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
     * A list of all bindings in each form.
     * @var Binding[]
     */
    protected $allBindings = [];

    /**
     * A list of top level bindings in each form.
     * @var Binding[]
     */
    protected $bindings = [];

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
     * The form definitions.
     * @var Form[]
     */
    protected $forms;

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

    protected $id;
    protected $name;
    /**
     * Maps form names to form bindings
     * @var array
     */
    protected $nameMap;
    /**
     * Maps schema objects to form bindings
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
     * @var Schema[]
     */
    protected $schemas;

    // This should not be required after reorg.
    protected $schemasLinked = false;

    /**
     * A translation service.
     * @var Translator
     */
    protected $translator;

    public function __construct()
    {
        $this->access = new Access\NullAccess();
        $this->show = '';
    }

    public function addForm(Form $form, $options = []) : self
    {
        $formName = $form->getName();
        $this->forms[$formName] = $form;
        //$this->forms[$formName] = new LinkedForm($form, $options);
        return $this;
    }

    public function addSchema(Schema $schema) : self
    {
        if ($this->schemas === null) {
            $this->schemas = new SchemaCollection();
        }
        $this->schemas->addSchema($schema);
        $this->schemasLinked = false;
        return $this;
    }

    protected function assignNames()
    {
        $this->nameMap = [];
        $containerCount = 1;

        foreach ($this->allBindings as $bindings) {
            foreach ($bindings as $binding) {
                if ($binding instanceof FieldBinding) {
                    $baseName = str_replace('/', '_', $binding->getObject());
                    $name = $baseName;
                    $confirmName = $baseName . self::CONFIRM_LABEL;
                    $append = 0;
                    while (
                        isset($this->nameMap[$name])
                        || isset($this->nameMap[$confirmName])
                    ) {
                        $name = $baseName . '_' . ++$append;
                        $confirmName = $name . '_' . $append . self::CONFIRM_LABEL;
                    }
                    $this->nameMap[$name] = $binding;
                    $binding->setNameOnForm($name);
                } elseif ($binding instanceof ContainerBinding) {
                    $baseName = 'container_';
                    $name = $baseName . $containerCount;
                    while (isset($this->nameMap[$name])) {
                        $name = $baseName . ++$containerCount;
                    }
                    $this->nameMap[$name] = $binding;
                    $binding->setNameOnForm($name);
                }
            }
        }
        $this->schemasLinked = true;
        return $this;
    }

    /**
     * Connect all the components into something we can generate
     * @return \self
     */
    public function bind() : self
    {
        if (empty($this->forms)) {
            throw new \RuntimeException('No forms have been provided.');
        }
        if ($this->schemasLinked) {
            return $this;
        }
        $this->objectMap = [];
        $this->allBindings = [];
        $this->bindings = [];
        foreach ($this->forms as $form) {
            $formName = $form->getName();
            $this->allBindings[$formName] = [];
            $this->bindings[$formName] = [];
            foreach ($form->getElements() as $element) {
                $binding = Binding::fromElement($element);
                $binding->setManager($this);
                $binding->bindSchema($this->schemas);
                $this->bindings[$formName][] = $binding;
            }
        }

        $this->schemasLinked = true;
        foreach ($this->formData as $segment => $data) {
            foreach ($data as $field => $value) {
                if ($segment !== '') {
                    $field = $segment . NextForm::SEGMENT_DELIM . $field;
                }
                if (!isset($this->objectMap[$field])) {
                    continue;
                }
                foreach ($this->objectMap[$field] as $element) {
                    $element->setValue($value);
                }
            }
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
     * @param array $options Generation options, optional unless stated otherwise:
     *  $options = [
     *      'attributes' => (Render\Attributes) Attributes to be added to the form element.
     *      'id' => The HTML id for the form. If not provided, one is generated.
     *              May also be passed through in 'attributes'.
     *      'name' => The HTML name for the form. If not provided, the id is used.
     *              May also be passed through in 'attributes'.
     *  ]
     * @return Block
     */
    public function generate($options) : Block
    {
        $this->options($options);
        $this->bind();

        // Make sure we have attributes
        if (!isset($options['attributes'])) {
            $options['attributes'] = new Attributes();
        }
        $attrs = &$options['attributes'];

        // If we were passed an ID, clean it up and add to attributes
        if (isset($options['id'])) {
            $attrs->set('id', $options['id']);
        }

        // Pick up the ID or auto-generate one
        if ($attrs->has('id')) {
            $this->id = $attrs->get('id');
        } else {
            $this->id = NextForm::htmlIdentifier('form', true);
            $attrs->set('id', $this->id);
        }

        // If we have been passed a name, use it
        if (isset($options['name'])) {
            $this->name = $options['name'];
            $attrs->set('name', $this->name);
        }

        // If there is no name, use the ID
        if (!$attrs->has('name')) {
            $this->name = $this->id;
            $attrs->set('name', $this->id);
        }

        // Pass the ID to the form
        $options['id'] = $this->id;

        // Assign field names
        $this->assignNames();
        $this->renderer->setShow($this->show);

        // Run the translations.
        foreach ($this->allBindings as $bindings) {
            foreach ($bindings as $binding) {
                $binding->translate($this->translator);
            }
        }

        $this->pageBlock = new Block();
        foreach ($this->forms as $formName => $form) {
            // Start the form, write all the bindings, close the form, return.
            $formBlock = $this->renderer->start($options);
            foreach ($this->bindings[$formName] as $binding) {
                $formBlock->merge(
                    $binding->generate($this->renderer, $this->access)
                );
            }
            $this->formBlock[$formName] = $formBlock->close();
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
     * @return array ['_nf_token, random token value]
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

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSegment($formName)
    {
        return $this->forms[$formName]->getSegment();
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
     * Populate form bindings.
     *
     * @param array $data Values indexed by schema object ID.
     * @param string $segment Optional segment prefix.
     * @throws LogicException
     * @return $this
     */
    public function populate($data, $segment = '') : self
    {
        $this->formData[$segment] = $data;
        return $this;
    }

    /**
     * Add a binding to the all bindings list and the object map.
     * @param Binding $binding
     * @return $this
     */
    public function registerBinding(Binding $binding) : self
    {
        try {
            $nameOnForm = $binding->getForm()->getName();
        } catch (Error $err) {
            throw new RuntimeException("Attempt to use an element with no form.");
        }
        if (!isset($this->allBindings[$nameOnForm])) {
            $this->allBindings[$nameOnForm] = [];
        }
        if (!in_array($binding, $this->allBindings[$nameOnForm], true)) {
            $this->allBindings[$nameOnForm][] = $binding;
        }
        $objectRef = $binding->getObject();
        if ($objectRef !== null) {
            if (!isset($this->objectMap[$objectRef])) {
                $this->objectMap[$objectRef] = [];
            }
            $this->objectMap[$objectRef][] = $binding;
        }
        return $this;
    }

    public function setAccess(AccessInterface $access) : self
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

    public function setRender(RenderInterface $renderer) : self
    {
        $this->renderer = $renderer;
        return $this;
    }

    public function setTranslator(Translator $translator) : self
    {
        $this->translator = $translator;

        return $this;
    }

    public function setUser($user) : self
    {
        $this->access->setUser($user);
    }

}
