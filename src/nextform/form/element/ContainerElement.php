<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Form\Element\FieldElement;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Traits\JsonEncoderTrait;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Class for any element that contains a list of sub-elements.
 */
abstract class ContainerElement Extends NamedElement
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * The list of elements contained by this instance.
     * @var Element[]
     */
    protected $elements = [];

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Build JSON encoder rules on the first instantiation.
     */
    public function __construct()
    {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = parent::$jsonEncodeMethod;
            self::$jsonEncodeMethod['labels'] = ['drop:empty', 'drop:null'];
            self::$jsonEncodeMethod['elements'] = ['method:jsonCollapseElements'];
        }
    }

    abstract public function addElement(Element $element);

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema)
    {
        foreach ($this->elements as $element) {
            $element->bindSchema($schema);
        }
    }

    /**
     * Sub-elements have a procedural instantiation.
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    protected function configureClassMap($property, $value)
    {
        $result = false;
        if ($property == 'elements') {
            $result = new \stdClass;
            $result->key = [$this, 'addElement'];
            $result->className = [Element::class, 'classFromType'];
        } else {
            $result = parent::configureClassMap($property, $value);
        }
        return $result;
    }

    protected function configureComplete()
    {
        return parent::configureComplete();
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize(&$config)
    {
        if (isset($this->configureOptions['_form'])) {
            $this->form = $this->configureOptions['_form'];
            $this->form->registerElement($this);
        }
        // Any elements that are simply strings are converted to basic field objects
        if (isset($config->elements) && is_array($config->elements)) {
            foreach ($config->elements as &$value) {
                if (is_string($value)) {
                    // Convert to a useful class
                    $value = FieldElement::expandString($value);
                }
            }
        }
    }

    protected function configurePropertyIgnore($property)
    {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property)
    {
        return parent::configurePropertyMap($property);
    }

    protected function configureValidate($property, &$value)
    {
        return parent::configureValidate($property, $value);
    }

    /**
     * Use a renderer to turn this element into part of the form.
     * @param RendererInterface $renderer Any Renderer object.
     * @param AccessInterface $access Any access control object
     * @param Translator $translator Any translation object.
     * @return Block
     */
    public function generate(
        RendererInterface $renderer,
        AccessInterface $access,
        Translator $translator
    ) : Block {
        $this->translate($translator);
        $options = false; // $access->hasAccess(...)
        $options = ['access' => 'write'];
        $containerData = $renderer->render($this, $options);
        foreach ($this->elements as $element) {
            $containerData->merge($element->generate($renderer, $access, $translator));
        }
        $containerData->close();
        return $containerData;
    }

    /**
     * Get the elements in this container.
     * @return Element[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * See if any of the contained elements can be represented as a shorthand string.
     * @param array $elementList
     */
    protected function jsonCollapseElements($elementList)
    {
        foreach ($elementList as &$element) {
            $element = $element->jsonCollapse();
        }
        return $elementList;
    }

}