<?php

/**
 * PHPUnit's JSON comparison tool is inadequate, so we use this.
 */
trait JsonComparison {

    protected function jsonCompare($l, $r, $path = '') {
        if (is_array($l)) {
            if (!is_array($r)) {
                return false;
            }
            $tr = $r;
            foreach ($l as $key => $value) {
                $subPath = $path . '/'. $key;
                if (!isset($r[$key]) && !($l[$key] === null)) {
                    echo 'Missing element ' . $subPath . "\n";
                    return false;
                }
                if (!$this -> jsonCompare($l[$key], $tr[$key], $subPath)) {
                    echo 'Failed at ' . $path . "\n";
                    return false;
                }
                unset($tr[$key]);
            }
            if (!empty($tr)) {
                echo 'Extra elements at ' . $path . ' ' . implode(',', array_keys($tr)) . "\n";
                return false;
            }
        } elseif (is_object($l)) {
            if (!is_object($r)) {
                return false;
            }
            $tr = clone $r;
            foreach ($l as $prop => $value) {
                $subPath = $path . '/'. $prop;
                if (!isset($tr -> $prop) && !($l -> $prop === null)) {
                    echo 'Missing property ' . $subPath . "\n";
                    return false;
                }
                if (!$this -> jsonCompare($l -> $prop, $tr -> $prop, $subPath)) {
                    echo 'Failed at ' . $path . "\n";
                    return false;
                }
                unset($tr -> $prop);
            }
            $tr = (array)$tr;
            if (!empty($tr)) {
                echo 'Extra elements at ' . $path . ' ' . implode(',', array_keys($tr)) . "\n";
                return false;
            }
        } else {
            if ($l !== $r) {
                echo 'Value mismatch at ' . $path . "\n";
                return false;
            }
            return true;
        }
        return true;
    }

}
