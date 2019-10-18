<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Element\Element;
use Abivia\NextForm\Renderer\Block;
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
     * The form this element belongs to
     * @var \Abivia\NextForm
     */
    protected $form;

    /**
     * Name on the rendered form.
     * @var string
     */
    protected $formName;

    /**
     * Set if there's a translation. This might go away as we revise translation handling.
     * @var bool
     */
    protected $hasTranslation;

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
     * The current value for the bound element.
     * @var string
     */
    protected $value;

    public function __construct()
    {

    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     * @codeCoverageIgnore
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema)
    {
        // Non-data elements do nothing. This just simplifies walking the tree
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

        return $binding;

    }

    /**
     * Use a renderer to turn this element into part of the form.
     * @param RendererInterface $renderer Any Renderer object.
     * @param AccessInterface $access Any access control object
     * @param Translator $translate Any translation object.
     * @return Block
     */
    public function generate(RendererInterface $renderer, AccessInterface $access, Translator $translate) : Block
    {
        $this->translate($translate);
        //$readOnly = false; // $access->hasAccess(...)
        $options = ['access' => 'write'];
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
     * @return string
     */
    public function getFormName()
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
        if ($translated && $this->hasTranslation) {
            return $this->labelsTranslated;
        } elseif ($this->labels === null) {
            $this->labels = new Labels();
        }
        return $this->labels;
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
        $this->labels = $element->getLabels();
        return $this;
    }

    /**
     * Connect this binding to a form
     * @param NextForm $form
     * @return $this
     */
    public function setForm(NextForm $form)
    {
        $this->form = $form;
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
     * @param Translator $translate
     * @return \self
     */
    public function translate(Translator $translate) : self
    {
        // Stuff here
        return $this;
    }

}