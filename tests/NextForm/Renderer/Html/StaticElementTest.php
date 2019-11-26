<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\StaticElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Html;

/**
 * @covers \Abivia\NextForm\Renderer\Html\StaticElement
 */
class NextFormRendererHtmlStaticElementTest extends \PHPUnit\Framework\TestCase
{

	public function testInstantiation()
    {
        $obj = new StaticElement(new Html(), new Binding());
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\StaticElement', $obj);
	}

	public function test1()
    {
		$this->assertEquals('', '');
    }

	public function test2()
    {
		$this->assertEquals('', '');
    }

}
