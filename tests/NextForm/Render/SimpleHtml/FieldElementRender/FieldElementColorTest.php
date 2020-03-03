<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderSimpleHtmlFieldElementRenderColorTest
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
     * Check color element, Horizontal layout
     */
	public function testColorSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldColor();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['default'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2('<input id="field_1" name="field_1" type="color"/>' . "\n")
            )
            . "<br/>\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="color" value="#F0F0F0"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now with view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' value="#F0F0F0" readonly/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Convert to hidden for read access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="#F0F0F0"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' class="nf-valid"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' class="nf-invalid"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check color element, Vertical layout
     */
	public function testColorSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldColor();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['default'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color" value="#F0F0F0"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now with view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color" value="#F0F0F0" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Convert to hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="#F0F0F0"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color"'
                . ' class="nf-valid"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color"'
                . ' class="nf-invalid"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
