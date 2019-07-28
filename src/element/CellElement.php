<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm;

/**
 *
 */
class CellElement Extends ContainerElement {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    static protected $jsonEncodeMethod = [];

    public function __construct() {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = parent::$jsonEncodeMethod;
        }
        $this -> type = 'cell';
    }

    public function addElement(Element $element) {
        if ($element instanceof ContainerElement) {
            throw new \OutOfBoundsException('Cells can\'t contain containers (sections or cells).');
        }
        $this -> elements[] = $element;
        return $this;
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
            $this -> form -> registerElement($this);
        }
    }

    protected function configureComplete() {
        return parent::configureComplete();
    }

    protected function configurePropertyIgnore($property) {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property) {
        return parent::configurePropertyMap($property);
    }

    public function findSegment() {
        return isset($this -> configureOptions['parent'])
            ? $this -> configureOptions['parent'] -> getSegment() : null;
    }

}