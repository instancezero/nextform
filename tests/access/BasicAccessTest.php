<?php

use Abivia\NextForm\Access\BasicAccess;

class BasicAccessTest extends \PHPUnit\Framework\TestCase {

	public function testBasicAccessInstantiation() {
        $obj = new BasicAccess();
		$this -> assertInstanceOf('\Abivia\NextForm\Access\BasicAccess', $obj);
	}

    /**
     * Ensure that a test configuration creates the expected object.
     */
    public function testBasicAccessConfigure() {
        $config = json_decode(file_get_contents(__DIR__ . '/BasicAccess.json'));
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new BasicAccess();
        $this -> assertTrue($obj -> configure($config));
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(__DIR__ . '/BasicAccess_actual.txt', $dump);
        $this -> assertStringEqualsFile(__DIR__ . '/BasicAccess_expect.txt', $dump);
    }

    /**
     * Make sure we can't load the currentUser from configuration.
     */
    public function testBasicAccessConfigureBad() {
        $config = json_decode('{"currentUser":{"whatever":true}}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new BasicAccess();
        $this -> assertFalse($obj -> configure($config, true));
    }

    /**
     * Verify that setUser() only works for valid users.
     */
    public function testBasicAccessSetUser() {
        $config = json_decode(file_get_contents(__DIR__ . '/BasicAccess.json'));
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new BasicAccess();
        $this -> assertTrue($obj -> configure($config));
        $obj -> setUser(1);
        $obj -> setUser(null);
        $obj -> setUser(2);
        $this -> expectException('LogicException');
        $obj -> setUser(9999);
    }

    /**
     * Check that our standard configuration generates the expected access space.
     */
    public function testBasicAccessPermissions() {
        $config = json_decode(file_get_contents(__DIR__ . '/BasicAccess.json'));
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new BasicAccess();
        $this -> assertTrue($obj -> configure($config));
        // Administrator tests
        $obj -> setUser(1);
        $this -> assertTrue($obj -> hasAccess('event', 'arbitrary', 'write'));
        $this -> assertTrue($obj -> hasAccess('event', 'id', 'read'));
        $this -> assertFalse($obj -> hasAccess('event', 'id', 'write'));
        $this -> assertFalse($obj -> hasAccess('undef', 'any', 'thing'));
        // Moderator tests using user argument
        $this -> assertTrue($obj -> hasAccess('event', 'arbitrary', 'read', 2));
        $this -> assertTrue($obj -> hasAccess('event', 'name', 'write', 2));
        $this -> assertFalse($obj -> hasAccess('event', 'arbitrary', 'write', 2));
        $this -> assertFalse($obj -> hasAccess('event', 'id', 'write', 2));
        $this -> assertFalse($obj -> hasAccess('event', 'profit', 'read', 2));
        $this -> assertFalse($obj -> hasAccess('undef', 'any', 'thing', 2));
        // Make sure default admin user still gets the same result
        $this -> assertTrue($obj -> hasAccess('event', 'id', 'read'));
        // Then switch to a guest
        $obj -> setUser(3);
        $this -> assertTrue($obj -> hasAccess('event', 'name', 'read'));
        $this -> assertFalse($obj -> hasAccess('event', 'name', 'write'));
        $this -> assertFalse($obj -> hasAccess('event', 'id', 'read'));
        $this -> assertFalse($obj -> hasAccess('users', 'any', 'read'));
        // Finally pass an invalid user
        $this -> expectException('LogicException');
        $obj -> hasAccess('undef', 'any', 'thing', 2487631);
    }

}
