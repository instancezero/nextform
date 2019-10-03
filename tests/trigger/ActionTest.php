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
            '{"change":"enable","value":true,"target":["field1","group1","group2"]}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Action();
        $this->assertTrue($obj->configure($config, true));
		$this->assertEquals(['enable'], $obj->getChange());
		$this->assertTrue($obj->getValue());
		$this->assertEquals(['field1', 'group1', 'group2'], $obj->getTarget());
    }

	public function testFormTriggerActionTargetCastToArray() {
        $config = json_decode(
            '{"change":"enabled","value":true,"target":"field1"}'
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
            '{"change":"enabled","value":"enable","target":"field1"}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Action();
        foreach ($knownChange as $type) {
            $config->change = $type;
            $this->assertTrue($obj->configure($config));
            $this->assertEquals([$type], $obj->getChange());
        }
        $config->change = '&^%* this will never be valid!!';
        $this->assertFalse($obj->configure($config));
    }

    public function testFormTriggerActionChangeGetSet() {
        $obj = new Action();
        $this->assertEquals([], $obj->getChange());
        $return = $obj->setChange('enabled');
        $this->assertTrue($obj === $return);
        $this->assertEquals(['enable'], $obj->getChange());
        $return = $obj->setChange('enable');
        $this->assertTrue($obj === $return);
        $this->assertEquals(['enable'], $obj->getChange());
        $this->expectException(\UnexpectedValueException::class);
        $return = $obj->setChange('blah');
    }

	public function testFormTriggerActionJsonEncode() {
        $jsonOriginal = '{"change":"enable","value":true,"target":["field1","group1","group2"]}';
        $config = json_decode($jsonOriginal);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Action();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals(['enable'], $obj->getChange());
        $vanilla = json_decode($jsonOriginal);
        $this->assertTrue(false != $vanilla, 'vanilla: JSON error!');
        $actual = json_decode(json_encode($obj));
        $this->assertTrue(false != $actual, 'actual: JSON error!');
		$this->assertEquals($vanilla, $actual);
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
        $this->assertEquals([], $obj->getChange());
        $return = $obj->setValue(6);
        $this->assertTrue($obj === $return);
        $this->assertEquals(6, $obj->getValue());
    }

	public function testFormTriggerActionEnableMap() {
        $obj = new Action();
        $obj->setChange('enabled');
		$this->assertEquals(['enable'], $obj->getChange());
    }

}
