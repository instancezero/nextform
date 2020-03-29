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
            . ', "before.html": true'
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
        $obj = Labels::build();
		$this->assertTrue($obj->get('accept') === null);
		$this->assertTrue($obj->get('after') === null);
		$this->assertTrue($obj->get('before') === null);
		$this->assertTrue($obj->get('error') === null);
		$this->assertTrue($obj->get('heading') === null);
		$this->assertTrue($obj->get('help') === null);
		$this->assertTrue($obj->get('inner') === null);
	}

	public function testConfigurationConfirm() {
        $obj = Labels::build();
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
        $obj = Labels::build();
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
        $obj = Labels::build();
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
        $obj = Labels::build();
        $this->assertFalse($obj->configure($config));
	}

	public function testEmpty() {
        $obj = Labels::build();
		$this->assertTrue($obj->isEmpty());
        $obj->set('heading', 'stuff');
		$this->assertFalse($obj->isEmpty());

        $confirm = Labels::build();
        $confirm->configure($this->configSimple);
        $obj = Labels::build();
        $obj->setConfirm($confirm);
		$this->assertFalse($obj->isEmpty());
	}

    public function testEscaped()
    {
        $obj = Labels::build();

        // Something untouched defaults to not escaped
        $this->assertFalse($obj->isEscaped('before'));

        // No confirm labels defined? return false
        $this->assertFalse($obj->isEscaped('before', true));

        // Confirm labels but not escaped: false
        $confirm = Labels::build();
        $confirm->configure($this->configSimple);
        $obj->setConfirm($confirm);
        $this->assertFalse($obj->isEscaped('before', true));

        // Unescaped with specials
        $obj->set('heading', 'Plain <i>Heading</i>');
        $this->assertFalse($obj->isEscaped('heading'));

        // Raw fetch returns unescaped
        $this->assertEquals('Plain <i>Heading</i>', $obj->get('heading'));

        // Escaped fetch returns no specials
        $this->assertEquals(
            'Plain &lt;i&gt;Heading&lt;/i&gt;',
            $obj->getEscaped('heading')
        );

        // Assign string as escaped
        $obj->set('help', 'html <i>Help</i>', ['escaped' => true]);
        $this->assertTrue($obj->isEscaped('help'));
        $this->assertEquals('html <i>Help</i>', $obj->get('help'));
        $this->assertEquals('html <i>Help</i>', $obj->getEscaped('help'));

        $obj->set('error', ['a>b', 'b<c']);
        $this->assertFalse($obj->isEscaped('error'));
        $this->assertEquals(['a>b', 'b<c'], $obj->get('error'));
        $this->assertEquals(['a&gt;b', 'b&lt;c'], $obj->getEscaped('error'));

        $obj->set('error', ['a>b', 'b<c'], ['escaped' => true]);
        $this->assertTrue($obj->isEscaped('help'));
        $this->assertEquals(['a>b', 'b<c'], $obj->get('error'));
        $this->assertEquals(['a>b', 'b<c'], $obj->getEscaped('error'));
    }

    public function testForConfirm() {
        $obj = Labels::build();
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

    public function testGetBad()
    {
        $obj = Labels::build();
        $this->expectException('\RuntimeException');
        $obj->get('someBadLabel');
    }

	public function testGetSimple() {
        $obj = Labels::build();
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
        $obj = Labels::build();
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
        $obj = Labels::build();
        $obj->configure($this->configConfirm);
        $encoded = json_encode($obj, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/label-confirm_actual.json', $encoded);
        $compare = json_decode($encoded);
        $this->assertTrue($this->jsonCompare($this->configConfirm, $compare));
    }

    public function testJsonEncodeCollapse() {
        $obj = Labels::build();
        $obj->set('heading', 'this heading');
        $encoded = json_encode($obj);
        $this->assertEquals('"this heading"', $encoded);
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
        $obj = Labels::build();
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
        $obj = Labels::build();
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
        $obj1 = Labels::build();
        $obj1->set('heading', 'stuff');
        $obj2 = Labels::build();
        $obj2->set('before', 'before');
        $merge = $obj2->merge($obj1);

        $mergeExpect = Labels::build();
        $mergeExpect->set('heading', 'stuff');
        $mergeExpect->set('before', 'before');
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineOverwrite() {
        $obj1 = Labels::build();
        $obj1->set('heading', 'stuff');
        $obj1->set('before', 'default');
        $obj2 = Labels::build();
        $obj2->set('after', 'after');
        $obj2->set('before', 'before');
        $merge = $obj2->merge($obj1);

        $mergeExpect = Labels::build();
        $mergeExpect->set('after', 'after');
        $mergeExpect->set('before', 'default');
        $mergeExpect->set('heading', 'stuff');
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineInsertConfirm() {
        $obj1 = Labels::build();
        $obj1->set('heading', 'stuff');
        $obj1->set('before', 'before merged');
        $obj1->set('before', 'before confirm merged', ['asConfirm' => true]);

        $obj2 = Labels::build();
        $obj2->set('after', 'after');
        $obj2->set('before', 'before');
        $merge = $obj2->merge($obj1);

        $mergeExpect = Labels::build();
        $mergeExpect->set('after', 'after');
        $mergeExpect->set('before', 'before merged');
        $mergeExpect->set('heading', 'stuff');
        $mergeExpect->set('before', 'before confirm merged', ['asConfirm' => true]);
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineOverwriteConfirm() {
        $obj1 = Labels::build();
        $obj1->set('heading', 'stuff');
        $obj1->set('before', 'before merged');
        $obj1->set('before', 'before confirm merged', ['asConfirm' => true]);

        $obj2 = Labels::build();
        $obj2->set('after', 'after');
        $obj2->set('before', 'before');
        $obj2->set('help', 'help confirm', ['asConfirm' => true]);
        $obj2->set('before', 'before confirm', ['asConfirm' => true]);
        $merge = $obj2->merge($obj1);

        $mergeExpect = Labels::build();
        $mergeExpect->set('after', 'after');
        $mergeExpect->set('before', 'before merged');
        $mergeExpect->set('heading', 'stuff');
        $mergeExpect->set('before', 'before confirm merged', ['asConfirm' => true]);
        $mergeExpect->set('help', 'help confirm', ['asConfirm' => true]);
        $this->assertEquals($mergeExpect, $merge);
	}

	public function testCombineTranslate() {
        // false + false = false
        $obj1 = Labels::build();
        $obj1->setTranslate(false);
        $obj2 = Labels::build();
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

    public function testTranslateSimple()
    {
        $trans = new MockTranslate();

        $obj = Labels::build();
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
