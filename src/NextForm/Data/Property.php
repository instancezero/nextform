<?php

namespace Abivia\NextForm\Data;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Data\Population;
use Abivia\NextForm\Data\Presentation;
use Abivia\NextForm\Data\Validation;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * A Property describes a data object in the schema; objects are contained
 * in Segments.
 */
class Property implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Description of what this property is / is for.
     * @var string
     */
    protected $description;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'name' => [],
        'description' => ['drop:null'],
        'labels' => ['drop:empty', 'drop:null'],
        'population' => ['drop:empty', 'drop:null'],
        'presentation' => ['drop:empty', 'drop:null'],
        'store' => ['drop:empty', 'drop:null'],
        'validation' => ['drop:empty', 'drop:null'],
    ];

    /**
     * Text associated with an element
     * @var \Abivia\NextForm\Data\Labels
     */
    protected $labels;

    /**
     * A list of form bindings that use this property
     * @var Binding[]
     */
    protected $linkedBindings = [];

    /**
     * The segment-unique name of this property.
     * @var string
     */
    protected $name;

    /**
     * Description of the data contained in this property.
     * @var Population
     */
    protected $population;

    /**
     * Description on how this property should be presented on a form.
     * @var Presentation
     */
    protected $presentation;

    /**
     * Definition of where the data is stored.
     * @var Store
     */
    protected $store;

    /**
     * Rules used to validate values for this property.
     * @var Validation
     */
    protected $validation;

    /**
     * Initialize a new Property.
     */
    public function __construct()
    {
        $this->labels = new Labels();
    }

    /**
     * Map a property to a class.
     * @param string $property The current class property name.
     * @param mixed $value The value to be stored in the property, made available for inspection.
     * @return mixed An object containing a class name and key, or false
     * @codeCoverageIgnore
     */
    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'population' => ['className' => '\Abivia\NextForm\Data\Population'],
            'presentation' => ['className' => '\Abivia\NextForm\Data\Presentation'],
            'store' => ['className' => '\Abivia\NextForm\Data\Store'],
            'labels' => ['className' => '\Abivia\NextForm\Data\Labels'],
            'validation' => ['className' => '\Abivia\NextForm\Data\Validation'],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    /**
     * Ensure that we have a label object after configuration is completed
     * @return boolean
     */
    protected function configureComplete()
    {
        if ($this->labels === null) {
            $this->labels = new Labels();
        }
        return true;
    }

    /**
     * Get the description of this Property.
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the schema-defined labels
     * @return \Abivia\NextForm\Data\Labels
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Get the property name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the property's population object.
     * @return Population
     */
    public function getPopulation() : Population {
        if ($this->population === null) {
            $this->population = new Population;
        }
        return $this->population;
    }

    /**
     * Get the property's presentation object.
     * @return Presentation
     */
    public function getPresentation() : Presentation
    {
        if ($this->presentation === null) {
            $this->presentation = new Presentation;
        }
        return $this->presentation;
    }

    /**
     * Get the property's validation object.
     * @return Validation
     */
    public function getValidation() : Validation
    {
        if ($this->validation === null) {
            $this->validation = new Validation;
        }
        return $this->validation;
    }

/**
     * Get the property's data store object.
     * @return \Abivia\NextForm\Data\Store
     */
    public function getStore() : ?\Abivia\NextForm\Data\Store
    {
        return $this->store;
    }

    /**
     * Connect a form element to this property.
     * @param Binding $binding The binding to be connected.
     * @return $this
     */
    public function linkBinding(Binding $binding)
    {
        if (!in_array($binding, $this->linkedBindings)) {
            $this->linkedBindings[] = $binding;
        }
        return $this;
    }

    /**
     * Set the description for this property.
     * @param string $text Descriptive text.
     * @return $this
     */
    public function setDescription($text)
    {
        $this->description = $text;
        return $this;
    }

    /**
     * Set the display texts for this Property.
     * @param Labels $labels Object containing displayable labels.
     * @return $this
     */
    public function setLabels(Labels $labels)
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * Set the Property name.
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * set the property's population object.
     * @param Population $population
     */
    public function setPopulation(Population $population)
    {
        $this->population = $population;
        return $this;
    }

    /**
     * Set the property's presentation object.
     * @param Presentation $presentation Presentation settings.
     * @return $this
     */
    public function setPresentation(Presentation $presentation)
    {
        $this->presentation = $presentation;
        return $this;
    }

    /**
     * Set the property's validation object.
     * @param Validation $validation Validation rules.
     * @return $this
     */
    public function setValidation(Validation $validation)
    {
        $this->validation = $validation;
        return $this;
    }

}
