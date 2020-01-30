<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\Manager;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\Form\Element\Element;
use Abivia\NextForm\Render\Block;
use DeepCopy\DeepCopy;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\PropertyNameMatcher;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Things that get rendered
 */
class Binding
{
    /**
     * System-assigned element ID
     * @var string
     */
    protected $autoId = '';

    /**
     * The source element
     * @var Element
     */
    protected $element;

    /**
     * Mapping of element classes to binding classes when a specialized class
     * is needed.
     *
     * @var type
     */
    static protected $elementMap = [
        'CellElement' => 'ContainerBinding',
        'FieldElement' => 'FieldBinding',
        'HtmlElement' => 'SimpleBinding',
        'SectionElement' => 'ContainerBinding',
        'StaticElement' => 'SimpleBinding',
    ];

    /**
     * Name on the rendered form.
     * @var string
     */
    protected $formName;

    /**
     * User-specified element id, overrides auto ID
     * @var string
     */
    protected $id = '';

    /**
     * Text labels for this binding.
     *
     * @var Labels
     */
    protected $labels;

    /**
     * Text labels after translation.
     *
     * @var Labels
     */
    protected $labelsTranslated;

    /**
     * The form this element belongs to
     * @var \Abivia\NextForm\Manager
     */
    protected $manager;

    /**
     * The current translation object.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * The current value for the bound element.
     * @var string
     */
    protected $value;

    public function __construct()
    {

    }

    /**
     * Connect data elements in the schemas
     * @param \Abivia\NextForm\Data\SchemaCollection $schemas
     * @codeCoverageIgnore
     */
    public function bindSchema(?\Abivia\NextForm\Data\SchemaCollection $schemas)
    {
        if ($this->manager) {
            $this->manager->registerBinding($this);
        }
    }

    /**
     * Check the access level for this binding.
     *
     * @param AccessInterface $access Any access control object.
     * @param string $segment The data segment to check, empty for a form name.
     * @param string $objectName The data object name or form name.
     * @param array $options Options, accessOverride is relevant
     * @return array Options with access, accessOverride elements set.
     */
    protected function checkAccess(
        AccessInterface $access,
        $segment,
        $objectName,
        $options
    ) {
        if (isset($options['accessOverride'])) {
            $level = $options['accessOverride'];
        } elseif ($segment === '' && $objectName === '') {
            // Objects with no name are writable.
            $level = 'write';
        } else {
            $level = 'none';
            if ($access->allows($segment, $objectName, 'write')) {
                $level = 'write';
            } elseif ($access->allows($segment, $objectName, 'view')) {
                $level = 'view';
            } elseif ($access->allows($segment, $objectName, 'hide')) {
                $level = 'hide';
            }
            if ($level != 'write') {
                // Anything less than write access overrides contained elements.
                $options['accessOverride'] = $level;
            }
        }
        $options['access'] = $level;
        return $options;
    }

    /**
     * Make a copy of this element, cloning/preserving selected properties
     * @return \self
     */
    public function copy() : self
    {
        static $cloner = null;

        if ($cloner === null) {
            $cloner = new DeepCopy();
            // Don't copy the form ID
            $cloner->addFilter(
                new SetNullFilter(),
                new PropertyNameMatcher('\Abivia\NextForm\Form\Binding\Binding', 'autoId')
            );
            // Don't clone the linked data
            $cloner->addFilter(
                new KeepFilter(),
                new PropertyNameMatcher('\Abivia\NextForm\Form\Binding\Binding', 'dataProperty')
            );
        }
        return $cloner->copy($this);
    }

    /**
     * Create an appropriate binding for the passed Element
     *
     * @param \Abivia\NextForm\Form\Binding\Element $element
     * @return \Abivia\NextForm\Form\Binding\Binding
     */
    static public function fromElement(Element $element) : Binding
    {
        $classPath = get_class($element);
        $classParts = explode('\\', $classPath);
        $elementClass = array_pop($classParts);
        if (isset(self::$elementMap[$elementClass])) {
            $bindingClass = __NAMESPACE__ . '\\' . self::$elementMap[$elementClass];
        } else {
            $bindingClass = __NAMESPACE__ . '\\Binding';
        }
        $binding = new $bindingClass;
        $binding->setElement($element);
        if ($binding instanceof ContainerBinding) {
            foreach ($element->getElements() as $subElement) {
                $binding->addBinding(Binding::fromElement($subElement));
            }
        }

        return $binding;
    }

    /**
     * Use a renderer to turn this element into part of the form.
     *
     * @param RenderInterface $renderer Any Render object.
     * @param AccessInterface $access Any access control object.
     * @param array $options Options: accessOverride to override default access.
     * @return Block
     */
    public function generate(
        RenderInterface $renderer,
        AccessInterface $access,
        $options = []
    ) : Block {
        $options = $this->checkAccess(
            $access, '', $this->element->getName(), $options
        );
        $pageData = $renderer->render($this, $options);
        return $pageData;
    }

    /**
     * Get this binding's element.
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Get this element's name on the form. If not assigned, a name is generated.
     *
     * @param $baseOnly If set, brackets are omitted. Only useful with
     *                  FieldBindings.
     * @return string
     */
    public function getFormName($baseOnly = false)
    {
        if ($this->formName === null) {
            $name = $this->element->getName();
            if ($name != '') {
                $this->formName = $name;
            } else {
                if ($this->autoId == '') {
                    $this->getId();
                }
                $this->formName = $this->autoId;
            }
        }
        return $this->formName;
    }

    /**
     * Get the form ID for this element.
     * @return string
     */
    public function getId()
    {
        if ($this->id !== '') {
            return $this->id;
        }
        if ($this->autoId === '') {
            $this->autoId = Manager::htmlIdentifier($this->element->getType(), true);
        }
        return $this->autoId;
    }

    /**
     * Get native or translated scope-resolved labels for this binding.
     * @param bool $translated
     * @return \Abivia\NextForm\Data\Labels
     */
    public function getLabels($translated = false) : Labels
    {
        if ($translated) {
            if ($this->labels === null) {
                $this->labelsTranslated = new Labels();
            }
            return $this->labelsTranslated;
        } elseif ($this->labels === null) {
            $this->labels = new Labels();
            $this->labelsTranslated = new Labels();
        }
        return $this->labels;
    }

    /**
     * Get the form manager.
     *
     * @return ?Manager
     */
    public function getManager() : ?Manager
    {
        return $this->manager;
    }

    /**
     * Get the name of a bound object, null when not a FieldBinding.
     *
     * @return ?string
     */
    public function getObject() : ?string
    {
        return null;
    }

    /**
     * Get the current value for the bound element.
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the element for this binding.
     *
     * @param Element $element
     * @return \self
     */
    public function setElement(Element $element) : self
    {
        $this->element = $element;
        $this->id = $element->getId();
        $this->labels = $element->getLabels() ?? new Labels();
        $this->labelsTranslated = clone $this->labels;
        return $this;
    }

    /**
     * Assign or override the current name of this binding on a form.
     * @param string $name
     * @return $this
     */
    public function setFormName($name)
    {
        $this->formName = $name;
        return $this;
    }

    /**
     * Set the form ID for this binding.
     * @param string $id
     * @return \self
     */
    public function setId($id) : self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the value for a label.
     * @param string $labelName Name of the text to be set.
     * @param string $text
     */
    public function setLabel($labelName, $text)
    {
        if ($this->labels === null) {
            $this->labels = new Labels();
        }
        $this->labels->set($labelName, $text);
        $this->labelsTranslated = $this->labels->translate($this->translator);
    }

    /**
     * Connect this binding to a Manager
     * @param Manager $manager
     * @return $this
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Set the current value for the bound element.
     * @param mixed $value The new value.
     * @return \self
     */
    public function setValue($value) : self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Translate the texts in this element.
     * @param Translator $translator
     * @return $this
     */
    public function translate(Translator $translator = null) : Binding
    {
        $this->translator = $translator;
        $this->labelsTranslated = $this->labels->translate($translator);
        return $this;
    }

}