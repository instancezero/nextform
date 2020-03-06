<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderButtonTest
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
	public function testButtonSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldButton();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="button"'
                    . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
                ),
                ['invalid' => '']
            )
        );

        $expect['reset'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="reset"'
                    . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
                ),
                ['invalid' => '']
            )
        );

        $expect['submit'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="submit"'
                    . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
                ),
                ['invalid' => '']
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="button"'
                    . ' class="btn btn-primary is-valid" value="Ok Bob"/>' . "\n"
                ),
                ['invalid' => '']
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="button"'
                    . ' class="btn btn-primary is-invalid" value="Ok Bob"/>' . "\n"
                ),
                ['invalid' => '']
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Vertical layout
     */
	public function testButtonSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldButton();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="button"'
                . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
            )
        );

        $expect['reset'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="reset"'
                . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
            )
        );

        $expect['submit'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="submit"'
                . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="button"'
                . ' class="btn btn-primary is-valid" value="Ok Bob"/>' . "\n"
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="button"'
                . ' class="btn btn-primary is-invalid" value="Ok Bob"/>' . "\n"
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
