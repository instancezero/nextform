<?php

use Abivia\NextForm\Access\Permissions;

/**
 * @covers \Abivia\NextForm\Access\Permissions
 */
class PermissionsTest extends \PHPUnit\Framework\TestCase {

	public function testPermissionsInstantiation() {
        $obj = new Permissions();
		$this -> assertInstanceOf('\Abivia\NextForm\Access\Permissions', $obj);
	}

    public function testPermissionsConfigure() {
        $config = json_decode('{"event.read": true}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Permissions();
        $this -> assertTrue($obj -> configure($config, true));
    }

    public function testPermissionsConfigureBad() {
        $config = json_decode('{"event": true}');
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Permissions();
        $this -> assertFalse($obj -> configure($config, true));
    }

    public function testPermissionsAddRule() {
        $obj = new Permissions();
        $this -> assertNull($obj -> has('some.thing', 'read'));
        $obj -> addRule('some.thing.read', true);
        $this -> assertTrue($obj -> has('some.thing', 'read'));
        $this -> assertTrue($obj -> has('some.thing.read'));
        $obj -> addRule('some.thing.write', false);
        $this -> assertFalse($obj -> has('some.thing', 'write'));
        $this -> assertNull($obj -> has('some.thing', 'foo'));
    }

    public function testPermissionsAddRuleInvalid() {
        $obj = new Permissions();
        $this -> expectException('DomainException');
        $obj -> addRule('bad-thing', true);
    }

}
