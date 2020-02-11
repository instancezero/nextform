<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Traits\JsonEncoderTrait;

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
            self::$jsonEncodeMethod['elements'] = [];
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
     * Preprocess any shorthand elements.
     *
     * @param stdClass $config
     */
    protected function expandElements(&$config) {
        // Any elements that are simply strings are converted to basic field objects
        if (isset($config->elements) && is_array($config->elements)) {
            foreach ($config->elements as &$value) {
                if (is_string($value)) {
                    // Convert to a useful class
                    $value = Form::expandString($value);
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
     * Get the elements in this container.
     * @return Element[]
     */
    public function getElements()
    {
        return $this->elements;
    }

}