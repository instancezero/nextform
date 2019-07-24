<?php

namespace Abivia\NextForm\Data;

/**
 * Describes the storage characteristics of an object.
 */
class Store implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    static protected $jsonEncodeMethod = [
        'type' => [],
        'size' => [],
    ];
    static protected $knownTypes = ['blob', 'date', 'decimal', 'float', 'int', 'string', 'text'];
    protected $size;
    protected $type;

    protected function configureValidate($property, &$value) {
        switch ($property) {
            case 'type':
                if (!($result = in_array($value, self::$knownTypes))) {
                    $this -> configureLogError(
                        'Invalid value "'. $value . '" for property "' . $property . '".'
                    );
                }
                break;
            default:
                $result = true;
        }
        return $result;
    }

    public function getSize() {
        return (string) $this -> size;
    }

    public function getType() {
        return $this -> type;
    }

}
