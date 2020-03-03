<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderSimpleHtmlFieldElementRenderPasswordTest
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
     * Check password element, Horizontal layout
     */
	public function testPasswordSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldPassword();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="password"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="password" readonly/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="password" value="secret" readonly/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="secret"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="nf-valid"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="nf-invalid"/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check password element, Vertical layout
     */
	public function testPasswordSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldPassword();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="password" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="password" value="secret" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="secret"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"'
                . ' class="nf-valid"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"'
                . ' class="nf-invalid"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
