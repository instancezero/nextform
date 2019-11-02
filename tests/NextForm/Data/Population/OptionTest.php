<?php

use Abivia\NextForm\Data\Population\Option;

/**
 * @covers Abivia\NextForm\Data\Population\Option
 */
class DataPopulationOptionTest extends \PHPUnit\Framework\TestCase {

	public function testInstantiation() {
        $obj = new Option();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Population\Option', $obj);
	}

    /**
     * Check that a minimalist option has the right default values.
     */
    public function testSimpleDefault() {
        $json = '{"label": "Something"}';
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->assertTrue($obj->configure($config));
        $this->assertEquals('Something', $obj->getLabel());
        $this->assertEquals('', $obj->getName());
        $this->assertEquals('Something', $obj->getValue());
        $this->assertTrue($obj->getEnabled());
        $this->assertFalse($obj->getSelected());
        $this->assertTrue($obj->configure($config));
    }

    /**
     * Check an option with a label and a value.
     */
    public function testSimpleValued() {
        $json = '{"label": "Something", "value": 5}';
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->assertTrue($obj->configure($config));
        $this->assertEquals('Something', $obj->getLabel());
        $this->assertEquals('', $obj->getName());
        $this->assertEquals(5, $obj->getValue());
        $this->assertTrue($obj->getEnabled());
        $this->assertFalse($obj->getSelected());
    }

    /**
     * Check an option with a label and a value.
     */
    public function testStringValued() {
        $obj = new Option();
        $this->assertTrue($obj->configure('Something:5'));
        $this->assertEquals('Something', $obj->getLabel());
        $this->assertEquals('', $obj->getName());
        $this->assertEquals(5, $obj->getValue());
        $this->assertTrue($obj->getEnabled());
        $this->assertFalse($obj->getSelected());
    }

    /**
     * Check that an option with a label and a value.
     */
    public function testSimpleNoLabel() {
        $json = '{"value": 5}';
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->expectException('OutOfBoundsException');
        $obj->configure($config);
    }

    public function testNested() {
        $json = <<<'jsonend'
{
    "name": "id",
    "label": "Theropods",
    "enabled": true,
    "value": [
        {
            "label": "Tyrannosaurus",
            "enabled": true,
            "selected": false,
            "value": 5
        }
    ]
}
jsonend;
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->assertTrue($obj->configure($config));
    }

    public function testNestedTooDeep() {
        $json = <<<'jsonend'
{
    "name": "id",
    "label": "Theropods",
    "enabled": true,
    "value": [
        {
            "label": "Tyrannosaurus",
            "enabled": true,
            "selected": false,
            "value": [
                {
                    "label": "Error",
                    "enabled": true,
                    "selected": false,
                    "value": -1
                }
            ]
        }
    ]
}
jsonend;
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->expectException('OutOfBoundsException');
        $obj->configure($config);
    }

    /**
     * Check that a minimalist option has the right default values.
     */
    public function testSidecar() {
        $json = '{"label": "Something","sidecar":{"prop":"foo"}}';
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->assertTrue($obj->configure($config));
        $sidecar = $obj->sidecar;
        $this->assertInstanceOf('\Stdclass', $sidecar);
        $this->assertEquals('foo', $sidecar->prop);
        $this->assertInstanceOf('\Abivia\NextForm\Data\Population\Option', $obj->setSidecar('fred'));
        $this->assertEquals('fred', $obj->sidecar);
    }

}