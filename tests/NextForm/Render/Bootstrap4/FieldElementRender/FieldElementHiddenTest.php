<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderHiddenTest
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
     * Check hidden element, Horizontal layout
     */
	public function testHiddenSuiteLabels()
    {
        $this->logMethod(__METHOD__);

        $cases = RenderCaseGenerator::html_FieldHiddenLabels();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['label-none'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"'
            . ' value="the value"/>' . "\n"
        );

        $expect['label-inner'] = $expect['label-none'];
        $expect['label-before'] = $expect['label-none'];
        $expect['label-after'] = $expect['label-none'];
        $expect['label-head'] = $expect['label-none'];
        $expect['label-help'] = $expect['label-none'];
        $expect['label-all'] = $expect['label-none'];
        $expect['valid'] = $expect['label-none'];
        $expect['invalid'] = $expect['label-none'];

        $this->setMode('h');
        $this->runElementCases($cases, $expect);

        $this->setMode('v');
        $this->runElementCases($cases, $expect);
    }

    /**
     * Check hidden element, Horizontal layout
     */
	public function testHiddenSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldHidden();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Same result with view access
        $expect['view'] = $expect['basic'];

        // Same result with read access
        $expect['hide'] = $expect['basic'];

        // Scalar valued element
        $expect['scalar'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        // Array valued element
        $expect['array'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="3"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="4"/>' . "\n"
        );

        // Scalar element with sidecar
        $expect['sidecar'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        $expect['valid'] = $expect['basic'];
        $expect['invalid'] = $expect['basic'];

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check hidden element, Vertical layout
     */
	public function testHiddenSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldHidden();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Same result with view access
        $expect['view'] = $expect['basic'];

        // Same result with hidden access
        $expect['hide'] = $expect['basic'];

        // Scalar valued element
        $expect['scalar'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        // Array valued element
        $expect['array'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="3"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="4"/>' . "\n"
        );

        // Scalar element with sidecar
        $expect['sidecar'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        $expect['valid'] = $expect['basic'];
        $expect['invalid'] = $expect['basic'];

        $this->runElementCases($cases, $expect);
    }

}
