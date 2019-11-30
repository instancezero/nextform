<?php

use Abivia\NextForm\Form\Element\FieldElement;

class FormElementFieldElementTest extends \PHPUnit\Framework\TestCase {

	public function testFormFieldElementInstantiation() {
        $obj = new FieldElement();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\FieldElement', $obj);
	}

    /**
     * Check that a skeleton element gets set up correctly
     */
	public function testConfiguration() {
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
        $this->assertEquals('some.object.name', $obj->getObject());
		$this->assertEquals('', $obj->getName());
		$this->assertEquals(true, $obj->getDisplay());

        // Make it not string-collapsable, several times, in order
        $obj->setShow('foo');
        $this->assertEquals($obj, $obj->jsonCollapse());
        $obj->setEnabled(false);
        $this->assertEquals($obj, $obj->jsonCollapse());
        $obj->setDefault('foo');
        $this->assertEquals($obj, $obj->jsonCollapse());
    }

    /**
     * Check that a short form element gets set up correctly
     */
	public function testConfigurationShort() {
        $config = json_decode('"some.object.name:somegroup"');
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new FieldElement();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('field', $obj->getType());
        $this->assertEquals('some.object.name', $obj->getObject());
		$this->assertEquals('', $obj->getName());
		$this->assertEquals(true, $obj->getDisplay());

        // Ensure we're still string-collapsable
        $this->assertEquals('some.object.name:somegroup', $obj->jsonCollapse());
    }

    public function testDataPropertyEmpty()
    {
        $obj = new FieldElement();
        $this->expectException('\RuntimeException');
        $obj->getDataProperty();
    }

    public function testGetSetDefault()
    {
        $obj = new FieldElement();
		$this->assertEquals(null, $obj->getDefault());
        $obj->setDefault('foo');
		$this->assertEquals('foo', $obj->getDefault());
    }

    public function testGetTriggers()
    {
        $obj = new FieldElement();
		$this->assertEquals([], $obj->getTriggers());
    }

}
