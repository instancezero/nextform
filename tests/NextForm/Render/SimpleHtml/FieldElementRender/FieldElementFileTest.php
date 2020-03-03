<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\File
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\File
 */
class NextFormRenderSimpleHtmlFieldElementRenderFileTest
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
     * Check file element, Horizontal layout
     */
	public function testFileSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldFile();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="file"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Now test validation
        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1[]" type="file"'
                    . ' accept="*.png,*.jpg" multiple/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1[]" type="text" readonly/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1[]" type="text"'
                . ' value="file1.png,file2.jpg" readonly/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="file1.png"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="file2.jpg"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="file"'
                    . ' class="nf-valid"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="file"'
                    . ' class="nf-invalid"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check file element, Vertical layout
     */
	public function testFileSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldFile();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="file"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now test validation
        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="file"'
                . ' accept="*.png,*.jpg" multiple/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="text"'
                . ' value="file1.png,file2.jpg" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        //
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="file1.png"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="file2.jpg"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="file"'
                . ' class="nf-valid"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="file"'
                . ' class="nf-invalid"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
