<?php
require_once __DIR__ . '/../../test-tools/MockTranslate.php';
require_once __DIR__ . '/../../test-tools/JsonComparison.php';

use \Abivia\NextForm\Data\Labels;

/**
 * @covers \Abivia\NextForm\Data\Labels
 */
class DataLabelsTest extends \PHPUnit\Framework\TestCase {
    use JsonComparison;

    protected $configConfirm;
    protected $configSimple;

    public function setUp() : void
    {
        $this->configConfirm = json_decode(
            '{"accept": "accept", "after": "after"'
            . ', "before": "before"'
            . ', "confirm": {"heading": "confirm heading"'
            . ', "help": "confirm help"'
            . '}'
            . ', "error": ["error1", "error2"]'
            . ', "heading": "heading", "help": "help"'
            . ', "inner": "placeholder"}'
        );
        if (!$this->configConfirm) {
            throw new RuntimeException('jsonConfirm syntax error!');
        }
        $this->configSimple = json_decode(
            '{"accept": "accept", "after": "after"'
            . ', "before": "before"'
            . ', "error": "error"'
            . ', "heading": "heading", "help": "help"'
            . ', "inner": "placeholder"}'
        );
        if (!$this->configSimple) {
            throw new RuntimeException('jsonSimple syntax error!');
        }
    }

	public function testInstantiation() {
        $obj = new Labels();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Labels', $obj);
	}

	public function testInitialValues() {
        $obj = new Labels();
		$this->assertTrue($obj->accept === null);
		$this->assertTrue($obj->after === null);
		$this->assertTrue($obj->before === null);
		$this->assertTrue($obj->error === null);
		$this->assertTrue($obj->heading === null);
		$this->assertTrue($obj->help === null);
		$this->assertTrue($obj->inner === null);
	}

	public function testConfigurationConfirm() {
        $obj = new Labels();
        $obj->configure($this->configConfirm);
		$this->assertEquals('accept', $obj->accept);
		$this->assertEquals('after', $obj->after);
		$this->assertEquals('before', $obj->before);
		$this->assertEquals(['error1', 'error2'], $obj->error);
		$this->assertEquals('heading', $obj->heading);
		$this->assertEquals('help', $obj->help);
		$this->assertEquals('placeholder', $obj->inner);
	}

	public function testConfigurationSimple() {
        $obj = new Labels();
        $obj->configure($this->configSimple);
		$this->assertEquals('accept', $obj->accept);
		$this->assertEquals('after', $obj->after);
		$this->assertEquals('before', $obj->before);
		$this->assertEquals('error', $obj->error);
		$this->assertEquals('heading', $obj->heading);
		$this->assertEquals('help', $obj->help);
		$this->assertEquals('placeholder', $obj->inner);
	}

	public function testConfigurationString() {
        $config = json_decode('"heading"');
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Labels();
        $obj->configure($config);
		$this->assertTrue($obj->accept === null);
		$this->assertTrue($obj->after === null);
		$this->assertTrue($obj->before === null);
		$this->assertTrue($obj->error === null);
		$this->assertEquals('heading', $obj->heading);
		$this->assertTrue($obj->help === null);
		$this->assertTrue($obj->inner === null);
	}

	public function testConfigurationNoNestedConfirm() {
        $config = json_decode(
            '{'
            . '"accept": "accept"'
            . ', "after": "after"'
            . ', "before": "before"'
            . ', "confirm": {"heading": "confirm heading"'
                . ', "help": "confirm help"'
                . ', "confirm": { "error": "this is wrong!" }'
            . '}'
            . ', "error": "error"'
            . ', "heading": "heading", "help": "help"'
            . ', "inner": "placeholder"}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Labels();
        $this->assertFalse($obj->configure($config));
	}

	public function testEmpty() {
        $obj = new Labels();
		$this->assertTrue($obj->isEmpty());
        $obj->heading = 'stuff';
		$this->assertFalse($obj->isEmpty());
	}

    public function testForConfirm() {
        $obj = new Labels();
        $obj->configure($this->configConfirm);
        $confirm = $obj->forConfirm();
		$this->assertEquals('accept', $confirm->get('accept'));
		$this->assertEquals('after', $confirm->get('after'));
		$this->assertEquals('before', $confirm->get('before'));
		$this->assertEquals(['error1', 'error2'], $confirm->get('error'));
		$this->assertEquals('confirm heading', $confirm->get('heading'));
		$this->assertEquals('confirm help', $confirm->get('help'));
		$this->assertEquals('placeholder', $confirm->get('inner'));

        // No confirm elements in the confirm version
		$this->assertFalse($confirm->has('heading', true));
    }

	public function testGetSimple() {
        $obj = new Labels();
        $obj->configure($this->configSimple);
		$this->assertEquals('accept', $obj->get('accept'));
		$this->assertEquals('after', $obj->get('after'));
		$this->assertEquals('before', $obj->get('before'));
		$this->assertEquals('error', $obj->get('error'));
		$this->assertEquals('heading', $obj->get('heading'));
		$this->assertEquals('help', $obj->get('help'));
		$this->assertEquals('placeholder', $obj->get('inner'));

		$this->assertEquals('accept', $obj->get('accept', true));
		$this->assertEquals('after', $obj->get('after', true));
		$this->assertEquals('before', $obj->get('before', true));
		$this->assertEquals('error', $obj->get('error', true));
		$this->assertEquals('heading', $obj->get('heading', true));
		$this->assertEquals('help', $obj->get('help', true));
		$this->assertEquals('placeholder', $obj->get('inner', true));
	}

	public function testGetConfirm() {
        $obj = new Labels();
        $obj->configure($this->configConfirm);
		$this->assertEquals('accept', $obj->get('accept'));
		$this->assertEquals('after', $obj->get('after'));
		$this->assertEquals('before', $obj->get('before'));
		$this->assertEquals(['error1', 'error2'], $obj->get('error'));
		$this->assertEquals('heading', $obj->get('heading'));
		$this->assertEquals('help', $obj->get('help'));
		$this->assertEquals('placeholder', $obj->get('inner'));

		$this->assertEquals('accept', $obj->get('accept', true));
		$this->assertEquals('after', $obj->get('after', true));
		$this->assertEquals('before', $obj->get('before', true));
		$this->assertEquals(['error1', 'error2'], $obj->get('error', true));
		$this->assertEquals('confirm heading', $obj->get('heading', true));
		$this->assertEquals('confirm help', $obj->get('help', true));
		$this->assertEquals('placeholder', $obj->get('inner', true));
	}

    public function testJsonEncode() {
        $obj = new Labels();
        $obj->configure($this->configConfirm);
        $encoded = json_encode($obj);
        $compare = json_decode($encoded);
        $this->assertTrue($this->jsonCompare($this->configConfirm, $compare));
    }

	public function testHasSparse() {
        $config = json_decode(
            '{"accept": "accept", "after": "after"'
            . ', "before": "before"'
            . ', "confirm": {"heading": "confirm heading"'
            . ', "help": "confirm help"'
            . '}'
            . ', "heading": "heading", "help": "help"'
            . ', "inner": "placeholder"}'
        );
        $obj = new Labels();
        $obj->configure($config);
		$this->assertTrue($obj->has('accept'));
		$this->assertTrue($obj->has('after'));
		$this->assertTrue($obj->has('before'));
		$this->assertFalse($obj->has('error'));
		$this->assertTrue($obj->has('heading'));
		$this->assertTrue($obj->has('help'));
		$this->assertTrue($obj->has('inner'));

		$this->assertFalse($obj->has('accept', true));
	}

	public function testHasConfirm() {
        $obj = new Labels();
        $obj->configure($this->configConfirm);
		$this->assertTrue($obj->has('accept'));
		$this->assertTrue($obj->has('after'));
		$this->assertTrue($obj->has('before'));
		$this->assertTrue($obj->has('error'));
		$this->assertTrue($obj->has('heading'));
		$this->assertTrue($obj->has('help'));
		$this->assertTrue($obj->has('inner'));

		$this->assertFalse($obj->has('accept', true));
		$this->assertFalse($obj->has('after', true));
		$this->assertFalse($obj->has('before', true));
		$this->assertFalse($obj->has('error', true));
		$this->assertTrue($obj->has('heading', true));
		$this->assertTrue($obj->has('help', true));
		$this->assertFalse($obj->has('inner', true));
	}

	public function testCombineSimple() {
        $obj1 = new Labels();
        $obj1->heading = 'stuff';
        $obj2 = new Labels();
        $obj2->before = 'before';
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->heading = 'stuff';
        $mergeExpect->before = 'before';
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineOverwrite() {
        $obj1 = new Labels();
        $obj1->heading = 'stuff';
        $obj1->before = 'default';
        $obj2 = new Labels();
        $obj2->after = 'after';
        $obj2->before = 'before';
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->after = 'after';
        $mergeExpect->before = 'default';
        $mergeExpect->heading = 'stuff';
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineInsertConfirm() {
        $obj1 = new Labels();
        $obj1->heading = 'stuff';
        $obj1->before = 'before merged';
        $obj1->set('before', 'before confirm merged', true);

        $obj2 = new Labels();
        $obj2->after = 'after';
        $obj2->before = 'before';
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->after = 'after';
        $mergeExpect->before = 'before merged';
        $mergeExpect->heading = 'stuff';
        $mergeExpect->set('before', 'before confirm merged', true);
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineOverwriteConfirm() {
        $obj1 = new Labels();
        $obj1->heading = 'stuff';
        $obj1->before = 'before merged';
        $obj1->set('before', 'before confirm merged', true);

        $obj2 = new Labels();
        $obj2->after = 'after';
        $obj2->before = 'before';
        $obj2->set('help', 'help confirm', true);
        $obj2->set('before', 'before confirm', true);
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->after = 'after';
        $mergeExpect->before = 'before merged';
        $mergeExpect->heading = 'stuff';
        $mergeExpect->set('before', 'before confirm merged', true);
        $mergeExpect->set('help', 'help confirm', true);
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineTranslate() {
        // false + false = false
        $obj1 = new Labels();
        $obj1->translate = false;
        $obj2 = new Labels();
        $obj2->translate = false;
        $merge = $obj2->merge($obj1);
        $this->assertFalse($merge->translate);
        // false + true = true
        $obj2->translate = true;
        $merge = $obj2->merge($obj1);
        $this->assertTrue($merge->translate);
        // true + false = true
        $obj1->translate = true;
        $obj2->translate = false;
        $merge = $obj2->merge($obj1);
        $this->assertTrue($merge->translate);
        // true + true = true
        $obj2->translate = true;
        $merge = $obj2->merge($obj1);
        $this->assertTrue($merge->translate);
	}

    public function testTranslateSimple() {
        $trans = new MockTranslate();

        $obj = new Labels();
        $obj->accept = 'accept';
        $obj->after = 'after';
        $obj->before = 'before';
        $obj->error = ['error1', 'error2'];
        $obj->heading = 'heading';
        $obj->help = 'help';
        $obj->inner = 'placeholder';
        $obj->translate = false;
        $translated = $obj->translate($trans);
        $this->assertEquals('accept', $translated->accept);
        $this->assertEquals('after', $translated->after);
        $this->assertEquals('before', $translated->before);
        $this->assertEquals(['error1', 'error2'], $translated->error);
        $this->assertEquals('heading', $translated->heading);
        $this->assertEquals('help', $translated->help);
        $this->assertEquals('placeholder', $translated->inner);
        $obj->translate = true;
        $translated = $obj->translate($trans);
        $this->assertEquals('accept (tslt)', $translated->accept);
        $this->assertEquals('after (tslt)', $translated->after);
        $this->assertEquals('before (tslt)', $translated->before);
        $this->assertEquals(
            ['error1 (tslt)', 'error2 (tslt)'],
            $translated->error
        );
        $this->assertEquals('heading (tslt)', $translated->heading);
        $this->assertEquals('help (tslt)', $translated->help);
        $this->assertEquals('placeholder (tslt)', $translated->inner);

        // No double-translations
        $translated = $translated->translate($trans);
        $this->assertEquals('accept (tslt)', $translated->accept);
        $this->assertEquals('after (tslt)', $translated->after);
        $this->assertEquals('before (tslt)', $translated->before);
        $this->assertEquals(
            ['error1 (tslt)', 'error2 (tslt)'],
            $translated->error
        );
        $this->assertEquals('heading (tslt)', $translated->heading);
        $this->assertEquals('help (tslt)', $translated->help);
        $this->assertEquals('placeholder (tslt)', $translated->inner);

        // Null translates to null
        $obj->accept = null;
        $translated = $obj->translate($trans);
        $this->assertEquals(null, $translated->accept);
    }

}
