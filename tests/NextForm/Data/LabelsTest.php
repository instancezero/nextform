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
		$this->assertTrue($obj->get('accept') === null);
		$this->assertTrue($obj->get('after') === null);
		$this->assertTrue($obj->get('before') === null);
		$this->assertTrue($obj->get('error') === null);
		$this->assertTrue($obj->get('heading') === null);
		$this->assertTrue($obj->get('help') === null);
		$this->assertTrue($obj->get('inner') === null);
	}

	public function testConfigurationConfirm() {
        $obj = new Labels();
        $obj->configure($this->configConfirm);
		$this->assertEquals('accept', $obj->get('accept'));
		$this->assertEquals('after', $obj->get('after'));
		$this->assertEquals('before', $obj->get('before'));
		$this->assertEquals(['error1', 'error2'], $obj->get('error'));
		$this->assertEquals('heading', $obj->get('heading'));
		$this->assertEquals('help', $obj->get('help'));
		$this->assertEquals('placeholder', $obj->get('inner'));
	}

	public function testConfigurationSimple() {
        $obj = new Labels();
        $obj->configure($this->configSimple);
		$this->assertEquals('accept', $obj->get('accept'));
		$this->assertEquals('after', $obj->get('after'));
		$this->assertEquals('before', $obj->get('before'));
		$this->assertEquals('error', $obj->get('error'));
		$this->assertEquals('heading', $obj->get('heading'));
		$this->assertEquals('help', $obj->get('help'));
		$this->assertEquals('placeholder', $obj->get('inner'));
	}

	public function testConfigurationString() {
        $config = json_decode('"heading"');
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Labels();
        $obj->configure($config);
		$this->assertTrue($obj->get('accept') === null);
		$this->assertTrue($obj->get('after') === null);
		$this->assertTrue($obj->get('before') === null);
		$this->assertTrue($obj->get('error') === null);
		$this->assertEquals('heading', $obj->get('heading'));
		$this->assertTrue($obj->get('help') === null);
		$this->assertTrue($obj->get('inner') === null);
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
        $obj->set('heading', 'stuff');
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
        $obj1->set('heading', 'stuff');
        $obj2 = new Labels();
        $obj2->set('before', 'before');
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->set('heading', 'stuff');
        $mergeExpect->set('before', 'before');
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineOverwrite() {
        $obj1 = new Labels();
        $obj1->set('heading', 'stuff');
        $obj1->set('before', 'default');
        $obj2 = new Labels();
        $obj2->set('after', 'after');
        $obj2->set('before', 'before');
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->set('after', 'after');
        $mergeExpect->set('before', 'default');
        $mergeExpect->set('heading', 'stuff');
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineInsertConfirm() {
        $obj1 = new Labels();
        $obj1->set('heading', 'stuff');
        $obj1->set('before', 'before merged');
        $obj1->set('before', 'before confirm merged', true);

        $obj2 = new Labels();
        $obj2->set('after', 'after');
        $obj2->set('before', 'before');
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->set('after', 'after');
        $mergeExpect->set('before', 'before merged');
        $mergeExpect->set('heading', 'stuff');
        $mergeExpect->set('before', 'before confirm merged', true);
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineOverwriteConfirm() {
        $obj1 = new Labels();
        $obj1->set('heading', 'stuff');
        $obj1->set('before', 'before merged');
        $obj1->set('before', 'before confirm merged', true);

        $obj2 = new Labels();
        $obj2->set('after', 'after');
        $obj2->set('before', 'before');
        $obj2->set('help', 'help confirm', true);
        $obj2->set('before', 'before confirm', true);
        $merge = $obj2->merge($obj1);

        $mergeExpect = new Labels();
        $mergeExpect->set('after', 'after');
        $mergeExpect->set('before', 'before merged');
        $mergeExpect->set('heading', 'stuff');
        $mergeExpect->set('before', 'before confirm merged', true);
        $mergeExpect->set('help', 'help confirm', true);
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineTranslate() {
        // false + false = false
        $obj1 = new Labels();
        $obj1->setTranslate(false);
        $obj2 = new Labels();
        $obj2->setTranslate(false);
        $merge = $obj2->merge($obj1);
        $this->assertFalse($merge->getTranslate());
        // false + true = true
        $obj2->setTranslate(true);
        $merge = $obj2->merge($obj1);
        $this->assertTrue($merge->getTranslate());
        // true + false = true
        $obj1->setTranslate(true);
        $obj2->setTranslate(false);
        $merge = $obj2->merge($obj1);
        $this->assertTrue($merge->getTranslate());
        // true + true = true
        $obj2->setTranslate(true);
        $merge = $obj2->merge($obj1);
        $this->assertTrue($merge->getTranslate());
	}

    public function testTranslateSimple() {
        $trans = new MockTranslate();

        $obj = new Labels();
        $obj->set('accept', 'accept');
        $obj->set('after', 'after');
        $obj->set('before', 'before');
        $obj->set('error', ['error1', 'error2']);
        $obj->set('heading', 'heading');
        $obj->set('help', 'help');
        $obj->set('inner', 'placeholder');
        $obj->setTranslate(false);
        $translated = $obj->translate($trans);
        $this->assertEquals('accept', $translated->get('accept'));
        $this->assertEquals('after', $translated->get('after'));
        $this->assertEquals('before', $translated->get('before'));
        $this->assertEquals(['error1', 'error2'], $translated->get('error'));
        $this->assertEquals('heading', $translated->get('heading'));
        $this->assertEquals('help', $translated->get('help'));
        $this->assertEquals('placeholder', $translated->get('inner'));
        $obj->setTranslate(true);
        $translated = $obj->translate($trans);
        $this->assertEquals('accept (tslt)', $translated->get('accept'));
        $this->assertEquals('after (tslt)', $translated->get('after'));
        $this->assertEquals('before (tslt)', $translated->get('before'));
        $this->assertEquals(
            ['error1 (tslt)', 'error2 (tslt)'],
            $translated->get('error')
        );
        $this->assertEquals('heading (tslt)', $translated->get('heading'));
        $this->assertEquals('help (tslt)', $translated->get('help'));
        $this->assertEquals('placeholder (tslt)', $translated->get('inner'));

        // No double-translations
        $translated = $translated->translate($trans);
        $this->assertEquals('accept (tslt)', $translated->get('accept'));
        $this->assertEquals('after (tslt)', $translated->get('after'));
        $this->assertEquals('before (tslt)', $translated->get('before'));
        $this->assertEquals(
            ['error1 (tslt)', 'error2 (tslt)'],
            $translated->get('error')
        );
        $this->assertEquals('heading (tslt)', $translated->get('heading'));
        $this->assertEquals('help (tslt)', $translated->get('help'));
        $this->assertEquals('placeholder (tslt)', $translated->get('inner'));

        // Null translates to null
        $obj->set('accept', null);
        $translated = $obj->translate($trans);
        $this->assertEquals(null, $translated->get('accept'));
    }

}
