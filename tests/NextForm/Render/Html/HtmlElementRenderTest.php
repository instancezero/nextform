<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Html\HtmlElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html;

include_once __DIR__ . '/../HtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Html\HtmlElementRender
 */
class NextFormRenderHtmlHtmlElementRenderTest extends HtmlRenderFrame
{
    public $testObj;

    public function setUp() : void
    {
        $this->testObj = new HtmlElementRender(new Html(), new Binding());
    }

    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Html());
    }

	public function testInstantiation()
    {
		$this->assertInstanceOf(
            '\Abivia\NextForm\Render\Html\HtmlElementRender', $this->testObj
        );
	}

	public function testRenderNone()
    {
        $block = $this->testObj->render(['access' => 'none']);
		$this->assertEquals('', $block->body);
    }

    /**
     * Check the standard cases for a HTML element
     */
	public function testHtmlSuite()
    {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Html();
        foreach ($cases as &$case) {
            $case[0] = new HtmlElementRender(new Html(), $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<p>This is some raw html &amp;</p>'
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = $expect['basic'];

        // Test hidden access
        $expect['hide'] = new Block();

        $this->runElementCases($cases, $expect);
    }

}
