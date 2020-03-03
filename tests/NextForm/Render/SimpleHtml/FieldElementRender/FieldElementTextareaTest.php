<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Textarea
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Textarea
 */
class NextFormRenderSimpleHtmlFieldElementRenderTextareaTest
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
     * Check textarea element, Horizontal layout
     */
	public function testTextareaSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldTextarea();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . '<textarea id="field_1" name="field_1"></textarea>' . "\n"
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . '<textarea id="field_1" name="field_1" readonly></textarea>' . "\n"
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . '<textarea id="field_1" name="field_1"'
                . ' class="nf-valid"></textarea>' . "\n"
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . '<textarea id="field_1" name="field_1"'
                . ' class="nf-invalid"></textarea>' . "\n"
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check textarea element, Vertical layout
     */
	public function testTextareaSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldTextarea();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<textarea id="field_1" name="field_1"></textarea>' . "\n"
            )

        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<textarea id="field_1" name="field_1" readonly></textarea>' . "\n"
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<textarea id="field_1" name="field_1"'
                . ' class="nf-valid"></textarea>' . "\n"
            )

        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<textarea id="field_1" name="field_1"'
                . ' class="nf-invalid"></textarea>' . "\n"
            )

        );

        $this->runElementCases($cases, $expect);
    }

}
