<?php

use Abivia\NextForm\Manager;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Element\StaticElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Html;

include_once __DIR__ . '/RendererCaseGenerator.php';


/**
 * @covers \Abivia\NextForm\Renderer\Html
 */
class NextFormRendererHtmlTest extends \PHPUnit\Framework\TestCase {

    protected $testObj;

    protected function setUp() : void
    {
        Manager::boot();
        $this->testObj = new Html();
        $this->testObj->setShow('layout:vertical:10');
    }

	public function testWriteTag()
    {
        $this->assertEquals('<input/>', $this->testObj->writeTag('input'));
        $this->assertEquals('<div>', $this->testObj->writeTag('div'));
        $this->assertEquals(
            '<div>foo</div>',
            $this->testObj->writeTag('div', null, 'foo')
        );
    }

	public function testElementHidden()
    {
        $cases = RendererCaseGenerator::html_FieldHidden();

        $result = $this->testObj->elementHidden($cases['basic'][0], 1);
        $this->assertEquals(
            '<input id="field_1" name="field_1" type="hidden" value="1"/>' . "\n",
            $result->body
        );

        $result = $this->testObj->elementHidden($cases['basic'][0], [1, 2]);
        $this->assertEquals(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="1"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="2"/>' . "\n",
            $result->body
        );

        $result = $this->testObj->elementHidden($cases['sidecar'][0], 1);
        $this->assertEquals(
            '<input id="field_2" name="field_2" type="hidden" value="1"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n",
            $result->body
        );
    }

	public function testElementHiddenList()
    {
        $cases = RendererCaseGenerator::html_FieldCheckboxList();

        $result = $this->testObj->elementHiddenList($cases['basic'][0]);
        $this->assertEquals(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n",
            $result->body
        );

        $result = $this->testObj->elementHiddenList($cases['single-value'][0]);
        $this->assertEquals(
            '<input id="field_2_opt3" name="field_2[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n",
            $result->body
        );

        $result = $this->testObj->elementHiddenList($cases['dual-value'][0]);
        $this->assertEquals(
            '<input id="field_3_opt0" name="field_3[]" type="hidden"'
            . ' value="textlist 1"/>' . "\n"
            . '<input id="field_3_opt3" name="field_3[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n",
            $result->body
        );

    }

    public function testGroupAttributes()
    {
        $config = json_decode('{"type":"static","value":"This is unescaped text with <stuff>!"}');
        $element = new StaticElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);

        $attrs = $this->testObj->groupAttributes($binding);
        $list = $attrs->getAll();
        $this->assertEquals(
            ['id' => 'static_1_container', 'data-nf-for' => 'static_1'],
            $list
        );

        $element->setDisplay(false);
        $attrs = $this->testObj->groupAttributes($binding);
        $list = $attrs->getAll();
        $this->assertEquals(
            [
                'id' => 'static_1_container',
                'data-nf-for' => 'static_1',
                'class' => 'nf-hidden'
            ],
            $list
        );

    }

    public function testGroupAttributesInCell()
    {
        $config = json_decode('{"type":"static","value":"This is unescaped text with <stuff>!"}');
        $element = new StaticElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);

        $this->testObj->setContext('inCell', true);
        $this->testObj->setContext('cellFirstElement', true);
        $attrs = $this->testObj->groupAttributes($binding);
        $list = $attrs->getAll();
        $this->assertEquals(
            [
                'id' => 'static_1_container',
                'data-nf-for' => 'static_1',
            ],
            $list
        );

        $attrs = $this->testObj->groupAttributes($binding);
        $list = $attrs->getAll();
        $this->assertEquals(
            [
                'id' => 'static_1_container',
                'data-nf-for' => 'static_1',
            ],
            $list
        );

    }

    public function testContext()
    {
        $this->testObj->setContext('test', true);
        $this->assertTrue($this->testObj->queryContext('test'));

        $this->testObj->pushContext();
        $this->assertTrue($this->testObj->queryContext('test'));

        $this->testObj->setContext('test', false);
        $this->assertFalse($this->testObj->queryContext('test'));

        $this->testObj->popContext();
        $this->assertTrue($this->testObj->queryContext('test'));
    }

    public function testContextBad()
    {
        $this->expectException('\RuntimeException');
        $this->assertTrue($this->testObj->queryContext('unset'));
    }

    public function testShowGet()
    {
        // Check the default value pathway
        $this->assertEquals('primary', $this->testObj->showGet('form', 'purpose'));

        // Set a value at the form scope
        $this->testObj->show('form', 'purpose', ['secondary']);
        $this->assertEquals('secondary', $this->testObj->showGet('form', 'purpose'));

        // Set a value at another scope
        $this->testObj->show('another', 'purpose', ['info']);
        $this->assertEquals('info', $this->testObj->showGet('another', 'purpose'));

        // Make sure the form scope is unchanged
        $this->assertEquals('secondary', $this->testObj->showGet('form', 'purpose'));

        // Check fallback for an unset scope
        $this->assertEquals('secondary', $this->testObj->showGet('unset', 'purpose'));

    }

    public function testShowGetBad()
    {
        $this->expectException('\RuntimeException');
        $this->testObj->show('form', 'purpose', 'totally-not-valid');

    }

}
