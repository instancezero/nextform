<?php

use Abivia\NextForm\Form\Element\Element;
use Illuminate\Contracts\Translation\Translator as Translator;

class ConcreteElement extends Element {

    public function __construct() {
        parent::__construct();
    }

    public function translate(Translator $translate) : Element {
        return $this;
    }
}

class FormConcreteElementTest extends \PHPUnit\Framework\TestCase {

    /**
     * Test instantiation of a nonexistent element class
     */
	public function testFormConcreteElementInstantiation() {
        $this->expectException('InvalidArgumentException');
        ConcreteElement::classFromType((object)['type' => 'SomeUnlikelyToExistElement']);
	}

    /**
     * Test the access methods for enabled
     */
	public function testFormConcreteElementEnabled() {
        $obj = new ConcreteElement();
        $this->assertTrue($obj->getEnabled());
		$this->assertTrue($obj === $obj->setEnabled(false));
        $this->assertFalse($obj->getEnabled());
    }

    /**
     * Test the access methods for group
     */
	public function testFormConcreteElementGroup() {
        $obj = new ConcreteElement();
        $this->assertEquals([], $obj->getGroups());
		$this->assertTrue($obj === $obj->setGroups('somegroup'));
        $this->assertEquals(['somegroup'], $obj->getGroups());

        // No duplicates in group list
        $this->assertTrue($obj === $obj->addGroup('somegroup'));
        $this->assertEquals(['somegroup'], $obj->getGroups());

        // Bad groupnames filtered
        $this->assertTrue($obj === $obj->addGroup('bad*group'));
        $this->assertEquals(['somegroup'], $obj->getGroups());

        // Whitespace around groupnames ignored
        $this->assertTrue($obj === $obj->addGroup(' goodgroup '));
        $this->assertEquals(['somegroup', 'goodgroup'], $obj->getGroups());
    }

    /**
     * Test the access methods for name
     */
	public function testFormConcreteElementId() {
        $obj = new ConcreteElement();
        $this->assertIsString($obj->getId());
		$this->assertTrue($obj === $obj->setId('ID'));
        $this->assertEquals('ID', $obj->getId());
    }

    /**
     * Test the access methods for name
     */
	public function testFormConcreteElementName() {
        $obj = new ConcreteElement();
        $this->assertEquals('', $obj->getName());
		$this->assertTrue($obj === $obj->setName('somename'));
        $this->assertEquals('somename', $obj->getName());
    }

    /**
     * Test the name validation
     */
	public function thisisnota_testFormConcreteElementBadNames() {
        $obj = new ConcreteElement();
        $this->assertEquals('', $obj->getName());
		$this->assertTrue($obj === $obj->setName('somename'));
    }

    /**
     * Test the access methods for visible
     */
	public function testFormConcreteElementVisible() {
        $obj = new ConcreteElement();
        $this->assertTrue($obj->getVisible());
		$this->assertTrue($obj === $obj->setVisible(false));
        $this->assertFalse($obj->getVisible());
    }

}
