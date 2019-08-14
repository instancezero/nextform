<?php

namespace Abivia\NextForm\Data;

/**
 * Describes how a data object is displayed on a form.
 */
class Presentation implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $cols;
    protected $confirm = false;
    static protected $jsonEncodeMethod = [
        'span' => [],
        'confirm' => ['drop:false'],
        'type' => [],
        'cols' => ['drop:null'],
        'rows' => ['drop:null'],
    ];
    static protected $knownTypes = [
        'button', 'checkbox', 'color', 'date', 'datetime-local',
        'email', 'file', 'hidden', 'image', 'month', 'number',
        'password', 'radio', 'range', 'reset', 'search', 'select',
        'submit', 'tel', 'text', 'textarea', 'time', 'url', 'week',
        // Non <input> element types...
        'select',
    ];
    protected $rows;
    protected $span = 1;
    protected $type;

    protected function configureValidate($property, &$value) {
        switch ($property) {
            case 'cols':
            case 'rows':
            case 'span':
                if (!is_numeric($value)) {
                    $this -> configureLogError($property . ' must be numeric.');
                    return false;
                }
                $value = (int) $value;
                break;
            case 'confirm':
                if (!is_bool($value)) {
                    $this -> configureLogError($property . ' must be boolean.');
                    return false;
                }
                break;
            case 'type':
                if (!(in_array($value, self::$knownTypes))) {
                    $this -> configureLogError(
                        'Invalid value "'. $value . '" for property "' . $property . '".'
                    );
                    return false;
                }
                break;
        }
        return true;
    }

    public function getCols() {
        return $this -> cols;
    }

    public function getConfirm() {
        return $this -> confirm;
    }

    public function getRows() {
        return $this -> rows;
    }

    public function getSpan() {
        return $this -> span;
    }

    public function getType() {
        return $this -> type;
    }

    public function setCols($cols) {
        $this -> configureErrors = [];
        if (!$this -> configureValidate('cols', $cols)) {
            throw new \RuntimeException(implode("\n", $this -> configureErrors));
        }
        $this -> cols = $cols;
        return $this;
    }

    public function setConfirm($confirm) {
        $this -> configureErrors = [];
        if (!$this -> configureValidate('confirm', $confirm)) {
            throw new \RuntimeException(implode("\n", $this -> configureErrors));
        }
        $this -> confirm = $confirm;
        return $this;
    }

    public function setRows($rows) {
        $this -> configureErrors = [];
        if (!$this -> configureValidate('rows', $rows)) {
            throw new \RuntimeException(implode("\n", $this -> configureErrors));
        }
        $this -> rows = $rows;
        return $this;
    }

    public function setSpan($span) {
        $this -> configureErrors = [];
        if (!$this -> configureValidate('span', $span)) {
            throw new \RuntimeException(implode("\n", $this -> configureErrors));
        }
        $this -> span = $span;
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
