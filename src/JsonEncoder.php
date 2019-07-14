<?php
namespace Abivia\NextForm;

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
            $drop = false;
            $toProp = $prop;
            foreach ($encoding as $option) {
                if ($option == 'array') {
                    $asArray = true;
                } elseif ($option == 'scalarize') {
                    $scalarize = true;
                } elseif (($cut = strpos($option, ':')) !== false) {
                    $cmd = substr($option, 0, $cut);
                    $arg = substr($option, $cut + 1);
                    if ($cmd == 'drop') {
                        switch ($arg) {
                            case 'blank':
                                $drop = $this -> $prop === '';
                                break;
                            case 'empty':
                                $drop = is_array($this -> $prop) && empty($this -> $prop);
                                break;
                            case 'false':
                                $drop = $this -> $prop === false;
                                break;
                            case 'null':
                                $drop = $this -> $prop === null;
                                break;
                            case 'true':
                                $drop = $this -> $prop === true;
                                break;
                        }
                        if ($drop) {
                            break;
                        }
                    } elseif ($cmd == 'map') {
                        $toProp = $arg;
                    } elseif ($cmd == 'method') {
                        $value = $this -> $arg($value);
                    }
                }
            }
            if ($drop) {
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

}
