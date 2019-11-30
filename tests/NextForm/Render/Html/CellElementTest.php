<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Html\CellRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html;

include_once __DIR__ . '/../HtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Html\CellRender
 */
class NextFormRenderHtmlCellRenderTest extends HtmlRenderFrame
{
    public $testObj;

    public function setUp() : void
    {
        $this->testObj = new CellRender(new Html(), new Binding());
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
            '\Abivia\NextForm\Render\Html\CellRender', $this->testObj
        );
	}

	public function testContext() {
        $this->logMethod(__METHOD__);
        $render = new Html();
        $obj = new CellRender($render, new Binding());
        $this->assertFalse($render->queryContext('inCell'));
        $block = $obj->render();
        $this->assertTrue($render->queryContext('inCell'));
        $block->close();
        $this->assertFalse($render->queryContext('inCell'));
    }

	public function testContextHidden() {
        $this->logMethod(__METHOD__);
        $render = new Html();
        $obj = new CellRender($render, new Binding());
        $this->assertFalse($render->queryContext('inCell'));
        $block = $obj->render(['access' => 'hide']);
        $this->assertTrue($render->queryContext('inCell'));
        $block->close();
        $this->assertFalse($render->queryContext('inCell'));
    }

	public function testRenderHidden()
    {
        $block = $this->testObj->render(['access' => 'hide']);
		$this->assertEquals('', $block->body);
    }

    /**
     * Check the standard cases for a static element
     */
	public function testCellSuite() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Cell();
        foreach ($cases as &$case) {
            $case[0] = new CellRender(new Html(), $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<div>' . "\n",
            '</div>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
