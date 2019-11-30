<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Html\StaticElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html;

include_once __DIR__ . '/../HtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Html\StaticElement
 */
class NextFormRenderHtmlStaticElementTest extends HtmlRenderFrame
{
    public $testObj;

    public function setUp() : void
    {
        $this->testObj = new StaticElementRender(new Html(), new Binding());
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Html());
    }

	public function testInstantiation()
    {
		$this->assertInstanceOf(
            '\Abivia\NextForm\Render\Html\StaticElementRender', $this->testObj
        );
	}

	public function testRender()
    {
        $config = json_decode('{"type": "static"}');
        $element = new Abivia\NextForm\Form\Element\StaticElement();
        $element->configure($config);

        $binding = Binding::fromElement($element);
        $fieldRender = new StaticElementRender(new Html(), $binding);
        $expect = "<div id=\"static_1_container\" data-nf-for=\"static_1\">\n"
            . "<div id=\"static_1\">\n\n"
            . "</div>\n"
            . "</div>\n";
        $block = $fieldRender->render(['access' => 'write']);
		$this->assertEquals($expect, $block->body);

        $element->setShow('hidden:foo');
        $block = $fieldRender->render(['access' => 'write']);
		$this->assertEquals($expect, $block->body);
    }

	public function testRenderHidden()
    {
        $block = $this->testObj->render(['access' => 'hide']);
		$this->assertEquals('', $block->body);
    }

    /**
     * Check the standard cases for a static element
     */
	public function testStaticSuite() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Static();
        foreach ($cases as &$case) {
            $case[0] = new StaticElementRender(new Html(), $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div', '')
                . $this->column2(
                    '<div id="static_1">' . "\n"
                    . 'This is unescaped text with &lt;stuff&gt;!' . "\n"
                    . '</div>' . "\n"
                ),
                ['id' => 'static_1']
            )
        );

        $expect['head'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header', 'div', '')
                . $this->column2(
                    '<div id="static_1">' . "\n"
                    . 'This is unescaped text with &lt;stuff&gt;!' . "\n"
                    . '</div>' . "\n"
                ),
                ['id' => 'static_1']
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['head'];

        // Test view access
        $expect['view'] = $expect['head'];

        // Test hidden access
        $expect['hide'] = new Block();

        $expect['raw'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div', '')
                . $this->column2(
                    '<div id="static_1">' . "\n"
                    . 'This is <strong>raw html</strong>!' . "\n"
                    . '</div>' . "\n"
                ),
                ['id' => 'static_1']
            )
        );

        $expect['raw-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header', 'div', '')
                . $this->column2(
                    '<div id="static_1">' . "\n"
                    . 'This is <strong>raw html</strong>!' . "\n"
                    . '</div>' . "\n"
                ),
                ['id' => 'static_1']
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
