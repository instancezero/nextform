<?php

namespace Abivia\NextForm\Data;

/**
 *  Describes a collection of Properties that come from the same source, for example
 *  columns are the properties that come from a database table. The table represents
 *  a segment.
 */
class Segment implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\JsonEncoder;

    static protected $jsonEncodeMethod = [
        'name' => [],
        'primary' => ['scalarize'],
        'objects' => 'array',
    ];
    protected $name;
    protected $objects;
    /**
     * Objects that constitute a primary key for the segment.
     * @var array
     */
    protected $primary;

    /**
     * Map a property to a class.
     * @param string $property The current class property name.
     * @param mixed $value The value to be stored in the property, made available for inspection.
     * @return mixed An object containing a class name and key, or false
     * @codeCoverageIgnore
     */
    protected function configureClassMap($property, $value) {
        static $classMap = [
            'objects' => ['className' => '\Abivia\NextForm\Data\Property', 'key' => 'getName', 'keyIsMethod' => true],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    protected function configureComplete() {
        // Make sure the objects listed in primary exist.
        return true;
    }

    protected function configureValidate($property, &$value) {
        if ($property == 'primary' && !is_array($value)) {
            $value = [$value];
        }
        return $value;
    }

    public function getName() {
        return $this -> name;
    }

    public function getProperty($propName) {
        return isset($this -> objects[$propName]) ? $this -> objects[$propName] : null;
    }

}
