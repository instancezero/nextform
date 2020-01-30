<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderEmailTest
extends Bootstrap4RenderFrame
{
    public $render;

    public function setUp() : void
    {
        $this->render = new Bootstrap4();
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Bootstrap4());
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="email" class="form-control"/>'
                    . "\n"
                )
            )
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1[]" type="email"'
                    . ' class="form-control" multiple/>' . "\n"
                )
            )
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Yer email', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="email" class="form-control"/>'
                    . "\n"
                )
            )
            . $this->formGroup(
                $this->column1h('Confirm yer email', 'label', 'field_1_confirm')
                . $this->column2h(
                    '<input id="field_1_confirm" name="field_1_confirm"'
                    . ' type="email" class="form-control"/>'
                   . "\n"
                ),
                ['id' => 'field_1_confirm']
            )
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Yer email', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="email" class="form-control"'
                    . ' value="snafu@fub.ar" readonly/>'
                    . "\n"
                )
            )
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
                '<input id="field_1" name="field_1" type="email" class="form-control"/>'
                . "\n"
            )
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="email"'
                . ' class="form-control" multiple/>' . "\n"
            )
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Yer email</label>' . "\n"
                . '<input id="field_1" name="field_1" type="email" class="form-control"/>'
                . "\n"
            )
            . $this->formGroup(
                '<label for="field_1_confirm">Confirm yer email</label>' . "\n"
               . '<input id="field_1_confirm" name="field_1_confirm"'
                . ' type="email" class="form-control"/>'
               . "\n",
                ['id' => 'field_1_confirm']
            )
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Yer email</label>' . "\n"
                . '<input id="field_1" name="field_1" type="email" class="form-control"'
                . ' value="snafu@fub.ar" readonly/>'
                . "\n"
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="snafu@fub.ar"/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
