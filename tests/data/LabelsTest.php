<?php

use \Abivia\NextForm\Data\Labels;

class DataLabelsTest extends \PHPUnit\Framework\TestCase {

	public function testDataLabelsInstantiation() {
        $obj = new Labels();
		$this -> assertInstanceOf('\Abivia\NextForm\Data\Labels', $obj);
	}

	public function testDataLabelsInitialValues() {
        $obj = new Labels();
		$this -> assertTrue($obj -> after === null);
		$this -> assertTrue($obj -> before === null);
		$this -> assertTrue($obj -> error === null);
		$this -> assertTrue($obj -> heading === null);
		$this -> assertTrue($obj -> help === null);
		$this -> assertTrue($obj -> placeholder === null);
	}

	public function testDataLabelsConfiguration() {
        $config = json_decode(
            '{"after": "after","before": "before","error": "error",'
            . '"heading": "heading","help": "help","placeholder": "placeholder"}'
        );
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Labels();
        $obj -> configure($config);
		$this -> assertEquals('after', $obj -> after);
		$this -> assertEquals('before', $obj -> before);
		$this -> assertEquals('error', $obj -> error);
		$this -> assertEquals('heading', $obj -> heading);
		$this -> assertEquals('help', $obj -> help);
		$this -> assertEquals('placeholder', $obj -> placeholder);
	}

}
