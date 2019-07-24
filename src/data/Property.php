<?php

namespace Abivia\NextForm\Data;
use \Abivia\NextForm\Data\Labels;

/**
 * A Property describes a data object in the schema; objects are contained in Segments.
 */
class Property implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;
    use \Abivia\NextForm\Traits\JsonLabelFolder;

    protected $description;
    static protected $jsonEncodeMethod = [
        'name' => [],
        'description' => ['drop:null'],
        'labels' => ['method:jsonLabelFold', 'drop:null'],
        'population' => ['drop:null'],
        'presentation' => ['drop:null'],
        'store' => ['drop:null'],
        'validation' => ['drop:null'],
    ];
    /**
     * Text associated with an element
     * @var \Abivia\NextForm\Data\Labels
     */
    protected $labels;
    protected $name;
    protected $population;
    protected $presentation;
    protected $store;
    protected $validation;

    /**
     * Map a property to a class.
     * @param string $property The current class property name.
     * @param mixed $value The value to be stored in the property, made available for inspection.
     * @return mixed An object containing a class name and key, or false
     * @codeCoverageIgnore
     */
    protected function configureClassMap($property, $value) {
        static $classMap = [
            'population' => ['className' => '\Abivia\NextForm\Data\Population'],
            'presentation' => ['className' => '\Abivia\NextForm\Data\Presentation'],
            'store' => ['className' => '\Abivia\NextForm\Data\Store'],
            'labels' => ['className' => '\Abivia\NextForm\Data\Labels'],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    /**
     * Get the schema-defined labels
     * @return \Abivia\NextForm\Data\Labels
     */
    public function getLabels() {
        return $this -> labels;
    }

    public function getName() {
        return $this -> name;
    }

    public function getPresentation() : \Abivia\NextForm\Data\Presentation {
        if ($this -> presentation === null) {
            return new \Abivia\NextForm\Data\Presentation;
        }
        return $this -> presentation;
    }

    public function getPopulation() : \Abivia\NextForm\Data\Population {
        if ($this -> population === null) {
            return new \Abivia\NextForm\Data\Population;
        }
        return $this -> population;
    }

    public function setName($name) {
        $this -> name = $name;
        return $this;
    }

}
