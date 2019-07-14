<?php

use Abivia\NextForm\Access\NullAccess;

class NullAccessTest extends \PHPUnit\Framework\TestCase {

	public function testNullAccessInstantiation() {
        $obj = new NullAccess();
		$this -> assertInstanceOf('\Abivia\NextForm\Access\NullAccess', $obj);
	}

    public function testNullAccessResult() {
        $obj = new NullAccess();
        $this -> assertTrue($obj -> hasAccess('foo', 'bar', 'bat'));
    }

}
