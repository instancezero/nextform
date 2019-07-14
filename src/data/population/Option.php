<?php

namespace Abivia\NextForm\Data\Population;

/**
 * Describes a value or list of values in a user selectable form object.
 */
class Option {
    use \Abivia\Configurable\Configurable;

    protected $enabled = true;
    protected $label;
    protected $name;
    protected $selected = false;
    protected $value;

    protected function configureClassMap($property, $value) {
        $result = false;
        if ($property == 'value' && is_array($value)) {
            $result = self::class;
        }
        return $result;
    }

    protected function configureComplete(): bool {
        if (is_array($this -> value)) {
            foreach($this -> value as $option) {
                if (is_array($option -> getValue())) {
                    throw new \OutOfBoundsException('Options can\'t be nested more than two levels deep.');
                }
            }
        }
        if ($this -> label == '') {
            throw new \OutOfBoundsException('Options must have a label.');
        }
        if ($this -> value == '') {
            $this -> value = $this -> label;
        }
        return true;
    }

    public function getEnabled() {
        return $this -> enabled;
    }

    public function getLabel() {
        return $this -> label;
    }

    public function getName() {
        return $this -> name;
    }

    public function getSelected() {
        return $this -> selected;
    }

    public function getValue() {
        return $this -> value;
    }

}