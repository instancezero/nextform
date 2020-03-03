<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderColorTest
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
     * Check checkbox element, Horizontal layout
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' class="form-control" value="#F0F0F0"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        //
        $expect['value-write'] = $expect['value'];

        // Now with view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' class="form-control" value="#F0F0F0" readonly/>' . "\n"
                )
            )
        );

        // Convert to hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="#F0F0F0"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' class="form-control is-valid"/>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="color"'
                    . ' class="form-control is-invalid"/>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Vertical layout
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
                '<input id="field_1" name="field_1" type="color"'
                . ' class="form-control"/>' . "\n"
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color"'
                . ' class="form-control" value="#F0F0F0"/>' . "\n"
            )
        );

        // Same result with explicit write access
        //
        $expect['value-write'] = $expect['value'];

        // Now with view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color"'
                . ' class="form-control" value="#F0F0F0" readonly/>' . "\n"
            )
        );

        // Convert to hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="#F0F0F0"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color"'
                . ' class="form-control is-valid"/>' . "\n"
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="color"'
                . ' class="form-control is-invalid"/>' . "\n"
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
