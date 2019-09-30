<?php

namespace Abivia\NextForm\Data;

/**
 * Describes acceptable values for a property
 */
class Validation implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    /**
     * A list of file patterns for an input element of type file.
     * @var string[]
     */
    protected $accept = [];

    /**
     * Set when validation should be performed via a server request.
     * @var bool
     */
    protected $async = false;

    /**
     * Sets the source for image or video in an input element of type file.
     * @var string
     */
    protected $capture;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
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

    /**
     * The maximum acceptable input length.
     * @var int
     */
    protected $maxLength;

    /**
     * The maximum value for numeric input types.
     * @var float
     */
    protected $maxValue;

    /**
     * The minimum acceptable input length.
     * @var int
     */
    protected $minLength;

    /**
     * The minimum value for numeric input types.
     * @var flost
     */
    protected $minValue;

    /**
     * Set when a check box list or select can have multiple values.
     * @var bool
     */
    protected $multiple = false;

    /**
     * Regular expression with PHP start-end delimiters (but still Javascript compatible)
     * for validating inputs.
     * @var string
     */
    protected $pattern = '';

    /**
     * Set when a value must be supplied.
     * @var bool
     */
    protected $required = false;

    /**
     * The size of an increment for numeric/date input types.
     * @var float
     */
    protected $step;

    /**
     * Set when the validation regex should be translated before use.
     * @var bool
     */
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
            if ($property === 'maxLength' && $value <= 0) {
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
            if (is_scalar($value)) {
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
     * Get the named property, with the ability to trim the pattern regex by using -pattern.
     * @param string $property The name of the property to get.
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

    /**
     * Determine if this object contributes nothing to a JSON encoding.
     * @return bool
     */
    public function isEmpty() {
        if (!empty($this -> accept)) {
            return false;
        }
        if ($this -> async) {
            return false;
        }
        if ($this -> capture) {
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
