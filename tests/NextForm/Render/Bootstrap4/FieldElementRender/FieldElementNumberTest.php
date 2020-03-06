<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderNumberTest
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

        $groupOptions = ['invalid' => ''];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"'
                    . ' required data-nf-req="1"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"'
                    . ' min="-1000" max="999.45" required data-nf-req="1"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"'
                    . ' min="-1000" max="999.45" required step="1.23" data-nf-req="1"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200" readonly/>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control is-valid" value="200"/>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control is-invalid" value="200"/>' . "\n"
                ),
                $groupOptions
            )
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
                '<input id="field_1" name="field_1" type="number"'
                . ' class="form-control" value="200"/>' . "\n"
            )
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' class="form-control" value="200"'
                . ' required data-nf-req="1"/>' . "\n"
            )
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' class="form-control" value="200"'
                . ' min="-1000" max="999.45" required data-nf-req="1"/>' . "\n"
            )
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' class="form-control" value="200"'
                . ' min="-1000" max="999.45" required step="1.23" data-nf-req="1"/>' . "\n"
            )
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' class="form-control" value="200" readonly/>' . "\n"
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' class="form-control is-valid" value="200"/>' . "\n"
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' class="form-control is-invalid" value="200"/>' . "\n"
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
