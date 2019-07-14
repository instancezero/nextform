<?php

use \Abivia\NextForm\Data\Presentation;

class DataPresentationTest extends \PHPUnit\Framework\TestCase {

	public function testDataPresentationInstantiation() {
        $obj = new Presentation();
		$this -> assertInstanceOf('\Abivia\NextForm\Data\Presentation', $obj);
	}

	public function testDataPresentationConfiguration() {
        $config = json_decode('{"cols": "1","type": "text"}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Presentation();
        $this -> assertTrue($obj -> configure($config, true));
		$this -> assertEquals(1, $obj -> getCols());
		$this -> assertFalse($obj -> getConfirm());
		$this -> assertEquals('text', $obj -> getType());
    }

    public function testDataPresentationTypeValidation() {
        $knownTypes = [
            'checkbox', 'file', 'hidden', 'radio', 'select', 'text', 'textarea', //'textauto',
        ];
        $config = json_decode('{"cols": "1","type": "text"}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Presentation();
        foreach ($knownTypes as $type) {
            $config -> type = $type;
            $this -> assertTrue($obj -> configure($config, true));
        }
        $config -> type = '&^%* this will never be valid!!';
        $this -> assertFalse($obj -> configure($config, true));
    }

}
