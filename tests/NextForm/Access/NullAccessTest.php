<?php

use Abivia\NextForm\Access\NullAccess;

/**
 * @covers \Abivia\NextForm\Access\NullAccess
 */
class NullAccessTest extends \PHPUnit\Framework\TestCase {

	public function testNullAccessInstantiation() {
        $obj = new NullAccess();
		$this->assertInstanceOf('\Abivia\NextForm\Access\NullAccess', $obj);
	}

    /**
     * Test that arbitrary checks return true
     */
    public function testNullAccessResult() {
        $obj = new NullAccess();
        $this->assertTrue($obj->hasAccess('foo', 'bar', 'bat'));
    }

    /**
     * Check that setUser returns an object.
     */
    public function testNullSetUser() {
        $obj = new NullAccess();
        $this->assertTrue($obj === $obj->setUser(0));
    }

}
