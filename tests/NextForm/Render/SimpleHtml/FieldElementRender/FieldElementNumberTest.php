<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderSimpleHtmlFieldElementRenderNumberTest
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
     * Check number element, Horizontal layout
     */
	public function testNumberSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldNumber();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="number" value="200"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' value="200"'
                    . ' required data-nf-req="1"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' value="200"'
                    . ' min="-1000" max="999.45" required data-nf-req="1"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' value="200"'
                    . ' min="-1000" max="999.45" required step="1.23" data-nf-req="1"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' value="200" readonly/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="number" class="nf-valid" value="200"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="number" class="nf-invalid" value="200"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check number element, Vertical layout
     */
	public function testNumberSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldNumber();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="number" value="200"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200"'
                . ' required data-nf-req="1"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200"'
                . ' min="-1000" max="999.45" required data-nf-req="1"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200"'
                . ' min="-1000" max="999.45" required step="1.23" data-nf-req="1"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="number" class="nf-valid" value="200"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="number" class="nf-invalid" value="200"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
