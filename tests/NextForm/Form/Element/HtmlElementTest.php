<?php

use Abivia\NextForm\Form\Element\HtmlElement;

class FormHtmlElementTest extends \PHPUnit\Framework\TestCase {

	public function testFormHtmlElementInstantiation() {
        $obj = new HtmlElement();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\HtmlElement', $obj);
	}

    /**
     * Check that a skeleton element gets set up correctly
     */
	public function testFormHtmlElementConfiguration() {
        $config = json_decode('
            {
                "type": "html",
                "value": "<h1>This is a heading<\\/h1>"
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new HtmlElement();
        $this->assertTrue($obj->configure($config, true));
		$this->assertEquals('html', $obj->getType());
		$this->assertEquals('', $obj->getName());
		$this->assertEquals(true, $obj->getDisplay());
    }

}
