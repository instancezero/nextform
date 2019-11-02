<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\ButtonElement;
use Abivia\NextForm\Form\Element\CellElement;
use Abivia\NextForm\Form\Element\FieldElement;
use Abivia\NextForm\Form\Element\HtmlElement;
use Abivia\NextForm\Form\Element\SectionElement;
use Abivia\NextForm\Form\Element\StaticElement;


/**
 * @covers Abivia\NextForm\Form\Binding\Binding
 */
class BindingTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test instantiation with a button element
     */
	public function testInstantiateButton()
    {
        $element = new ButtonElement();
        $binding = Binding::fromElement($element);
        $this->assertInstanceOf('\Abivia\NextForm\Form\Binding\Binding', $binding);
        $this->assertEquals($element, $binding->getElement());
	}

    /**
     * Test instantiation with a cell element
     */
	public function testInstantiateCell()
    {
        $element = new CellElement();
        $binding = Binding::fromElement($element);
        $this->assertInstanceOf('\Abivia\NextForm\Form\Binding\ContainerBinding', $binding);
        $this->assertEquals($element, $binding->getElement());
	}

    /**
     * Test instantiation with a field element
     */
	public function testInstantiateField()
    {
        $element = new FieldElement();
        $binding = Binding::fromElement($element);
        $this->assertInstanceOf('\Abivia\NextForm\Form\Binding\FieldBinding', $binding);
        $this->assertEquals($element, $binding->getElement());
	}

    /**
     * Test instantiation with a html element
     */
	public function testInstantiateHtml()
    {
        $element = new HtmlElement();
        $binding = Binding::fromElement($element);
        $this->assertInstanceOf('\Abivia\NextForm\Form\Binding\SimpleBinding', $binding);
        $this->assertEquals($element, $binding->getElement());
	}

    /**
     * Test instantiation with a section element
     */
	public function testInstantiateSection()
    {
        $element = new SectionElement();
        $binding = Binding::fromElement($element);
        $this->assertInstanceOf('\Abivia\NextForm\Form\Binding\ContainerBinding', $binding);
        $this->assertEquals($element, $binding->getElement());
	}

    /**
     * Test instantiation with a static element
     */
	public function testInstantiateStatic()
    {
        $element = new StaticElement();
        $binding = Binding::fromElement($element);
        $this->assertInstanceOf('\Abivia\NextForm\Form\Binding\SimpleBinding', $binding);
        $this->assertEquals($element, $binding->getElement());
	}

}
