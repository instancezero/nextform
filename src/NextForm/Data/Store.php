<?php

namespace Abivia\NextForm\Data;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Describes the storage characteristics of an object.
 */
class Store implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'type' => ['drop:null'],
        'size' => ['drop:null'],
    ];

    /**
     * A list of recognized storage types.
     * @var string[]
     */
    static protected $knownTypes = ['blob', 'date', 'decimal', 'float', 'int', 'string', 'text'];

    /**
     * The size in the data store. Can be non-numeric.
     * @var string
     */
    protected $size;

    /**
     * The storage type.
     * @var string
     */
    protected $type;

    /**
     * Check that the supplied type is valid.
     * @param string $property The property to be validated.
     * @param mixed $value Current value for the property.
     * @return boolean
     */
    protected function configureValidate($property, &$value)
    {
        $result = true;
        if ($property === 'type') {
            if (!($result = \in_array($value, self::$knownTypes))) {
                $this->configureLogError(
                    'Invalid value "'. $value . '" for property "' . $property . '".'
                );
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Get the current storage size.
     * @return string
     */
    public function getSize()
    {
        return (string) $this->size;
    }

    /**
     * Get the current data type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Determine if this object contributes nothing to a JSON encoding.
     * @return bool
     */
    public function isEmpty() : bool
    {
        if ($this->size !== null) {
            return false;
        }
        if ($this->type !== null && $this->type !== '') {
            return false;
        }
        return true;
    }

    /**
     * Set the storage size.
     * @param string $size The desired storage size.
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = (string) $size;
        return $this;
    }

    /**
     * Set the data type.
     * @param string $type The desired data type.
     * @return $this
     * @throws RuntimeException If the type is not valid.
     */
    public function setType($type)
    {
        if (!$this->configureValidate('type', $type)) {
            throw new \RuntimeException($type . ' is not a valid value for type.');
        }
        $this->type = $type;
        return $this;
    }

}
