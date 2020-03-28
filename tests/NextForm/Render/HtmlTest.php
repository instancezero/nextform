<?php

use Abivia\NextForm\NextForm;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Element\StaticElement;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html;

include_once __DIR__ . '/RenderCaseGenerator.php';


/**
 * @covers \Abivia\NextForm\Render\Html
 */
class NextFormRenderHtmlTest extends \PHPUnit\Framework\TestCase
{

    protected $testObj;

    protected function setUp() : void
    {
        NextForm::boot();
        $this->testObj = new Html();
        $this->testObj->setShow('layout:vertical:10');
    }

	public function testElementHidden()
    {
        $cases = RenderCaseGenerator::html_FieldHidden();

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
        $cases = RenderCaseGenerator::html_FieldCheckboxList();

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
                'class' => ['cellspace']
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

    public function testShowBad()
    {
        $this->expectException('\RuntimeException');
        $this->testObj->show('form', '$not$defined$', ['irrelevant']);
    }

    public function testShowDefault()
    {
        // Set a value at the form scope
        $this->testObj->show('form', 'purpose', ['secondary']);
        $this->assertEquals('secondary', $this->testObj->showGet('form', 'purpose'));

        // Pass null to force a default
        $this->testObj->show('form', 'purpose', null);
        $this->assertEquals('primary', $this->testObj->showGet('form', 'purpose'));

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

        // Check undefined scope
        $this->assertEquals(
            null, $this->testObj->showGet('unset', '$not$defined$')
        );

    }

    public function testShowGetBad()
    {
        $this->expectException('\RuntimeException');
        $this->testObj->show('form', 'purpose', 'totally-not-valid');

    }

    public function testRender()
    {
        $config = json_decode('{"type":"static","value":"This is unescaped text with <stuff>!"}');
        $element = new StaticElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);

        // Simple case of no access
        $block = $this->testObj->render($binding, ['access' => 'none']);
        $this->assertEquals('', $block->body);

        // Write access, we don't care much about the result,
        // short of it being a Block
        $block = $this->testObj->render($binding);
        $this->assertInstanceOf('Abivia\NextForm\Render\Block', $block);
    }

	public function testShowIsSpan() {
        $this->assertFalse($this->testObj->showIsSpan('foo'));
        $this->assertTrue($this->testObj->showIsSpan('4'));
        $this->assertTrue($this->testObj->showIsSpan('rp-xs-9'));
        $this->assertTrue($this->testObj->showIsSpan('lg-5'));
        $this->assertFalse($this->testObj->showIsSpan('b4-xx-4'));
        $this->assertTrue($this->testObj->showIsSpan('b4-xs-34'));
	}

	public function testShowParseSpan() {
        $unmatched = [
            'match' => false, 'scheme' => null, 'size' => null, 'weight' => null,
            'class' => null
        ];

        $result = $this->testObj->showParseSpan('foo');
        $this->assertEquals($unmatched, $result[0]);

        $expect = [
            'match' => true, 'scheme' => null, 'size' => '', 'weight' => 4,
            'class' => '4'
        ];
        $result = $this->testObj->showParseSpan('4');
        $this->assertEquals($expect, $result[0]);

        $expect = [
            'match' => true, 'scheme' => 'rp', 'size' => '', 'weight' => 9,
            'class' => '9'
        ];
        $result = $this->testObj->showParseSpan('rp-xs-9');
        $this->assertEquals($expect, $result[0]);

        $expect = [
            'match' => true, 'scheme' => null, 'size' => 'lg', 'weight' => 5,
            'class' => 'lg-5'
        ];
        $result = $this->testObj->showParseSpan('lg-5');
        $this->assertEquals($expect, $result[0]);

        $result = $this->testObj->showParseSpan('b4-xx-4');
        $this->assertEquals($unmatched, $result[0]);

        $expect = [
            ['match' => true, 'scheme' => 'b4', 'size' => '', 'weight' => 34,
            'class' => '34'],
            ['match' => true, 'scheme' => null, 'size' => 'lg', 'weight' => 5,
            'class' => 'lg-5'],
        ];
        $result = $this->testObj->showParseSpan('b4-xs-34:lg-5');
        $this->assertEquals($expect, $result);
	}

    public function testStart()
    {
        $attrs = new Attributes();
        $attrs->set('id', 'formid');
        $attrs->set('name', 'bob');
        $block = $this->testObj->start(['attributes' => $attrs]);
        $this->assertInstanceOf('Abivia\NextForm\Render\Block', $block);

        $block = $this->testObj->start(
            ['attributes' => $attrs]
        );
        $this->assertInstanceOf('Abivia\NextForm\Render\Block', $block);
    }

    public function testStartBad1()
    {
        $this->expectException('\RuntimeException');
        $this->testObj->start();
    }

    public function testStartBad2()
    {
        $this->expectException('\RuntimeException');
        $this->testObj->start(['attributes' => new Attributes()]);
    }

    public function testStateData()
    {
        $block = Html::stateData(['var0' => 0, 'var1' => 1]);
        $block->close();
        $this->assertEquals(
            '<input name="var0" type="hidden" value="0"/>' . "\n"
            . '<input name="var1" type="hidden" value="1"/>' . "\n",
            $block->body

        );
    }

    public function testWriteElement()
    {
        $block = $this->testObj->writeElement('div');
        $this->assertEquals('', $block->body, 'empty');

        $block = $this->testObj->writeElement('div', ['force' => true]);
        $this->assertEquals("<div>\n", $block->body, 'forced');

        $block = $this->testObj->writeElement('div', ['show' => 'cellspacing']);
        $this->assertEquals(
            "<div class=\"cellspace\">\n",
            $block->body,
            'with show'
        );

        $block = $this->testObj->writeElement(
            'div',
            ['attributes' => new Attributes('id', ['bob'])]
        );
        $this->assertEquals(
            "<div id=\"bob\">\n",
            $block->body,
            'with attrs'
        );

    }

    public function testWriteLabel()
    {
        $labels = Labels::build();
        $labels->set('inner', 'a label');
        $html = $this->testObj->writeLabel('div', $labels, 'inner');
        $this->assertEquals('<div>a label</div>', $html, 'basic');

        $html = $this->testObj->writeLabel('div', $labels, 'heading');
        $this->assertEquals('', $html, 'null');

        $html = $this->testObj->writeLabel(
            'span', $labels, 'inner', null, ['div' => 'foo']
        );
        $this->assertEquals(
            "<div class=\"foo\">\n<span>a label</span>\n</div>\n",
            $html,
            'wrapped in div'
        );

        $labels->set('error', ['item1', 'item2']);
        $html = $this->testObj->writeLabel('div', $labels, 'error');
        $this->assertEquals(
            "<div><ul>\n<li>item1</li>\n<li>item2</li>\n</ul>\n</div>",
            $html,
            'list'
        );

        $html = $this->testObj->writeLabel(
            'div', $labels, ['inner' => 'cellspacing']
        );
        $this->assertEquals('<div class="cellspace">a label</div>', $html, 'with purpose');

        // Keep this last, horizontal layout
        $this->testObj->setShow('layout:horizontal');
        $html = $this->testObj->writeLabel(
            'div', $labels, ['heading' => 'headingAttributes']
        );
        $this->assertEquals('<div>&nbsp;</div>', $html, 'null horizontal');
    }

	public function testWriteTag()
    {
        $this->assertEquals('<input/>', Html::writeTag('input'));
        $this->assertEquals('<div>', Html::writeTag('div'));
        $this->assertEquals(
            '<div>foo</div>',
            Html::writeTag('div', null, 'foo')
        );
        $this->assertEquals(
            '<div>f&lt;oo</div>',
            Html::writeTag('div', null, 'f<oo')
        );
        $this->assertEquals(
            '<div>f<oo</div>',
            Html::writeTag('div', null, 'f<oo', ['escape' => false])
        );
    }

	public function testWriteList()
    {
        $list = ['Item0', 'Item1'];

        // Stock list
        $this->assertEquals(
            "<ul>\n<li>Item0</li>\n<li>Item1</li>\n</ul>\n",
            Html::writeList($list)
        );

        // List Attributes
        $listClass = new Attributes('class', 'lc');
        $this->assertEquals(
            "<ul class=\"lc\">\n<li>Item0</li>\n<li>Item1</li>\n</ul>\n",
            Html::writeList($list, ['ul' => $listClass])
        );

        // Item Attributes
        $itemClass = new Attributes('class', 'ic');
        $this->assertEquals(
            "<ul>\n<li class=\"ic\">Item0</li>\n"
            . "<li class=\"ic\">Item1</li>\n</ul>\n",
            Html::writeList($list, ['li' => $itemClass])
        );

        // Item and list attributes
        $this->assertEquals(
            "<ul class=\"lc\">\n<li class=\"ic\">Item0</li>\n"
            . "<li class=\"ic\">Item1</li>\n</ul>\n",
            Html::writeList($list, ['li' => $itemClass, 'ul' => $listClass])
        );

        // Escaping special characters by default
        $list[1] = 'Item<em>1</em>';
        $this->assertEquals(
            "<ul>\n<li>Item0</li>\n<li>Item&lt;em&gt;1&lt;/em&gt;</li>\n</ul>\n",
            Html::writeList($list)
        );

        // Escaping disabled
        $this->assertEquals(
            "<ul>\n<li>Item0</li>\n<li>Item<em>1</em></li>\n</ul>\n",
            Html::writeList($list, ['escape' => false])
        );

    }

}
