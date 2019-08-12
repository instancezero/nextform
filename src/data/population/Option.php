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
        'label' => [],
        'value' => ['drop:null'],
        'enabled' => ['drop:true'],
        'name' => ['drop:blank','drop:null'],
        'sidecar' => ['drop:null'],
    ];
    protected $label;
    protected $name;
    protected $selected = false;
    /**
     * Arbitrary data associated with this field.
     * @var mixed
     */
    public $sidecar;
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

    protected function configureInitialize(&$config, &$options) {
        // if the value is an array convert any strings to a class
        if (isset($config -> value) && is_array($config -> value)) {
            foreach ($config -> value as &$value) {
                if (is_string($value)) {
                    // Convert to a useful class
                    $obj = new \Stdclass;
                    $obj -> label = $value;
                    $value = $obj;
                }
            }
        }
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

    public function getSidecar() {
        return $this -> sidecar;
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

    public function isNested() {
        return is_array($this -> value);
    }

    public function setEnabled($enabled) {
        $this -> enabled = $enabled;
        return $this;
    }

    public function setLabel($label) {
        $this -> label = $label;
        return $this;
    }

    public function setName($name) {
        $this -> name = $name;
        return $this;
    }

    public function setSelected($selected) {
        $this -> selected = $selected;
        return $this;
    }

    public function setSidecar($data) {
        $this -> sidecar = $data;
        return $this;
    }

    public function setValue($value) {
        $this -> value = $value;
        return $this;
    }

    public function translate(Translator $translate) {
        $this -> label = $translate -> trans($this -> label);
    }

}