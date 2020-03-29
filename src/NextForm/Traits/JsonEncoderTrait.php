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
 * 'drop:empty', empty array properties or objects that return isEmpty() = true will be omitted
 * 'drop:false', false-valued properties will be omitted
 * 'drop:null', null-valued properties will be omitted
 * 'drop:true', true-valued properties will be omitted
 * 'map:toName', changes the property name to "toName"
 * 'method:mname', passes the property to the class method mname()
 * 'scalarize', converts a single element array to scalar.
 * 'order:n', weight for selecting the order in which elements are generated (default 0)
 *
 */
trait JsonEncoderTrait
{

    //static protected $jsonEncodeMethod = [];

    public function jsonCollapse()
    {
        return $this;
    }

    public function jsonSerialize()
    {
        $collapse = $this->jsonCollapse();
        if (\is_string($collapse)) {
            return $collapse;
        }
        $ordered = self::jsonSerializeSort();
        $result = new \stdClass;
        foreach ($ordered as $prop => $encoding) {
            // If we have a pseudo-property, use the property name.
            $value = property_exists($this, $prop) ? $this->$prop : $prop;
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
                    $keep = $this->jsonSerializeCommand($option, $cut, $toProp, $value);
                    if (!$keep) {
                        break;
                    }
                }
            }
            if (!$keep) {
                continue;
            }
            if ($asArray) {
                $result->$toProp = array_values($value);
            } else {
                $result->$toProp = $value;
            }
            if ($scalarize && is_array($result->$toProp) && count($result->$toProp) == 1) {
                $result->$toProp = $result->$toProp[0];
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
    protected function jsonSerializeCommand($option, $cut, &$prop, &$value)
    {
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
                        $drop = $value->isEmpty();
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
            $this->$arg($prop, $value);
        }
        return true;
    }

    /**
     * Sort the encoder rules by weight and name.
     */
    static protected function jsonSerializeSort() {
        // Build a list of [weight, property]
        $minWeight = PHP_INT_MIN;
        $sorter = [];
        foreach (self::$jsonEncodeMethod as $prop => $encoding) {
            if (!is_array($encoding)) {
                $encoding = explode(';', $encoding);
                self::$jsonEncodeMethod[$prop] = $encoding;
            }
            $weight = $minWeight;
            foreach ($encoding as $key => $option) {
                if (substr($option, 0, 6) === 'order:') {
                    $value = substr($option, 6);
                    if (is_numeric($value)) {
                        $weight = round($value);
                    }
                    unset($encoding[$key]);
                }
            }
            $minWeight = max($minWeight, $weight) + 1;
            $sorter[] = [$weight, $prop];
        }
        // Sort by weight, name
        usort($sorter, function ($l, $r) {
            if ($l[0] === $r[0]) {
                return strcmp($l[1], $r[1]);
            }
            return ($l[0] < $r[0]) ? -1 : 1;
        });

        // Create a new array in the resulting order
        $ordered = [];
        foreach ($sorter as $info) {
            $prop = $info[1];
            $ordered[$prop] = self::$jsonEncodeMethod[$prop];
        }

        // Return the ordered rules
        return $ordered;
    }

}
