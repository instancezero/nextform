<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderWeekTest
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
     * Check week element, Horizontal layout
     */
	public function testWeekSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldWeek();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' min="1957-W30" max="2099-W42"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' readonly/>' . "\n"
                )
            )
        );

        // Convert to hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-W37"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control is-valid"/>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control is-invalid"/>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check week element, Vertical layout
     */
	public function testWeekSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldWeek();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' min="1957-W30" max="2099-W42"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' readonly/>' . "\n"
                )
            )
        );

        // Convert to hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-W37"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control is-valid"/>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control is-invalid"/>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
