<?php

namespace Abivia\NextForm\Data;

/**
 * Describes acceptable values for a property
 */
class Validation implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $accept = [];
    protected $async = false;
    protected $capture;
    static protected $jsonEncodeMethod = [
        'accept' => ['drop:empty'],
        'async' => ['drop:false'],
        'capture' => ['drop:null','drop:blank'],
        'maxLength' => ['drop:null'],
        'maxValue' => ['drop:null'],
        'minLength' => ['drop:null'],
        'minValue' => ['drop:null'],
        'multiple' => ['drop:false'],
        'pattern' => ['drop:null','drop:blank'],
        'required' => ['drop:false'],
        'step' => ['drop:null','drop:zero'],
        'translatePattern' => ['drop:false'],
    ];
    protected $maxLength;
    protected $maxValue;
    protected $minLength;
    protected $minValue;
    protected $multiple = false;
    protected $pattern = '';
    protected $required = false;
    protected $step;
    protected $translatePattern = false;

    protected function configureComplete() {
        if ($this -> maxValue < $this -> minValue) {
                $this -> configureLogError(
                    'Minimum value must be less than or equal to maximum value.'
                );
                return false;
        }
        return true;
    }

    protected function configureValidate($property, &$value) {
        if (in_array($property, ['async', 'multiple', 'required', 'translatePattern'])) {
            if (!is_bool($value)) {
                $this -> configureLogError($property . ' must be a boolean.');
                return false;
            }
        } elseif (in_array($property, ['maxLength', 'minLength'])) {
            if (!is_numeric($value)) {
                $this -> configureLogError($property . ' must be numeric.');
                return false;
            }
            $value = (int) $value;
            if (($property === 'maxLength' || $property === 'step') && $value <= 0) {
                $this -> configureLogError($property . ' must be a positive integer.');
                return false;
            }
            if ($property === 'minLength' && $value < 0) {
                $this -> configureLogError($property . ' must be a non-negative integer.');
                return false;
            }
        } elseif (in_array($property, ['maxValue', 'minValue', 'step'])) {
            if (!is_numeric($value) && strtotime($value) === false) {
                $this -> configureLogError($property . ' must be numeric or date.');
                return false;
            }
            if (is_numeric($value)) {
                $value = (float) $value;
            }
            if ($property === 'step' && $value <= 0) {
                $this -> configureLogError($property . ' must be a positive number.');
                return false;
            }
        } elseif (in_array($property, ['pattern'])) {
            if (!is_string($value)) {
                $this -> configureLogError($property . ' must be a string.');
                return false;
            }
            @preg_match($value, '');
            if (preg_last_error() !== 0) {
                $this -> configureLogError($property . ' must be a valid regular expression.');
                return false;
            }
        } elseif ($property === 'accept') {
            if (is_string($value)) {
                $value = explode(',', $value);
            }
            if (!is_array($value)) {
                $this -> configureLogError($property . ' must be an array or comma-delimited list.');
                return false;
            }
        } elseif ($property === 'capture') {
            if ($value !== null && !in_array($value, ['environment', 'user'])) {
                $this -> configureLogError($property . ' must be null, "user", or "environment".');
                return false;
            }
        }
        return true;
    }

    /**
     * Get the named property, with the ability to trim the pattern regex.
     * @param string $property
     * @return mixed
     * @throws \RuntimeException
     */
    public function get($property) {
        if ($property == '-pattern') {
            return $this -> pattern !== '' ? substr($this -> pattern, 1, -1) : '';
        }
        if (!isset(self::$jsonEncodeMethod[$property])) {
            throw new \RuntimeException($property . ' is not a recognized property.');
        }
        return $this -> $property;
    }

    public function isEmpty() {
        if (!empty($this -> accept)) {
            return false;
        }
        if ($this -> async) {
            return false;
        }
        if ($this -> maxLength != 0) {
            return false;
        }
        if ($this -> maxValue !== null) {
            return false;
        }
        if ($this -> minLength != 0) {
            return false;
        }
        if ($this -> minValue !== null) {
            return false;
        }
        if ($this -> multiple) {
            return false;
        }
        if ($this -> pattern != '') {
            return false;
        }
        if ($this -> required) {
            return false;
        }
        if ($this -> step != 0) {
            return false;
        }
        if ($this -> translatePattern) {
            return false;
        }
        return true;
    }

    /**
     * Set a property, validating while doing so
     * @param string $property The property name.
     * @param mixed $value The property value.
     * @return $this
     * @throws \RuntimeException If the property has an invalid value.
     */
    public function set($property, $value) {
        $this -> configureErrors = [];
        if (!$this -> configureValidate($property, $value)) {
            throw new \RuntimeException(implode("\n", $this -> configureErrors));
        }
        $this -> $property = $value;
        return $this;
    }

}
