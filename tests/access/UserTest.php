<?php

use Abivia\NextForm\Access\User;

/**
 * @covers Abivia\NextForm\Access\User
 */
class UserTest extends \PHPUnit\Framework\TestCase {

	public function testUserInstantiation() {
        $obj = new User();
		$this -> assertInstanceOf('\Abivia\NextForm\Access\User', $obj);
	}

    public function testAccessResult() {
        $config = json_decode('{"id": 1,"roles": ["role1", "role2"]}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new User();
        $this -> assertTrue($obj -> configure($config));
        $this -> assertEquals(1, $obj -> getId());
        $this -> assertEquals(['role1', 'role2'], $obj -> getRoles());
    }

}
