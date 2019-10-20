<?php

use Abivia\NextForm\Form\Element\StaticElement;

class FormStaticElementTest extends \PHPUnit\Framework\TestCase {

	public function testFormStaticElementInstantiation() {
        $obj = new StaticElement();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\StaticElement', $obj);
	}

    /**
     * Check that a skeleton element gets set up correctly
     */
	public function testFormStaticElementConfiguration() {
        $config = json_decode('
            {
                "type": "static"
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new StaticElement();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('static', $obj->getType());
		$this->assertEquals('', $obj->getName());
		$this->assertEquals(true, $obj->getDisplay());
    }

}
