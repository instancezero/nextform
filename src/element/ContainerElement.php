<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm\Render\Block;

/**
 *
 */
abstract class ContainerElement Extends NamedElement {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $elements = [];
    static protected $jsonEncodeMethod = [];

    public function __construct() {
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = parent::$jsonEncodeMethod;
            self::$jsonEncodeMethod['labels'] = ['drop:empty', 'drop:null'];
            self::$jsonEncodeMethod['elements'] = [];
        }
    }

    abstract public function addElement(Element $element);

    protected function configureClassMap($property, $value) {
        $result = false;
        if ($property == 'elements') {
            $result = new \stdClass;
            $result -> key = [$this, 'addElement'];
            $result -> className = [Element::class, 'classFromType'];
        } else {
            $result = parent::configureClassMap($property, $value);
        }
        return $result;
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

    public function generate($renderer, $access, $translate) {
        $this -> translate($translate);
        $options = false; // $access -> hasAccess(...)
        $options = ['access' => 'write'];
        $renderer -> pushContext($options);
        $containerData = $renderer -> render($this, $options);
        foreach ($this -> elements as $element) {
            $containerData -> merge($element -> generate($renderer, $access, $translate));
        }
        $containerData -> close();
        $renderer -> popContext($containerData, $options);
        return $containerData;
    }

    public function getElements() {
        return $this -> elements;
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema) {
        foreach ($this -> elements as $element) {
            $element -> bindSchema($schema);
        }
    }

}