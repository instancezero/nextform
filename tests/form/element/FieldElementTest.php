<?php

use Abivia\NextForm\Form\Element\FieldElement;

class FormFieldElementTest extends \PHPUnit\Framework\TestCase {

	public function testFormFieldElementInstantiation() {
        $obj = new FieldElement();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\FieldElement', $obj);
	}

    /**
     * Check that a skeleton element gets set up correctly
     */
	public function testFormFieldElementConfiguration() {
        $config = json_decode('
            {
                "type": "field",
                "object": "some.object.name",
                "memberOf": "somegroup"
            }'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new FieldElement();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('field', $obj->getType());
		$this->assertEquals('', $obj->getName());
		$this->assertEquals(true, $obj->getVisible());
    }

}
