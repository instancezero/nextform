<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderSimpleHtmlFieldElementRenderEmailTest
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
     * Check email element, Horizontal layout
     */
	public function testEmailSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldEmail();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="email"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1[]" type="email" multiple/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this->formGroup(
                $this->column1('Yer email')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="email"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
            . $this->formGroup(
                '<label for="field_1_confirm"'
                . ' style="display:inline-block; vertical-align:top; width:20%">'
                . 'Confirm yer email</label>' . "\n"
                . $this->column2(
                    '<input id="field_1_confirm" name="field_1_confirm"'
                    . ' type="email"/>' . "\n"
                ),
                ['id' => 'field_1_confirm']
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('Yer email')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="email"'
                    . ' value="snafu@fub.ar" readonly/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="snafu@fub.ar"/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check email element, Vertical layout
     */
	public function testEmailSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldEmail();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="email"/>' . "\n"
            )
            . '<br/>' . "\n"
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="email" multiple/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Yer email</label>' . "\n"
                . '<input id="field_1" name="field_1" type="email"/>' . "\n"
                )
            . '<br/>' . "\n"
            . $this->formGroup(
                '<label for="field_1_confirm">Confirm yer email</label>' . "\n"
                . '<input id="field_1_confirm" name="field_1_confirm"'
                . ' type="email"/>' . "\n",
                ['id' => 'field_1_confirm']
            )
            . '<br/>' . "\n"
        );

        // Test view access
        //
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Yer email</label>' . "\n"
                . '<input id="field_1" name="field_1" type="email"'
                . ' value="snafu@fub.ar" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="snafu@fub.ar"/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
