<?php

use Abivia\NextForm\Access\Role;

/**
 * @covers \Abivia\NextForm\Access\Role
 */
class RoleTest extends \PHPUnit\Framework\TestCase {

	public function testRoleInstantiation() {
        $obj = new Role();
		$this -> assertInstanceOf('\Abivia\NextForm\Access\Role', $obj);
	}

    public function testRoleResult() {
        $config = json_decode('{"name": "somerolename",'
            . '"permissions": {"segment.read": true,'
            . '"segment-writable.write": true, "segment-protected.read":false}}'
        );
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Role();
        $this -> assertTrue($obj -> configure($config));
        $this -> assertEquals('somerolename', $obj -> getName());
        // Global read on segment
        $this -> assertTrue($obj -> has('segment.read'));
        // Global read in parsed form
        $this -> assertTrue($obj -> has('segment', 'read'));
        // Read and write on the writable object: null and true
        $this -> assertNull($obj -> has('segment-writable', 'read'));
        $this -> assertTrue($obj -> has('segment-writable', 'write'));
        // Test explicit denial of read
        $this -> assertFalse($obj -> has('segment-protected', 'read'));
        // Global permissions have nothing to say about unspecified objects
        $this -> assertNull($obj -> has('segment-assumed', 'read'));
    }

    public function testRoleNames() {
        $obj = new Role();
        $obj -> setName('guest');
        $this -> assertEquals('guest', $obj -> getName());
        $obj -> setName('abcd');
        $this -> assertEquals('abcd', $obj -> getName());
    }

}
