<?php

use Abivia\NextForm\Form\Element\CellElement;
use Abivia\NextForm;

/**
 * @covers \Abivia\NextForm\Form\Element\CellElement
 */
class FormCellElementTest extends \PHPUnit\Framework\TestCase {

	public function testFormCellElementInstantiation() {
        $obj = new CellElement();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\CellElement', $obj);
	}

    /**
     * Check that a skeleton element gets set up correctly
     */
	public function testFormCellElementConfiguration() {
        $config = json_decode('
            {
                "type": "cell",
                "elements": []
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new CellElement();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('cell', $obj->getType());
		$this->assertEquals('', $obj->getName());
		$this->assertEquals(true, $obj->getVisible());
    }

    /**
     * Check that a cell can contain all the valid things
     */
	public function testFormCellElementNestedValid() {
        $config = json_decode('
            {
                "type": "cell",
                "elements": [
                    {
                        "type": "field",
                        "object": "some-identifier"
                    },
                    {
                        "type": "html",
                        "value": "<em>stuff</em>"
                    },
                    {
                        "type": "static",
                        "value": "your text here"
                    }
                ]
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new CellElement();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('cell', $obj->getType());
        $elements = $obj->getElements();
		$this->assertEquals(3, count($elements));
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\FieldElement', $elements[0]);
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\HtmlElement', $elements[1]);
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\StaticElement', $elements[2]);
    }

    /**
     * Check that a cell can't contain a cell
     */
	public function testFormCellElementNestedCell() {
        $config = json_decode('
            {
                "type": "cell",
                "elements": [
                    {
                        "type": "cell",
                        "elements": []
                    }
                ]
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new CellElement();
        $this->expectException(\RuntimeException::class);
        $this->assertFalse($obj->configure($config));
    }

    /**
     * Check that a cell can't contain a section
     */
	public function testFormCellElementNestedSection() {
        $config = json_decode('
            {
                "type": "cell",
                "elements": [
                    {
                        "type": "section",
                        "elements": []
                    }
                ]
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new CellElement();
        $this->expectException(\RuntimeException::class);
        $this->assertFalse($obj->configure($config));
    }

}
