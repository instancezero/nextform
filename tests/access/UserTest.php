<?php

use Abivia\NextForm\Access\User;

/**
 * @covers Abivia\NextForm\Access\User
 */
class UserTest extends \PHPUnit\Framework\TestCase {

	public function testInstantiation() {
        $obj = new User();
		$this -> assertInstanceOf('\Abivia\NextForm\Access\User', $obj);
	}

    public function testConfig() {
        $config = json_decode('{"id": 1,"roles": ["role1", "role2"]}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new User();
        $this -> assertTrue($obj -> configure($config));
        $this -> assertEquals(1, $obj -> getId());
        $this -> assertEquals(['role1', 'role2'], $obj -> getRoles());
    }

    public function testGetSetId() {
        $obj = new User();
        $this -> assertEquals(null, $obj -> getId());
        $obj -> setId('Bob');
        $this -> assertEquals('Bob', $obj -> getId());
    }

    public function testRoleOperations() {
        $obj = new User();
        $this -> assertEquals([], $obj -> getRoles());
        $obj -> addRole('user');
        $this -> assertEquals(['user'], $obj -> getRoles());
        $obj -> addRole('moderator');
        $this -> assertEquals(['user', 'moderator'], $obj -> getRoles());
        $obj -> deleteRole('moderator');
        $this -> assertEquals(['user'], $obj -> getRoles());
    }

}
