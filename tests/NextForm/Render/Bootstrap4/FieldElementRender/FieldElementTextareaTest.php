<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Textarea
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Textarea
 */
class NextFormRenderBootstrap4FieldElementRenderTextareaTest
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
     * Check textarea element, Horizontal layout
     */
	public function testTextareaSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldTextarea();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $groupOptions = ['invalid' => ''];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<textarea id="field_1" name="field_1"></textarea>' . "\n"
                ),
                $groupOptions
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<textarea id="field_1" name="field_1" readonly></textarea>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<textarea id="field_1" name="field_1" class="is-valid">'
                    . '</textarea>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<textarea id="field_1" name="field_1" class="is-invalid">'
                    . '</textarea>' . "\n"
                ),
                $groupOptions
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check textarea element, Vertical layout
     */
	public function testTextareaSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldTextarea();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<textarea id="field_1" name="field_1"></textarea>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<textarea id="field_1" name="field_1" readonly></textarea>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<textarea id="field_1" name="field_1" class="is-valid">'
                    . '</textarea>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<textarea id="field_1" name="field_1" class="is-invalid">'
                    . '</textarea>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
