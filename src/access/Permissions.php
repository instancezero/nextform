<?php

namespace Abivia\NextForm\Access;

/**
 * A simple permission for the BasicAccess class.
 */
class Permissions {

    protected $rules = [];

    public function addRule($rule, $flag) {
        $parts = $this -> parseRule($rule);
        if (!isset($this -> rules[$parts[0]])) {
            $this -> rules[$parts[0]] = [];
        }
        $this -> rules[$parts[0]][$parts[1]] = $flag;
    }

    public function configure($value, $strict = false) {
        try {
            foreach ($value as $rule => $flag) {
                $this -> addRule($rule, $flag);
            }
        } catch (\DomainException $ex) {
            if ($strict) {
                return false;
            }
        }
        return true;
    }

    public function has($object, $operation = null) {
        if ($operation === null) {
            list($object, $operation) = $this -> parseRule($object);
        }
        if (!isset($this -> rules[$object]) || !isset($this -> rules[$object][$operation])) {
            return null;
        }
        return $this -> rules[$object][$operation];
    }

    protected function parseRule($rule) {
        if (false === $lastPos = strrpos($rule, '.')) {
            throw new \DomainException('Rule must end with a period followed by an operation name.');
        }
        $object = substr($rule, 0, $lastPos);
        $operation = substr($rule, $lastPos + 1);
        return [$object, $operation];
    }

}
