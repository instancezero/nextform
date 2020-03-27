<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\Form\Element\Element;
use Abivia\NextForm\LinkedForm;
use Abivia\NextForm\NextForm;
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
        'CaptchaElement' => 'SimpleBinding',
        'CellElement' => 'ContainerBinding',
        'FieldElement' => 'FieldBinding',
        'HtmlElement' => 'SimpleBinding',
        'SectionElement' => 'ContainerBinding',
        'StaticElement' => 'SimpleBinding',
    ];

    /**
     * The form the element in this binding is on.
     * @var Form
     */
    protected $form;

    /**
     * Name of this binding on the rendered form.
     * @var string
     */
    protected $nameOnForm;

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
     *
     * @var LinkedForm
     */
    protected $boundForm;

    /**
     * The current translation object.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Validation status (tri-state: true, false, or null)
     * @var ?bool
     */
    protected $valid;

    /**
     * The current value for the bound element.
     * @var string
     */
    protected $value;

    public function __construct()
    {

    }

    /**
     * Connect data elements in the schemas. Only useful for FieldBindings.
     *
     * @param \Abivia\NextForm\Data\SchemaCollection $schemas
     * @return null
     * @codeCoverageIgnore
     */
    public function bindSchema(?\Abivia\NextForm\Data\SchemaCollection $schemas)
    {
        return null;
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
        } elseif($access === null) {
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
     * @return Binding
     */
    public function copy()
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
     * Create an appropriate bindings for the passed Element and any
     * contained elements.
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
        ?AccessInterface $access = null,
        $options = []
    ) : Block {
        $options = $this->checkAccess(
            $access, '', $this->element->getName(), $options
        );
        $pageData = $renderer->render($this, $options);
        return $pageData;
    }

    /**
     * Get the bound form for this binding.
     *
     * @return LinkedForm $boundForm
     */
    public function getLinkedForm() : LinkedForm
    {
        return $this->boundForm;
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
     * Get this element's form.
     *
     * @return Form
     */
    public function getForm() : ?Form
    {
        return $this->form;
    }

    /**
     * Get this element's name on the form. If not assigned, a name is generated.
     *
     * @param $baseOnly If set, brackets are omitted. Only useful with
     *                  FieldBindings.
     * @return string
     */
    public function getNameOnForm($baseOnly = false)
    {
        if ($this->nameOnForm === null) {
            $name = $this->element->getName();
            if ($name != '') {
                $this->nameOnForm = $name;
            } else {
                if ($this->autoId == '') {
                    $this->getId();
                }
                $this->nameOnForm = $this->autoId;
            }
        }
        return $this->nameOnForm;
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
            $this->autoId = NextForm::htmlIdentifier($this->element->getType(), true);
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
     * Get the name of a bound object, null when not a FieldBinding.
     *
     * @return ?string
     */
    public function getObject() : ?string
    {
        return null;
    }

    /**
     * Get the current validation state.
     * @return ?bool
     */
    public function getValid() : ?bool
    {
        return $this->valid;
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
     * Connect this binding to a linked form
     * @param LinkedForm $boundForm
     * @return $this
     */
    public function setLinkedForm(LinkedForm $boundForm) :self
    {
        $this->boundForm = $boundForm;
        return $this;
    }

    /**
     * Set the element for this binding.
     *
     * @param Element $element
     * @return $this
     */
    public function setElement(Element $element)
    {
        $this->element = $element;
        $this->form = $element->getForm();
        $this->id = $element->getId();
        $this->labels = $element->getLabels() ?? new Labels();
        $this->labelsTranslated = clone $this->labels;
        return $this;
    }

    /**
     * Set the form ID for this binding.
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Assign or override the current name of this binding on a form.
     * @param string $name
     * @return $this
     */
    public function setNameOnForm($name)
    {
        $this->nameOnForm = $name;
        return $this;
    }

    /**
     * Set the value for a label.
     *
     * @param string $labelName Name of the text to be set.
     * @param string|null $text
     * @param bool $asConfirm When set, set the "confirm" version.
     * @return $this
     */
    public function setLabel($labelName, $text, $asConfirm = false)
    {
        if ($this->labels === null) {
            $this->labels = new Labels();
        }
        $this->labels->set($labelName, $text, $asConfirm);
        $this->labelsTranslated = $this->labels->translate($this->translator);
        return $this;
    }

    /**
     * Set new labels.
     *
     * @param Labels $labels The new labels.
     * @return $this
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * Set the validation state
     * @param mixed $state True, false, or null for indeterminate.
     * @return $this
     */
    public function setValid($state)
    {
        $this->valid = $state;
        return $this;
    }

    /**
     * Set the current value for the bound element.
     * @param mixed $value The new value.
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Translate the texts in this element.
     * @param Translator $translator
     * @return $this
     */
    public function translate(?Translator $translator = null) : Binding
    {
        $this->translator = $translator;
        $this->labelsTranslated = $this->labels->translate($translator);
        return $this;
    }

}