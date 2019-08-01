<?php

namespace Abivia\NextForm\Data\Population;

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Describes a value or list of values in a user selectable form object.
 */
class Option implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $enabled = true;
    static protected $jsonEncodeMethod = [
        'enabled' => ['drop:true'],
        'label' => [],
        'name' => ['drop:blank','drop:null'],
        'sidecar' => ['drop:null'],
        'value' => ['drop:null'],
    ];
    protected $label;
    protected $name;
    protected $selected = false;
    protected $sidecar;
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
                if (is_array($option -> getList())) {
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

    public function getList() {
        if (!is_array($this -> value)) {
            return null;
        }
        return $this -> value;
    }

    public function getName() {
        return $this -> name;
    }

    public function getSelected() {
        return $this -> selected;
    }

    public function getValue() {
        if (is_array($this -> value)) {
            return null;
        }
        return $this -> value;
    }

    public function isEmpty() : bool {
        if ($this -> enabled === false) {
            return false;
        }
        if ($this -> label !== null && $this -> label !== '') {
            return false;
        }
        if ($this -> name !== null && $this -> name !== '') {
            return false;
        }
        if ($this -> sidecar !== null) {
            return false;
        }
        if ($this -> value !== null) {
            return false;
        }
        return true;
    }


    public function translate(Translator $translate) {
        $this -> label = $translate -> trans($this -> label);
    }

}