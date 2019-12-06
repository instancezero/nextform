<?php

use Abivia\NextForm\Data\Population;
use Abivia\NextForm\Data\Population\Option;

/**
 * @covers Abivia\NextForm\Data\Population
 */
class DataPopulationTest extends \PHPUnit\Framework\TestCase {

	public function testDataPopulationInstantiation() {
        $obj = new Population();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Population', $obj);
	}

	public function testDataPopulationConfiguration() {
        $config = json_decode(
            '{"source": "static",'
            . '"query": "test",'
            . '"translate": false,'
            . '"parameters": ["objid"],"list": []}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Population();
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('static', $obj->getSource());
		$this->assertEquals('test', $obj->getQuery());
		$this->assertFalse($obj->getTranslate());
    }

    public function testDataPopulationTypeValidation() {
        $knownSources = 'fixed|static|local|remote';
        $obj = new Population();
        foreach (explode('|', $knownSources) as $type) {
            $obj->setSource($type);
        }
        $this->expectException('LogicException');
        $obj->setSource('&^%* this will never be valid!!');
    }

    /**
     * A population with a fixed lookup list
     */
    public function testFixed() {
        $json = <<<'jsonend'
{
    "source": "fixed",
    "list": [
        {
            "value": 1,
            "label": "langkey1"
        },
        {
            "value": 2,
            "label": "langkey2"
        }
    ]
}
jsonend;
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Population();
        $this->assertTrue($obj->configure($config));
        $list = $obj->getList();
        $this->assertTrue(isset($list[0]));
        $this->assertTrue(isset($list[1]));
        $this->assertEquals(2, count($list));

        foreach ($obj->getIterator() as $key => $item) {
            $this->assertInstanceOf('\Abivia\NextForm\Data\Population\Option', $item);
            $this->assertEquals($config->list[$key]->value, $item->getValue());
            $this->assertEquals($config->list[$key]->label, $item->getLabel());
        }
    }

    /**
     * A population with a simplified fixed lookup list
     */
    public function testFixedSimple() {
        $json = <<<'jsonend'
{
    "source": "fixed",
    "list": [
        "langkey1",
        "langkey2"
    ]
}
jsonend;
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Population();
        $this->assertTrue($obj->configure($config));
        $list = $obj->getList();
        $this->assertEquals(2, count($list));
        $this->assertTrue(isset($list[0]));
        $this->assertTrue(isset($list[1]));
		$this->assertInstanceOf('\Abivia\NextForm\Data\Population\Option', $list[0]);
    }

    /**
     * A population with a fixed nested lookup list
     */
    public function testFixedNested() {
        $json = <<<'jsonend'
{
    "source": "fixed",
    "list": [
        {
            "value": 1,
            "label": "langkey1"
        },
        {
            "label": "langkey2",
            "value": [
                {
                    "value": 2.1,
                    "label": "langkey2"
                }
            ]
        }
    ]
}
jsonend;
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Population();
        $this->assertTrue($obj->configure($config, true));
        $list = $obj->getList();
        $this->assertEquals(2, count($list));
        $this->assertEquals(1, count($list[1]->getList()));
    }

    /**
     * A population object with both options and a lookup
     */
    public function testOptionsLookup() {
        $json = <<<'jsonend'
{
    "source": "static",
    "query": "queryobjectid",
    "parameters": ["objid.1", "objid.2"],
    "list": [
        {
            "value": "a value",
            "label": "label or language key"
        }
    ]
}
jsonend;
        $config = json_decode($json);
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Population();
        $this->assertTrue($obj->configure($config, true));
    }

    public function testEmptyList() {
        $obj = new Population();
        $this->assertEquals([], $obj->getList());
    }

    public function testParameters() {
        $obj = new Population();
        $this->assertEquals([], $obj->getParameters());
        $obj->setParameters(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $obj->getParameters());
    }

    public function testQuery() {
        $obj = new Population();
        $this->assertEquals(null, $obj->getQuery());
        $obj->setQuery('select foo from bar');
        $this->assertEquals('select foo from bar', $obj->getQuery());
    }

    public function testSidecar() {
        $obj = new Population();
        $this->assertEquals(null, $obj->getSidecar());
        $obj->setSidecar('foo');
        $this->assertEquals('foo', $obj->getSidecar());
    }

    public function testSource() {
        $obj = new Population();
        $this->assertEquals(null, $obj->getSource());
        $obj->setSource('fixed');
        $this->assertEquals('fixed', $obj->getSource());

        $this->expectException('\LogicException');
        $obj->setSource('foo');
    }

    public function testTranslate() {
        $obj = new Population();
        $this->assertTrue($obj->getTranslate());
        $obj->setTranslate(false);
        $this->assertFalse($obj->getTranslate());
    }

    public function testEmpty() {
        $obj = new Population();
        $this->assertTrue($obj->isEmpty());
        $obj->setTranslate(false);
        $this->assertFalse($obj->isEmpty());

        $obj = new Population();
        $obj->setSource('fixed');
        $this->assertFalse($obj->isEmpty());

        $obj = new Population();
        $obj->setSidecar('foo');
        $this->assertFalse($obj->isEmpty());

        $obj = new Population();
        $obj->setQuery('foo');
        $this->assertFalse($obj->isEmpty());

        $obj = new Population();
        $obj->setParameters(['foo']);
        $this->assertFalse($obj->isEmpty());

        $obj = new Population();
        $obj->addOption(new Option);
        $this->assertFalse($obj->isEmpty());
    }

}
