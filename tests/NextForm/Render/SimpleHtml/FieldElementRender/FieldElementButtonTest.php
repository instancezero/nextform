<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderSimpleHtmlFieldElementRenderButtonTest
extends SimpleHtmlRenderFrame
{
    public $render;

    public function setUp() : void
    {
        $this->render = new SimpleHtml();
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new SimpleHtml());
    }

    /**
     * Check checkbox element, Horizontal layout
     */
	public function testButtonSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldButton();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="button" value="Ok Bob"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $expect['reset'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="reset" value="Ok Bob"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $expect['submit'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="submit" value="Ok Bob"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Vertical layout
     */
	public function testButtonSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldButton();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['value'] = Block::fromString(
            $this->formGroup(
            '<input id="field_1" name="field_1" type="button" value="Ok Bob"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['reset'] = Block::fromString(
            $this->formGroup(
            '<input id="field_1" name="field_1" type="reset" value="Ok Bob"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['submit'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="submit" value="Ok Bob"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
