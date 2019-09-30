<?php

/**
 * PHPUnit's JSON comparison tool is inadequate, so we use this.
 */
trait JsonComparison {

    protected function jsonCompare($leftSide, $rightSide, $path = '') {
        if (is_array($leftSide)) {
            if (!is_array($rightSide)) {
                echo "\n" . 'jsonCompare: ' . gettype($leftSide)
                    . ' to ' . gettype($rightSide)
                    . ' comparison at ' . $path . "\n";
                return false;
            }
            $testRight = $rightSide;
            foreach ($leftSide as $key => $value) {
                $subPath = $path . '/'. $key;
                if (!isset($rightSide[$key]) && $leftSide[$key] !== null) {
                    echo "\n" . 'Missing element on right ' . $subPath
                        . ' (left = ' . $leftSide[$key] . ")\n";
                    return false;
                }
                if (!$this -> jsonCompare($leftSide[$key], $testRight[$key], $subPath)) {
                    return false;
                }
                unset($testRight[$key]);
            }
            if (!empty($testRight)) {
                echo "\n" . 'Extra elements at ' . $path . ' ' . implode(',', array_keys($testRight)) . "\n";
                return false;
            }
        } elseif (is_object($leftSide)) {
            if (!is_object($rightSide)) {
                echo "\n" . 'jsonCompare: ' . gettype($leftSide)
                    . ' to ' . gettype($rightSide)
                    . ' comparison at ' . $path . "\n";
                return false;
            }
            $testRight = clone $rightSide;
            foreach ($leftSide as $prop => $value) {
                $subPath = $path . '/'. $prop;
                if (!isset($testRight -> $prop) && $leftSide -> $prop !== null) {
                    echo "\n" . 'Missing property on right ' . $subPath
                        . ' (left = ' . $leftSide -> $prop . ")\n";
                    return false;
                }
                if (isset($testRight -> $prop)) {
                    if (!$this -> jsonCompare($leftSide -> $prop, $testRight -> $prop, $subPath)) {
                        return false;
                    }
                }
                unset($testRight -> $prop);
            }
            $testRight = (array)$testRight;
            if (!empty($testRight)) {
                echo "\n" . 'Extra elements at ' . $path . ' ' . implode(',', array_keys($testRight)) . "\n";
                return false;
            }
        } else {
            if ($leftSide !== $rightSide) {
                echo "\n" . 'Value mismatch at ' . $path . "\n";
                return false;
            }
            return true;
        }
        return true;
    }

}
