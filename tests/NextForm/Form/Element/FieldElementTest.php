<?php

use Abivia\NextForm\Form\Element\FieldElement;
use Abivia\NextForm\Trigger\Trigger;

class FormElementFieldElementTest extends \PHPUnit\Framework\TestCase {

	public function testInstantiation() {
        $obj = new FieldElement();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Element\FieldElement', $obj);
	}

	public function testBuild() {
        $obj = FieldElement::build();
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

    public function testGetSetDefault()
    {
        $obj = new FieldElement();
		$this->assertEquals(null, $obj->getDefault());
        $obj->setDefault('foo');
		$this->assertEquals('foo', $obj->getDefault());
    }

    public function testGetSetObject()
    {
        $obj = new FieldElement();
		$this->assertEquals(null, $obj->getObject());
        $obj->setObject('foo');
		$this->assertEquals('foo', $obj->getObject());
    }

    public function testGetSetTriggers()
    {
        $obj = new FieldElement();
		$this->assertEquals([], $obj->getTriggers());
        $trigger1 = new Trigger();
        $obj->addTrigger($trigger1);
		$this->assertEquals([$trigger1], $obj->getTriggers());
        $trigger2 = new Trigger();
        $obj->addTrigger($trigger2);
		$this->assertEquals([$trigger1, $trigger2], $obj->getTriggers());
        $obj->setTriggers([]);
		$this->assertEquals([], $obj->getTriggers());
    }

}
