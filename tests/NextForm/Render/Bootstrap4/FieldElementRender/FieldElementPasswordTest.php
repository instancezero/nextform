<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderPasswordTest
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control" value="secret" readonly/>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="secret"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control is-valid"/>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control is-invalid"/>' . "\n"
                )
            )
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
                '<input id="field_1" name="field_1" type="password"'
                . ' class="form-control"/>' . "\n"
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"'
                . ' class="form-control" readonly/>' . "\n"
            )
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"'
                . ' class="form-control" value="secret" readonly/>' . "\n"
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="secret"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"'
                . ' class="form-control is-valid"/>' . "\n"
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"'
                . ' class="form-control is-invalid"/>' . "\n"
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
