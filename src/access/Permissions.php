<?php

namespace Abivia\NextForm\Access;

/**
 * A simple permission for the BasicAccess class.
 */
class Permissions
{

    /**
     * Permission flags.
     * @var bool[]
     */
    protected $rules = [];

    /**
     * Add a rule to the permission list.
     * @param string $rule The rule identifier ("object.operation").
     * @param bool $flag True if the permission is granted.
     * @return self
     */
    public function addRule($rule, $flag) : self
    {
        $parts = $this -> parseRule($rule);
        if (!isset($this -> rules[$parts[0]])) {
            $this -> rules[$parts[0]] = [];
        }
        $this -> rules[$parts[0]][$parts[1]] = $flag;
        return $this;
    }

    /**
     * Configure a list of rules.
     * @param bool[] $value A list of permissions indexed by rule identifiers ("object.operation").
     * @param bool $strict Strict validation. When set a malformed rule will abort configuration.
     * @return boolean
     */
    public function configure($value, $strict = false)
    {
        foreach ($value as $rule => $flag) {
            try {
                $this -> addRule($rule, $flag);
            } catch (\DomainException $ex) {
                if ($strict) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Delete a rule from the permission list.
     * @param string $rule The rule identifier ("object.operation").
     * @return self
     */
    public function deleteRule($rule) : self
    {
        $parts = $this -> parseRule($rule);
        if (isset($this -> rules[$parts[0]])) {
            unset($this -> rules[$parts[0]][$parts[1]]);
        }
        return $this;
    }

    /**
     * Determine if a permission exists.
     * @param string $object Either an object name followed by an operation name or
     * just a rule name.
     * @param string $operation The operation name, when not part of the $object.
     * @return ?bool Boolean if there is a permission by this name, null otherwise.
     */
    public function has($object, $operation = null) : ?bool
    {
        if ($operation === null) {
            list($object, $operation) = $this -> parseRule($object);
        }
        if (!isset($this -> rules[$object]) || !isset($this -> rules[$object][$operation])) {
            return null;
        }
        return $this -> rules[$object][$operation];
    }

    /**
     * Break a permission object into a rule name and an operation.
     * @param string $rule Rules are an object name, a period, and an operation name
     * @return string[] An array of [object, operation]
     * @throws \DomainException when the rule is malformed.
     */
    protected function parseRule($rule)
    {
        if (false === $lastPos = strrpos($rule, '.')) {
            throw new \DomainException('Rule must end with a period followed by an operation name.');
        }
        $object = substr($rule, 0, $lastPos);
        $operation = substr($rule, $lastPos + 1);
        return [$object, $operation];
    }

}
