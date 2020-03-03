<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderMonthTest
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
            * Check month element, Horizontal layout
     */
	public function testMonthSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldMonth();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"/>' . "\n"
                )
            )
        );

        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"'
                    . ' value="2010-10"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"'
                    . ' value="2010-10" min="1957-10" max="2099-11"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"'
                    . ' value="2010-10"'
                    . ' readonly/>' . "\n"
                )
            )
        );

        // Hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-10"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month"'
                    . ' class="form-control is-valid"/>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month"'
                    . ' class="form-control is-invalid"/>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check month element, Vertical layout
     */
	public function testMonthSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldMonth();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month"'
                . ' class="form-control"/>' . "\n"
            )
        );

        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month" class="form-control"'
                . ' value="2010-10"/>' . "\n"
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month" class="form-control"'
                . ' value="2010-10" min="1957-10" max="2099-11"/>' . "\n"
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month" class="form-control"'
                . ' value="2010-10"'
                . ' readonly/>' . "\n"
            )
        );

        // Hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-10"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month"'
                . ' class="form-control is-valid"/>' . "\n"
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month"'
                . ' class="form-control is-invalid"/>' . "\n"
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
