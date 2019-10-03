<?php

use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\SimpleElement;
use Illuminate\Contracts\Translation\Translator as Translator;

class ConcreteSimpleElement extends SimpleElement
{

    public function __construct() {
        parent::__construct();
    }

    public function translate(Translator $translate) : Element
    {
        return $this;
    }
}

class FormConcreteSimpleElementTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test the access methods for enabled
     */
	public function testEnabled() {
        $obj = new ConcreteSimpleElement();
        $this->assertTrue($obj->getEnabled());
		$this->assertTrue($obj === $obj->setEnabled(false));
        $this->assertFalse($obj->getEnabled());
    }

    /**
     * Test the access methods for group
     */
	public function testGroup() {
        $obj = new ConcreteSimpleElement();
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
	public function testId() {
        $obj = new ConcreteSimpleElement();
        $this->assertIsString($obj->getId());
		$this->assertTrue($obj === $obj->setId('ID'));
        $this->assertEquals('ID', $obj->getId());
    }

    /**
     * Test the access methods for name
     */
	public function testName() {
        $obj = new ConcreteSimpleElement();
        $this->assertEquals('', $obj->getName());
		$this->assertTrue($obj === $obj->setName('somename'));
        $this->assertEquals('somename', $obj->getName());
    }

    /**
     * Test the name validation
     */
	public function thisisnota_testBadNames() {
        $obj = new ConcreteSimpleElement();
        $this->assertEquals('', $obj->getName());
		$this->assertTrue($obj === $obj->setName('somename'));
    }

    /**
     * Test the access methods for visible
     */
	public function testVisible() {
        $obj = new ConcreteSimpleElement();
        $this->assertTrue($obj->getVisible());
		$this->assertTrue($obj === $obj->setVisible(false));
        $this->assertFalse($obj->getVisible());
    }

}
