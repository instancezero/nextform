<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\SectionElement;
use Abivia\NextForm\Render\Html\SectionElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html;

include_once __DIR__ . '/../HtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Html\SectionElementRender
 */
class NextFormRenderHtmlSectionElementRenderTest extends HtmlRenderFrame
{
    public $testObj;

    public function setUp() : void
    {
        $this->testObj = new SectionElementRender(
            new Html(),
            Binding::fromElement(new SectionElement())
        );
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
            '\Abivia\NextForm\Render\Html\SectionElementRender', $this->testObj
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
	public function testSectionSuite()
    {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Section();
        foreach ($cases as &$case) {
            $case[0] = new SectionElementRender(new Html(), $case[0]);
        }

        $expect = [];

        $expect['empty'] = Block::fromString(
            $this->formGroup(
                '', ['element' => 'fieldset', 'id' => 'section_1', 'close' => false]
            ),
            '</fieldset>' . "\n"
        );

        // Now add a label
        $expect['label'] = Block::fromString(
            $this->formGroup(
                '<legend>This is legendary</legend>' . "\n",
                ['element' => 'fieldset', 'id' => 'section_1', 'close' => false]
            ),
            '</fieldset>' . "\n"
        );

        // Same for view access
        $expect['label-view'] = $expect['label'];

        // Nothing for hidden access
        $expect['label-hide'] = new Block();

        $this->runElementCases($cases, $expect);
    }

}
