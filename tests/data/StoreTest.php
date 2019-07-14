<?php
include_once dirname(__FILE__) . '/../Inspection.php';

use \Abivia\NextForm\Data\Store;

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

	public function testDataStoreInstantiation() {
        $obj = new Store();
		$this -> assertInstanceOf('\Abivia\NextForm\Data\Store', $obj);
	}

}
