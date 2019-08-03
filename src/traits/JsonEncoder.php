<?php
namespace Abivia\NextForm\Traits;

/**
 * Assist with JSON encoding protected properties
 *
 * Classes that use this trait and implement \JsonSerializable can define
 * $jsonEncodeMethod as a static array keyed by the property name with scalar or array
 * values. The scalar should be either 'array' or 'default' (or '').
 * An array can be used to specify:
 * 'array', strip keys from an associative array and return the array.
 * 'drop:empty', empty array properties will be omitted
 * 'drop:false', false-valued properties will be omitted
 * 'drop:null', null-valued properties will be omitted
 * 'drop:true', true-valued properties will be omitted
 * 'map:toName', changes the property name to "toName"
 * 'method:mname', passes the property to the class method mname()
 * 'scalarize', converts a single element array to scalar.
 *
 */
trait JsonEncoder {

    //static protected $jsonEncodeMethod = [];

    public function jsonSerialize() {
        $result = new \stdClass;
        foreach (self::$jsonEncodeMethod as $prop => $encoding) {
            if (!is_array($encoding)) {
                $encoding = [$encoding];
            }
            $value = $this -> $prop;
            $scalarize = false;
            $asArray = false;
            $keep = true;
            $toProp = $prop;
            foreach ($encoding as $option) {
                if ($option == 'array') {
                    $asArray = true;
                } elseif ($option == 'scalarize') {
                    $scalarize = true;
                } elseif (($cut = strpos($option, ':')) !== false) {
                    $keep = $this -> jsonSerializeCommand($option, $cut, $toProp, $value);
                    if (!$keep) {
                        break;
                    }
                }
            }
            if (!$keep) {
                continue;
            }
            if ($asArray) {
                $result -> $toProp = array_values($value);
            } else {
                $result -> $toProp = $value;
            }
            if ($scalarize && is_array($result -> $toProp) && count($result -> $toProp) == 1) {
                $result -> $toProp = $result -> $toProp[0];
            }
        }
        return $result;
    }

    /**
     * Process command style options
     * @param string $option The full option
     * @param int $cut Where the command-option break is located
     * @param string $prop The name of the property
     * @param mixed $value The value of the property
     * @return boolean Returns true if the value is part of the serialization.
     */
    protected function jsonSerializeCommand($option, $cut, &$prop, &$value) {
        $cmd = substr($option, 0, $cut);
        $arg = substr($option, $cut + 1);
        $drop = false;
        if ($cmd == 'drop') {
            switch ($arg) {
                case 'blank':
                    $drop = $value === '';
                    break;
                case 'empty':
                    if (is_array($value)) {
                        $drop = empty($value);
                    } elseif (is_object($value) && method_exists($value, 'isEmpty')) {
                        $drop = $value -> isEmpty();
                    }
                    break;
                case 'false':
                    $drop = $value === false;
                    break;
                case 'null':
                    $drop = $value === null;
                    break;
                case 'true':
                    $drop = $value === true;
                    break;
                case 'zero':
                    $drop = $value === 0;
                    break;
            }
            if ($drop) {
                return false;
            }
        } elseif ($cmd == 'map') {
            $prop = $arg;
        } elseif ($cmd == 'method') {
            $value = $this -> $arg($value);
        }
        return true;
    }

}
