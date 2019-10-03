<?php
include_once __DIR__ . '/../test-tools/NullTranslate.php';

use \Abivia\NextForm\Data\Labels;

/**
 * @covers \Abivia\NextForm\Data\Labels
 */
class DataLabelsTest extends \PHPUnit\Framework\TestCase {

	public function testDataLabelsInstantiation() {
        $obj = new Labels();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Labels', $obj);
	}

	public function testDataLabelsInitialValues() {
        $obj = new Labels();
		$this->assertTrue($obj->after === null);
		$this->assertTrue($obj->before === null);
		$this->assertTrue($obj->confirm === null);
		$this->assertTrue($obj->error === null);
		$this->assertTrue($obj->heading === null);
		$this->assertTrue($obj->help === null);
		$this->assertTrue($obj->inner === null);
	}

	public function testDataLabelsConfiguration() {
        $config = json_decode(
            '{"after": "after","before": "before","error": "error",'
            . '"heading": "heading","help": "help","inner": "placeholder"}'
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Labels();
        $obj->configure($config);
		$this->assertEquals('after', $obj->after);
		$this->assertEquals('before', $obj->before);
		$this->assertEquals('error', $obj->error);
		$this->assertEquals('heading', $obj->heading);
		$this->assertEquals('help', $obj->help);
		$this->assertEquals('placeholder', $obj->inner);
	}

	public function testDataLabelsEmpty() {
        $obj = new Labels();
		$this->assertTrue($obj->isEmpty());
        $obj->heading = 'stuff';
		$this->assertFalse($obj->isEmpty());
	}

	public function testDataLabelsCombineSimple() {
        $obj1 = new Labels();
        $obj1->heading = 'stuff';
        $obj2 = new Labels();
        $obj2->before = 'before';
        $merge = $obj2->combine($obj1);
        $obj3 = new Labels();
        $obj3->heading = 'stuff';
        $obj3->before = 'before';
        $this->assertEquals($obj3, $merge);
	}

	public function testDataLabelsCombineOverwrite() {
        $obj1 = new Labels();
        $obj1->heading = 'stuff';
        $obj1->before = 'default';
        $obj2 = new Labels();
        $obj2->after = 'after';
        $obj2->before = 'before';
        $merge = $obj2->combine($obj1);
        $obj3 = new Labels();
        $obj3->after = 'after';
        $obj3->before = 'default';
        $obj3->heading = 'stuff';
        $this->assertEquals($obj3, $merge);
	}

	public function testDataLabelsCombineTranslate() {
        // false + false = false
        $obj1 = new Labels();
        $obj1->translate = false;
        $obj2 = new Labels();
        $obj2->translate = false;
        $merge = $obj2->combine($obj1);
        $this->assertFalse($merge->translate);
        // false + true = true
        $obj2->translate = true;
        $merge = $obj2->combine($obj1);
        $this->assertTrue($merge->translate);
        // true + false = true
        $obj1->translate = true;
        $obj2->translate = false;
        $merge = $obj2->combine($obj1);
        $this->assertTrue($merge->translate);
        // true + true = true
        $obj2->translate = true;
        $merge = $obj2->combine($obj1);
        $this->assertTrue($merge->translate);
	}

    public function testDataLabelsTranslate() {
        $obj = new Labels();
        $obj->after = 'after';
        $obj->before = 'before';
        $obj->confirm = 'confirm';
        $obj->error = 'error';
        $obj->heading = 'heading';
        $obj->help = 'help';
        $obj->inner = 'placeholder';
        $obj->translate = false;
        $trans = new NullTranslate();
        $translated = $obj->translate($trans);
        $this->assertEquals('after', $translated->after);
        $this->assertEquals('before', $translated->before);
        $this->assertEquals('confirm', $translated->confirm);
        $this->assertEquals('error', $translated->error);
        $this->assertEquals('heading', $translated->heading);
        $this->assertEquals('help', $translated->help);
        $this->assertEquals('placeholder', $translated->inner);
        $obj->translate = true;
        $translated = $obj->translate($trans);
        $this->assertEquals('after (tslt)', $translated->after);
        $this->assertEquals('before (tslt)', $translated->before);
        $this->assertEquals('confirm (tslt)', $translated->confirm);
        $this->assertEquals('error (tslt)', $translated->error);
        $this->assertEquals('heading (tslt)', $translated->heading);
        $this->assertEquals('help (tslt)', $translated->help);
        $this->assertEquals('placeholder (tslt)', $translated->inner);
        // No double-translations
        $translated = $translated->translate($trans);
        $this->assertEquals('after (tslt)', $translated->after);
        $this->assertEquals('before (tslt)', $translated->before);
        $this->assertEquals('confirm (tslt)', $translated->confirm);
        $this->assertEquals('error (tslt)', $translated->error);
        $this->assertEquals('heading (tslt)', $translated->heading);
        $this->assertEquals('help (tslt)', $translated->help);
        $this->assertEquals('placeholder (tslt)', $translated->inner);
    }

}
