<?php

namespace Abivia\NextForm\Element;

/**
 *
 */
class SectionElement Extends ContainerElement {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $object;
    protected $triggers;

    public function __construct() {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = parent::$parentJsonEncodeMethod;
            self::$jsonEncodeMethod['labels'] = '';
            self::$jsonEncodeMethod['object'] = '';
            self::$jsonEncodeMethod['triggers'] = '';
        }
        $this -> type = 'section';
    }

    public function addElement(Element $element) {
        if ($element instanceof SectionElement) {
            throw new \OutOfBoundsException('Sections can\'t be nested.');
        }
        $this -> elements[] = $element;
        return $this;
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
        return parent::configureValidate($property, $value);
    }

    protected function findSegment() {
        return $this -> configureOptions['parent'] -> findSegment();
    }

}