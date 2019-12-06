<?php
include_once __DIR__ . '/../../test-tools/Inspection.php';

use Abivia\NextForm\Data\Store;

/**
 * @covers \Abivia\NextForm\Data\Store
 */
class DataStoreTest extends \PHPUnit\Framework\TestCase {

    use Inspection;

    public function testEmpty() {
        $obj = new Store;
        $this->assertTrue($obj->isEmpty());

        $obj->setType('blob');
        $this->assertFalse($obj->isEmpty());

        $obj = new Store;
        $obj->setSize('large');
        $this->assertFalse($obj->isEmpty());
    }

    public function testLoad() {
        $obj = new Store();
        $config = json_decode('{"type":"int","size":"10"}');
        $this->assertTrue(false != $config, 'JSON error!');
        $this->assertTrue($obj->configure($config));
		$this->assertEquals('int', $obj->getType());
		$this->assertEquals('10', $obj->getSize());
    }

    public function testLoadBadType() {
        $obj = new Store();
        $config = json_decode('{"type":"jinglebells","size":"10"}');
        $this->assertTrue(false != $config, 'JSON error!');
        $this->assertFalse($obj->configure($config));
    }

	public function testInstantiation() {
        $obj = new Store();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Store', $obj);
	}

    public function testSize() {
        $obj = new Store;
        $this->assertEquals(null, $obj->getSize());

        $obj->setSize('large');
        $this->assertEquals('large', $obj->getSize());
    }

    public function testType() {
        $obj = new Store;
        $this->assertEquals(null, $obj->getType());

        $obj->setType('blob');
        $this->assertEquals('blob', $obj->getType());

        $this->expectException('\RuntimeException');
        $obj->setType('invalid');

    }

}
