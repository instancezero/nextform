<?php

use Abivia\NextForm\Trigger\Action;

/**
 * @covers \Abivia\NextForm\Trigger\Action
 */
class FormTriggerActionTest extends \PHPUnit\Framework\TestCase {

	public function testFormTriggerActionInstantiation() {
        $obj = new Action();
		$this->assertInstanceOf('\Abivia\NextForm\Trigger\Action', $obj);
	}

	public function testFormTriggerActionConfiguration() {
        $config = json_decode(
            '{"subject":"enable","value":true,"target":["field1","group1","group2"]}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Action();
        $this->assertTrue($obj->configure($config, true));
		$this->assertEquals('enable', $obj->getSubject());
		$this->assertTrue($obj->getValue());
		$this->assertEquals(['field1', 'group1', 'group2'], $obj->getTarget());
    }

	public function testFormTriggerActionTargetCastToArray() {
        $config = json_decode(
            '{"subject":"enable","value":true,"target":"field1"}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Action();
        $this->assertTrue($obj->configure($config, true));
		$this->assertEquals(['field1'], $obj->getTarget());
    }

    public function testFormTriggerActionChangeValidation() {
        $knownChange = [
            'enable', 'value', 'visible'
        ];
        $config = json_decode(
            '{"subject":"enable","value":"enable","target":"field1"}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Action();
        foreach ($knownChange as $type) {
            $config->subject = $type;
            $this->assertTrue($obj->configure($config));
            $this->assertEquals($type, $obj->getSubject());
        }
        $config->subject = '&^%* this will never be valid!!';
        $this->assertFalse($obj->configure($config));
    }

    public function testFormTriggerActionChangeGetSet() {
        $obj = new Action();
        $this->assertEquals(null, $obj->getSubject());
        $return = $obj->setSubject('enable');
        $this->assertTrue($obj === $return);
        $this->assertEquals('enable', $obj->getSubject());
        $this->expectException(\UnexpectedValueException::class);
        $return = $obj->setSubject('blah');
    }

	public function testFormTriggerActionJsonEncode() {
        $jsonOriginal = '{"subject":"enable","value":true,"target":["field1","group1","group2"]}';
        $config = json_decode($jsonOriginal);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Action();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('enable', $obj->getSubject());
        $vanilla = json_decode($jsonOriginal);
        $this->assertTrue(false != $vanilla, 'vanilla: JSON error!');
        $actual = json_decode(json_encode($obj));
        $this->assertTrue(false != $actual, 'actual: JSON error!');
		$this->assertEquals('field1,group1,group2:enable:true', $actual);
    }

    public function testFormTriggerActionTargetGetSet() {
        $obj = new Action();
        $this->assertEquals([], $obj->getTarget());
        $return = $obj->setTarget('field1');
        $this->assertTrue($obj === $return);
        $this->assertEquals(['field1'], $obj->getTarget());
        $return = $obj->setTarget(['field1', 'field2']);
        $this->assertEquals(['field1', 'field2'], $obj->getTarget());
    }

    public function testFormTriggerActionValueGetSet() {
        $obj = new Action();
        $this->assertEquals(null, $obj->getValue());
        $return = $obj->setValue(6);
        $this->assertTrue($obj === $return);
        $this->assertEquals(6, $obj->getValue());
    }

}
