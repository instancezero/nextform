<?php

require_once __DIR__ . '/../../../test-tools/MockTranslate.php';

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
        $this->assertFalse($obj->isNested());

        $obj->setSelected(true);
        $this->assertTrue($obj->getSelected());
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
    public function testString() {
        $json = '"Something"';
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->assertTrue($obj->configure($config));
        $this->assertEquals('Something', $obj->getLabel());
        $this->assertEquals('', $obj->getName());
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
        $this->assertTrue($obj->isNested());
        $this->assertNull($obj->getValue());
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
        $this->assertInstanceOf(
            '\Abivia\NextForm\Data\Population\Option',
            $obj->setSidecar('fred')
        );
        $this->assertEquals('fred', $obj->sidecar);
        $this->assertEquals('fred', $obj->getSidecar());
    }

    /**
     * Check group functions.
     */
    public function testGroup() {
        $json = '{"label": "Something", "memberOf": ["g1", "bad news", "g2"]}';
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->assertTrue($obj->configure($config));
        $this->assertEquals(['g1', 'g2'], $obj->getGroups());

        $obj->addGroup('n1');
        $this->assertEquals(['g1', 'g2', 'n1'], $obj->getGroups());

        $obj->addGroup('bad name');
        $this->assertEquals(['g1', 'g2', 'n1'], $obj->getGroups());

        $obj->deleteGroup('g2');
        $this->assertEquals(['g1', 'n1'], $obj->getGroups());

        $obj->setGroups('bob');
        $this->assertEquals(['bob'], $obj->getGroups());

        $obj->setGroups(['bob', 'omar']);
        $this->assertEquals(['bob', 'omar'], $obj->getGroups());
    }

    public function testIsEmpty() {
        $obj = new Option();
        $this->assertTrue($obj->isEmpty());

        $obj = new Option();
        $obj->setValue(5);
        $this->assertFalse($obj->isEmpty());

        $obj = new Option();
        $obj->setSidecar(5);
        $this->assertFalse($obj->isEmpty());

        $obj = new Option();
        $obj->setName('bob');
        $this->assertFalse($obj->isEmpty());

        $obj = new Option();
        $obj->setLabel('ell');
        $this->assertFalse($obj->isEmpty());

        $obj = new Option();
        $obj->setGroups('g1');
        $this->assertFalse($obj->isEmpty());

        $obj = new Option();
        $obj->setEnabled(false);
        $this->assertFalse($obj->isEmpty());

    }

    public function testjsonCollapse() {
        $obj = new Option();
        $obj->setLabel('foo');
        $obj->setValue(5);
        $this->assertEquals('foo:5', $obj->jsonCollapse());

        $obj = new Option();
        $obj->setLabel('foo');
        $obj->setValue(true);
        $this->assertEquals('foo:true', $obj->jsonCollapse());

        $obj = new Option();
        $obj->setLabel('foo');
        $obj->setValue('foo');
        $this->assertEquals('foo', $obj->jsonCollapse());

        $obj = new Option();
        $obj->setSidecar(5);
        $this->assertEquals($obj, $obj->jsonCollapse());

        $obj = new Option();
        $obj->setName('bob');
        $this->assertEquals($obj, $obj->jsonCollapse());

        $obj = new Option();
        $obj->setGroups('g1');
        $this->assertEquals($obj, $obj->jsonCollapse());

        $obj = new Option();
        $obj->setEnabled(false);
        $this->assertEquals($obj, $obj->jsonCollapse());

    }

    /**
     * Check an option with a label and a value.
     */
    public function testTranslate() {
        $json = '{"label": "Something", "value": 5}';
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Option();
        $this->assertTrue($obj->configure($config));
        $trans = $obj->translate(new MockTranslate());
        $this->assertEquals('Something (tslt)', $trans->getLabel());
        $this->assertEquals(5, $obj->getValue());
    }

}
