<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm;
use Abivia\NextForm\Form\Trigger\Trigger;

/**
 *
 */
class FieldElement extends Element {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\JsonEncoder;

    /**
     * A data property in the form's schema.
     * @var \Abivia\NextForm\Data\Property
     */
    protected $dataProperty;
    static protected $jsonEncodeMethod = [];
    static protected $jsonLocalMethod = [
        'labels' => ['drop:null'],
        'object' => ['method:removeScope'],
        'triggers' => ['drop:empty', 'drop:null'],
    ];
    protected $labels;
    /**
     * The name of an associated schema object
     * @var string
     */
    protected $object;
    protected $triggers = [];

    public function __construct() {
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = array_merge(parent::$parentJsonEncodeMethod, self::$jsonLocalMethod);
        }
        $this -> type = 'field';
    }

    protected function configureClassMap($property, $value) {
        static $classMap = [
            'triggers' => ['className' => Trigger::class],
        ];
        $result = false;
        if (isset($classMap[$property])) {
            $result = (object) $classMap[$property];
        }
        return $result;
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize() {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
        }
    }

    protected function configurePropertyIgnore($property) {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property) {
        return parent::configurePropertyMap($property);
    }

    /**
     * Make sure the object property has a scope.
     * @param string $property
     * @param mixed $value
     * @return boolean
     */
    protected function configureValidate($property, &$value) {
        if ($property == 'object') {
            if ($value === '') {
                return true;
            }
            if (strpos($value, NextForm::SEGMENT_DELIM) === false && $this -> form) {
                $value = $this -> form -> getSegment()
                    . NextForm::SEGMENT_DELIM . $value;
            }
        } elseif (in_array($property, array_keys(self::$jsonLocalMethod))) {
            return true;
        }
        return parent::configureValidate($property, $value);
    }

    /**
     * Get the connected schema object, if any
     * @return \Abivia\NextForm\Data\Property
     */
    public function getDataProperty() {
        return $this -> dataProperty;
    }

    public function getLabels() {
        if ($this -> dataProperty) {
            $labels = $this -> dataProperty -> getLabels() -> merge($this -> labels);
        } else {
            $labels = $this -> labels;
        }
        return $labels;
    }

    public function getObject() {
        return $this -> object;
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     */
    public function linkSchema($schema) {
        $this -> dataProperty = $schema -> getProperty($this -> object);
        if ($this -> dataProperty) {
            $this -> form -> registerObject($this);
        }
    }

    protected function removeScope($value) {
        if (!$this -> form) {
            return $value;
        }
        $segment = $this -> form -> getSegment();
        if ($segment !== '') {
            if (strpos($value, $segment . NextForm::SEGMENT_DELIM) === 0) {
                $value = substr($value, strlen($segment) +1);
            }
        }
        return $value;
    }

}