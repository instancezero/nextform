<?php

namespace Abivia\NextForm;

use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface as RenderInterface;
use Abivia\NextForm\Data\Schema;
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
class Manager
{

    public const GROUP_DELIM = ':';
    public const SEGMENT_DELIM = '/';

    /**
     * The access controller
     * @var AccessInterface
     */
    protected $access;

    /**
     * A list of all bindings in the form.
     * @var Binding[]
     */
    protected $allBindings = [];

    /**
     * A list of top level bindings in the form.
     * @var Binding[]
     */
    protected $bindings = [];

    /**
     * The form definition.
     * @var Form
     */
    protected $form;

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
    protected $renderer;
    protected $schema;
    protected $schemaIsLinked = false;
    protected $translator;
    protected $useSegment = '';

    public function __construct()
    {
        $this->access = new Access\NullAccess();
        $this->show = '';
    }

    protected function assignNames()
    {
        $this->nameMap = [];
        $containerCount = 0;
        foreach ($this->allBindings as $binding) {
            if ($binding instanceof FieldBinding) {
                $baseName = str_replace('/', '_', $binding->getObject());
                $name = $baseName;
                $confirmName = $baseName . '_confirm';
                $append = 0;
                while (isset($this->nameMap[$name]) || isset($this->nameMap[$confirmName])) {
                    $name = $baseName . '_' . ++$append;
                    $confirmName = $name . '_' . $append . '_confirm';
                }
                $this->nameMap[$name] = $binding;
                $binding->setFormName($name);
            } elseif ($binding instanceof ContainerBinding) {
                $baseName = 'container_';
                $name = $baseName;
                while (isset($this->nameMap[$name])) {
                    $name = $baseName . ++$containerCount;
                }
                $this->nameMap[$name] = $binding;
                $binding->setFormName($name);
            }
        }
        $this->schemaIsLinked = true;
        return $this;
    }

    /**
     * Connect all the components into something we can generate
     * @return \self
     */
    public function bind(Form $form = null, Schema $schema = null) : self
    {
        if ($this->schemaIsLinked) {
            return $this;
        }
        if ($form !== null) {
            $this->setForm($form);
        }
        if ($schema !== null) {
            $this->setSchema($schema);
        }
        if ($this->form === null) {
            throw new RuntimeException('Unable to bind to a null form.');
        }
        $this->objectMap = [];
        $this->allBindings = [];
        $this->bindings = [];
        foreach ($this->form->getElements() as $element) {
            $binding = Binding::fromElement($element);
            $binding->setManager($this);
            $binding->bindSchema($this->schema);
            $this->bindings[] = $binding;
        }
        $this->schemaIsLinked = true;
        return $this;
    }

    /**
     * Reset the static context
     */
    static public function boot()
    {
        self::$htmlId = 0;
    }

    /**
     * Generate a form.
     * @param array $options Generation options, optional unless stated otherwise:
     *  $options = [
     *      'attributes' => (Render\Attributes) Attributes to be added to the form element.
     *      'id' => The HTML id for the form. If not provided, one is generated.
     *              May also be passed through in 'attributes'.
     *      'name' => The HTML name for the form. If not provided, the id is used.
     *              May also be passed through in 'attributes'.
     *      'token' => (string) Form submission token. If provided and empty, no token is used.
     *      'tokenName' => (string) Name/ID to use for the token. Default is "nf_token".
     *  ]
     * @return Block
     */
    public function generate($options)
    {
        $this->options($options);
        $this->bind();

        // Make sure we have attributes
        if (!isset($options['attributes'])) {
            $options['attributes'] = new Attributes();
        }
        $attrs = $options['attributes'];

        // If we were passed an ID, clean it up and add to attributes
        if (isset($options['id'])) {
            $attrs->set('id', $options['id']);
        }

        // Pick up the ID or auto-generate one
        if ($attrs->has('id')) {
            $this->id = $attrs->get('id');
        } else {
            $this->id = Manager::htmlIdentifier('form', true);
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

        //Pass the ID to the form
        $options['id'] = $this->id;

        // Assign field names
        $this->assignNames();
        $this->renderer->setShow($this->show);

        // Run the translations.
        foreach ($this->allBindings as $binding) {
            $binding->translate($this->translator);
        }

        // Start the form, write all the bindings, close the form, return.
        $pageData = $this->renderer->start($options);
        foreach ($this->bindings as $binding) {
            $pageData->merge(
                $binding->generate($this->renderer, $this->access)
            );
        }
        $pageData->close();
        return $pageData;
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

    public function getSegment()
    {
        return $this->form->getSegment();
    }

    /**
     * Get all the data objects in the specified segment from the form.
     * @param type $segment
     * @return array Data bindings indexed by object name
     */
    public function getSegmentData($segment)
    {
        $prefix = $segment . Manager::SEGMENT_DELIM;
        $prefixLen = \strlen($segment . Manager::SEGMENT_DELIM);
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
        if (!$this->schemaIsLinked) {
            throw new \LogicException('Form not linked to schema.');
        }
        foreach ($data as $field => $value) {
            if ($segment !== '') {
                $field = $segment . Manager::SEGMENT_DELIM . $field;
            }
            if (!isset($this->objectMap[$field])) {
                continue;
            }
            foreach ($this->objectMap[$field] as $element) {
                $element->setValue($value);
            }
        }
        return $this;
    }

    /**
     * Add a binding to the all bindings list and the object map.
     * @param Binding $binding
     * @return $this
     */
    public function registerBinding(Binding $binding) : self
    {
        if (!in_array($binding, $this->allBindings, true)) {
            $this->allBindings[] = $binding;
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

    public function setForm(Form $form) : self
    {
        $this->form = $form;
        $this->schemaIsLinked = false;
        return $this;
    }

    public function setRender(RenderInterface $renderer) : self
    {
        $this->renderer = $renderer;
        return $this;
    }

    public function setSchema(Schema $schema) : self
    {
        $this->schema = $schema;
        $this->schemaIsLinked = false;
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
