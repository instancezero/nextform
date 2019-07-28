<?php

use \Abivia\NextForm\Element\Element;

class ConcreteElement extends Element {

}

class FormConcreteElementTest extends \PHPUnit\Framework\TestCase {

    /**
     * Test instantiation of a nonexistent element class
     */
	public function testFormConcreteElementInstantiation() {
        $this -> expectException('InvalidArgumentException');
        ConcreteElement::classFromType((object)['type' => 'SomeUnlikelyToExistElement']);
	}

    /**
     * Test the access methods for enabled
     */
	public function testFormConcreteElementEnabled() {
        $obj = new ConcreteElement();
        $this -> assertTrue($obj -> getEnabled());
		$this -> assertTrue($obj === $obj -> setEnabled(false));
        $this -> assertFalse($obj -> getEnabled());
    }

    /**
     * Test the access methods for group
     */
	public function testFormConcreteElementGroup() {
        $obj = new ConcreteElement();
        $this -> assertEquals('', $obj -> getGroup());
		$this -> assertTrue($obj === $obj -> setGroup('somegroup'));
        $this -> assertEquals('somegroup', $obj -> getGroup());
    }

    /**
     * Test the access methods for name
     */
	public function testFormConcreteElementId() {
        $obj = new ConcreteElement();
        $this -> assertIsString($obj -> getId());
		$this -> assertTrue($obj === $obj -> setId('ID'));
        $this -> assertEquals('ID', $obj -> getId());
    }

    /**
     * Test the access methods for name
     */
	public function testFormConcreteElementName() {
        $obj = new ConcreteElement();
        $this -> assertEquals('', $obj -> getName());
		$this -> assertTrue($obj === $obj -> setName('somename'));
        $this -> assertEquals('somename', $obj -> getName());
    }

    /**
     * Test the name validation
     */
	public function thisisnota_testFormConcreteElementBadNames() {
        $obj = new ConcreteElement();
        $this -> assertEquals('', $obj -> getName());
		$this -> assertTrue($obj === $obj -> setName('somename'));
    }

    /**
     * Test the access methods for visible
     */
	public function testFormConcreteElementVisible() {
        $obj = new ConcreteElement();
        $this -> assertTrue($obj -> getVisible());
		$this -> assertTrue($obj === $obj -> setVisible(false));
        $this -> assertFalse($obj -> getVisible());
    }

}
