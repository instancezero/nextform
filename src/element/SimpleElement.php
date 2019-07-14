<?php

namespace Abivia\NextForm\Form\Element;

/**
 *
 */
abstract class SimpleElement Extends Element {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\JsonEncoder;

    protected $value = '';
    static protected $jsonEncodeMethod = [];

    public function __construct() {
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = parent::$parentJsonEncodeMethod;
            self::$jsonEncodeMethod['value'] = '';
        }
    }

    public function getValue() {
        return $this -> value;
    }

    protected function configureClassMap($property, $value) {
        return parent::configureClassMap($property, $value);
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize() {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
        }
    }

    protected function configurePropertyIgnore($property) {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property) {
        return parent::configurePropertyMap($property);
    }

    public function setValue($value) {
        $this -> value = $value;
        return $this;
    }

}