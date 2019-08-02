<?php

namespace Abivia\NextForm\Element;

/**
 *
 */
class ButtonElement Extends NamedElement {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    static protected $jsonEncodeMethod = [];
    static protected $jsonLocalMethod = [
        'reset' => ['drop:false'],
        'submit' => ['drop:false'],
    ];
    protected $reset = false;
    protected $submit = false;

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

    public function getButtonType() {
        $type = 'button';
        if ($this -> submit) {
            $type = 'submit';
        } elseif ($this -> reset) {
            $type = 'reset';
        }
        return $type;
    }

}