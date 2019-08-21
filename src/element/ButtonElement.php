<?php

namespace Abivia\NextForm\Element;

/**
 *
 */
class ButtonElement Extends NamedElement {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $function = 'button';
    static protected $jsonEncodeMethod = [];
    static protected $jsonLocalMethod = [
        'function' => ['drop:false'],
    ];
    static protected $validFunctions = ['button', 'reset', 'submit'];

    public function __construct() {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = array_merge(parent::$jsonEncodeMethod, self::$jsonLocalMethod);
        }
        $this -> type = 'button';
    }

    protected function configureClassMap($property, $value) {
        return parent::configureClassMap($property, $value);
    }

    protected function configureComplete() {
        return parent::configureComplete();
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize() {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
            $this -> form -> registerElement($this);
        }
    }

    protected function configurePropertyIgnore($property) {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property) {
        return parent::configurePropertyMap($property);
    }

    protected function configureValidate($property, &$value) {
        if ($property === 'function') {
            if (!in_array($value, self::$validFunctions)) {
                $this -> configureLogError(
                    $property . ' must be one of ' . implode(',', self::$validFunctions) . '.'
                );
                return false;
            }
            return true;
        }
        return parent::configureValidate($property, $value);
    }

    public function getFunction() {
        return $this -> function;
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