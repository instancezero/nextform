<?php

use Abivia\NextForm\Form\Element\SectionElement;

class FormSectionElementTest extends \PHPUnit\Framework\TestCase {

	public function testFormSectionElementInstantiation() {
        $obj = new SectionElement();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\SectionElement', $obj);
	}

    /**
     * Check that a skeleton element gets set up correctly
     */
	public function testFormSectionElementConfiguration() {
        $config = json_decode('
            {
                "type": "section",
                "elements": []
            }'
        );
        $obj = new SectionElement();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('section', $obj->getType());
		$this->assertEquals('', $obj->getName());
		$this->assertEquals(true, $obj->getVisible());
    }

    /**
     * Check that a cell can contain all the valid things
     */
	public function testFormSectionElementNestedValid() {
        $config = json_decode('
            {
                "type": "section",
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
                    },
                    {
                        "type": "cell",
                        "elements": []
                    }
                ]
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new SectionElement();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('section', $obj->getType());
        $elements = $obj->getElements();
		$this->assertEquals(4, count($elements));
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\FieldElement', $elements[0]);
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\HtmlElement', $elements[1]);
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\StaticElement', $elements[2]);
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\CellElement', $elements[3]);
    }

    /**
     * Check that a section can't contain a section
     */
	public function testFormSectionElementNestedSection() {
        $config = json_decode('
            {
                "type": "section",
                "elements": [
                    {
                        "type": "section",
                        "elements": []
                    }
                ]
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new SectionElement();
        $this->expectException(\OutOfBoundsException::class);
        $this->assertFalse($obj->configure($config));
    }

}
