<?php

use Abivia\NextForm;

class NextFormTest extends \PHPUnit\Framework\TestCase {

    public function testFormInstantiation() {
        $obj = new NextForm();
		$this->assertInstanceOf('\Abivia\NextForm', $obj);
    }

}
