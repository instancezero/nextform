<?php

namespace Abivia\NextForm\Data;

/**
 * Describes how a data object is displayed on a form.
 */
class Presentation implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $cols = 1;
    protected $confirm = false;
    static protected $jsonEncodeMethod = [
        'cols' => [],
        'confirm' => ['drop:false'],
        'type' => [],
    ];
    static protected $knownTypes = [
        'button', 'checkbox', 'color', 'date', 'datetime-local',
        'email', 'file', 'hidden', 'image', 'month', 'number',
        'password', 'radio', 'range', 'reset', 'search',
        'submit', 'tel', 'text', 'textarea', 'time', 'url', 'week',
        // Our non w3c types...
        'select',
    ];
    /*
        */
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

    public function getCols() {
        return $this -> cols;
    }

    public function getConfirm() {
        return $this -> confirm;
    }

    public function getType() {
        return $this -> type;
    }

    public function setConfirm($confirm) {
        $this -> confirm = $confirm;
        return $this;
    }

    public function setType($value) {
        if (!$this -> configureValidate('type', $value)) {
            if (is_scalar($value)) {
                $msg = '"' . $value . '" is not a valid presentation type.';
            } else {
                $msg = 'non-scalar value passed to setType()';
            }
            throw new \RuntimeException($msg);
        }
        $this -> type = $value;
        return $this;
    }

}
