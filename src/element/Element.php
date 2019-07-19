<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm\Render\Block;

/**
 *
 */
abstract class Element implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\JsonEncoder;

    protected $enabled = true;
    /**
     * The form this element belongs to
     * @var \Abivia\NextForm
     */
    protected $form;
    protected $group;
    protected $name = '';
    static protected $parentJsonEncodeMethod = [
        'type' => [],
        'name' => ['drop:blank', 'drop:null'],
        'group' => ['drop:null', 'map:memberOf'],
        'enabled' => ['drop:true'],
        'visible' => ['drop:true']
    ];
    protected $type;
    protected $visible = true;

    static public function classFromType($obj) {
        $result = 'Abivia\NextForm\Element\\' . ucfirst(strtolower($obj -> type)) . 'Element';
        if (!class_exists($result)) {
            throw new \InvalidArgumentException($obj -> type . ' is not a valid element type.');
        }
        return $result;
    }

    protected function configureInitialize() {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
        }
    }

    protected function configurePropertyIgnore($property) {
        return $property == 'type';
    }

    protected function configurePropertyMap($property) {
        if ($property == 'memberOf') {
            $property = 'group';
        }
        return $property;
    }

    public function generate($renderer, $access, $translate) {
        $readOnly = false; // $access -> hasAccess(...)
        $pageData = $renderer -> render($this, $translate, $readOnly);
        return $pageData;
    }

    public function getEnabled() {
        return $this -> enabled;
    }

    public function getGroup() {
        return $this -> group;
    }

    public function getName() {
        return $this -> name;
    }

    public function getType() {
        return $this -> type;
    }

    public function getVisible() {
        return $this -> visible;
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     */
    public function linkSchema($schema) {
        // Non-data elements do nothing.
    }

    public function setEnabled($enabled) {
        $this -> enabled = $enabled;
        return $this;
    }

    public function setGroup($group) {
        $this -> group = $group;
        return $this;
    }

    public function setName($name) {
        $this -> name = $name;
        return $this;
    }

    public function setVisible($visible) {
        $this -> visible = $visible;
        return $this;
    }

}