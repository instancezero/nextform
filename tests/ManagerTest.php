<?php

use Abivia\NextForm\Manager;

class NextFormTest extends \PHPUnit\Framework\TestCase {

    public function testFormInstantiation() {
        $obj = new Manager();
		$this->assertInstanceOf('\Abivia\NextForm\Manager', $obj);
    }

}
