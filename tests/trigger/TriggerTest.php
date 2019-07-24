<?php

use \Abivia\NextForm\Trigger\Trigger;

class FormTriggerTest extends \PHPUnit\Framework\TestCase {

	public function testFormTriggerInstantiation() {
        $obj = new Trigger();
		$this -> assertInstanceOf('\Abivia\NextForm\Trigger\Trigger', $obj);
	}

    /**
     * Check that a skeleton event gets set up correctly
     */
	public function testFormTriggerConfigurationEvent() {
        $config = json_decode('
            {
                "event": "onValid",
                "actions": []
            }'
        );
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Trigger();
        $this -> assertTrue($obj -> configure($config));
		$this -> assertEquals('event', $obj -> getType());
		$this -> assertEquals('onvalid', $obj -> getEvent());
    }

    /**
     * Check that actions are loaded correctly
     */
	public function testFormTriggerConfigurationActions() {
        $config = json_decode('
            {
                "event": "onValid",
                "actions": [
                    {
                        "change": "visible",
                        "value": false,
                        "target": "field1"
                    },
                    {
                        "change": "enable",
                        "value": true,
                        "target": "field2"
                    }
                ]
            }'
        );
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Trigger();
        $this -> assertTrue($obj -> configure($config, true));
		$actions = $obj -> getActions();
		$this -> assertEquals(2, count($actions));
        $this -> assertInstanceOf('\Abivia\NextForm\Trigger\Action', $actions[0]);
    }

    /**
     * Check that actions are loaded correctly
     */
	public function testFormTriggerConfigurationEventTypeError() {
        $config = json_decode('
            {
                "event": "invalidevent",
                "actions": []
            }'
        );
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Trigger();
        $this -> assertFalse($obj -> configure($config));
    }

	public function testFormTriggerConfigurationValue() {
        $config = json_decode('
            {
                "value": 7,
                "actions": []
            }'
        );
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Trigger();
        $this -> assertTrue($obj -> configure($config));
		$this -> assertEquals('value', $obj -> getType());
		$this -> assertEquals(7, $obj -> getValue());
    }

	public function testFormTriggerConfigurationMalformed() {
        $config = json_decode('
            {
                "actions": []
            }'
        );
        $this -> assertTrue(false != $config, 'JSON error!');
        $obj = new Trigger();
        $this -> assertFalse($obj -> configure($config));
    }

}
