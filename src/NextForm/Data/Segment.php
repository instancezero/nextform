<?php

namespace Abivia\NextForm\Data;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 *  Describes a collection of Properties that come from the same source, for example
 *  columns are the properties that come from a database table. The table represents
 *  a segment.
 */
class Segment implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'name' => [],
        'primary' => ['drop:empty', 'scalarize'],
        'properties' => ['map:objects','array'],
    ];

    /**
     * The name of this segment
     * @var string
     */
    protected $name;

    /**
     * A list of the properties in this segment.
     * @var Property[]
     */
    protected $properties;

    /**
     * Objects that constitute a primary key for the segment.
     * @var string[]
     */
    protected $primary = [];

    /**
     * Check that all elements in a list of property names are defined.
     * @param string[] $keyList List of property names.
     * @return string Name of the first missing property or empty if no errors.
     */
    protected function checkPrimary($keyList)
    {
        foreach ($keyList as $propName) {
            if (!isset($this->properties[$propName])) {
                return $propName;
            }
        }
        return '';
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
            'properties' => [
                'className' => '\Abivia\NextForm\Data\Property',
                'key' => 'getName',
                'keyIsMethod' => true
            ],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    protected function configureComplete()
    {
        // Make sure the properties listed in primary exist.
        $badPropName = $this->checkPrimary($this->primary);
        if ($badPropName !== '') {
            $this->configureLogError(
                $badPropName . ' does not exist but is named as a primary key in segment '
                . $this->name
            );
            return false;
        }
        return true;
    }

    /**
     * Rename "objects" in the configure file to "properties" in this class.
     * @param string $property
     * @return string
     */
    protected function configurePropertyMap($property): string
    {
        if ($property === 'objects') {
            return 'properties';
        }
        return $property;
    }

    /**
     * Convert string as a primary key into a single valued array.
     * @param string $property The property name.
     * @param mixed $value The current property value.
     * @return type
     */
    protected function configureValidate($property, &$value)
    {
        if ($property == 'primary' && !\is_array($value)) {
            $value = [$value];
        }
        return $value;
    }

    /**
     * Get the name of this segment.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the list of properties contributing to a primary key.
     * @return string[]
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * Get a property by name.
     * @param string $propName Name of the property to fetch.
     * @return Property|null
     */
    public function getProperty($propName) : ?Property
    {
        return isset($this->properties[$propName])
            ? $this->properties[$propName] : null;
    }

    /**
     * Set the name of this segment.
     * @param string $name Name for the segment.
     * @return self
     */
    public function setName($name) :self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the primary key properties for this segment.
     * @param string|array $keyList
     * @return $this
     * @throws \RuntimeException
     */
    public function setPrimary($keyList)
    {
        // Check the format of the values, converting string to array.
        $keyList = $this->configureValidate('primary', $keyList);

        // Make sure all the properties are defined.
        $badPropName = $this->checkPrimary($keyList);
        if ($badPropName !== '') {
            throw new \RuntimeException(
                $badPropName . ' is not a valid primary key in segment'
                . $this->name . '.'
            );
        }
        $this->primary = $keyList;
        return $this;
    }

    /**
     * Set/replace a property.
     * @param Property $prop The property to add/replace.
     * @return $this
     * @throws \RuntimeException
     */
    public function setProperty(Property $prop)
    {
        $propName = $prop->getName();
        if ($propName === null || $propName === '') {
            throw new \RuntimeException(
                'Cannot assign unnamed property to segment '
                . $this->name . '.'
            );
        }
        $this->properties[$propName] = $prop;
        return $this;
    }

}
