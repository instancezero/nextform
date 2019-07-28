<?php
include_once __DIR__ . '/../test-tools/Inspection.php';

use \Abivia\NextForm\Data\Store;

/**
 * @covers \Abivia\NextForm\Data\Store
 */
class DataStoreTest extends \PHPUnit\Framework\TestCase {

    use Inspection;

    public function testStoreLoad() {
        $obj = new Store();
        $config = json_decode('{"type":"int","size":"10"}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $this -> assertTrue($obj -> configure($config));
		$this -> assertEquals('int', $obj -> getType());
		$this -> assertEquals('10', $obj -> getSize());
    }

    public function testStoreLoadBadType() {
        $obj = new Store();
        $config = json_decode('{"type":"jinglebells","size":"10"}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $this -> assertFalse($obj -> configure($config));
    }

	public function testDataStoreInstantiation() {
        $obj = new Store();
		$this -> assertInstanceOf('\Abivia\NextForm\Data\Store', $obj);
	}

}
