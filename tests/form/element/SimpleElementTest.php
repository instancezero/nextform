<?php

require_once __DIR__ . '/../../test-tools/MockTranslate.php';

use Abivia\NextForm\Form\Element\Element;
use Abivia\NextForm\Form\Element\SimpleElement;
use Illuminate\Contracts\Translation\Translator as Translator;

class ConcreteSimpleElement extends SimpleElement
{

    public function __construct() {
        parent::__construct();
    }

}

class MockForm
{
    public function registerElement($that)
    {

    }
}

/**
 * @covers Abivia\NextForm\Form\Element\SimpleElement
 */
class FormSimpleElementTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test the access methods for enabled
     */
	public function testConfigure() {
        $obj = new ConcreteSimpleElement();
        $form = new MockForm();
        $json = '{"name":"foo"}';
        $config = json_decode($json);
        $this->assertTrue($obj->configure($config, ['_form' => $form]));
		$this->assertTrue($obj === $obj->setEnabled(false));
        $this->assertFalse($obj->getEnabled());
    }

    /**
     * Test the access methods for group
     */
	public function testTranslate() {
        $obj = new ConcreteSimpleElement();
        $this->assertTrue($obj->getTranslate());
        $obj-> setTranslate(false);
        $this->assertFalse($obj->getTranslate());
    }

    /**
     * Test the access methods for value without translation
     */
	public function testValue() {
        $obj = new ConcreteSimpleElement();
        $obj->setValue('hello');
        $this->assertEquals('hello', $obj->getValue());
    }

    /**
     * Test the access methods for value with translation
     */
	public function testValueTranslated() {
        $obj = new ConcreteSimpleElement();
        $obj->setValue('hello');
        $translator = new MockTranslate();
        $obj-> translate($translator);
        $this->assertEquals('hello (tslt)', $obj->getValue());
    }

}
