<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderSimpleHtmlFieldElementRenderTextTest
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
     * Check text element, Horizontal layout
     */
	public function testTextSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldText();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // No access specification assumes write access
        $expect['default'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2('<input id="field_1" name="field_1" type="text"/>' . "\n")
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text" readonly/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check text element, Horizontal layout, with data list
     */
	public function testTextSuiteHorizontalDataList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldTextDataList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' list="field_1_list"/>' . "\n"
                    . "<datalist id=\"field_1_list\">\n"
                    . "<option value=\"textlist 1\"/>\n"
                    . "<option value=\"textlist 2\""
                    . " data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                    . "<option value=\"textlist 3\""
                    . " data-nf-name=\"tl3\"/>\n"
                    . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                    . "</datalist>\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text" readonly/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check text element, Horizontal layout, with labels
     */
	public function testTextSuiteHorizontalLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldTextLabels();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' value="the value"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<span>prefix</span>'
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' value="the value"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' value="the value"/>'
                    . '<span>suffix</span>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' value="the value"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' value="the value"'
                    . ' placeholder="inner"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header')
                . $this->column2(
                    '<span>prefix</span>'
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' value="the value" placeholder="inner"/>'
                    . '<span>suffix</span>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check text element, Horizontal layout, with validation
     */
	public function testTextSuiteHorizontalValidation()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldTextValidation();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['required'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="text"'
                    . ' required data-nf-req="1"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' maxlength="10" required data-nf-req="1"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' maxlength="10" minlength="3" required data-nf-req="1"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' maxlength="10" minlength="3"'
                    . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required data-nf-req="1"/>' . "\n"
                )
            )
            . "<br/>\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check text element, Vertical layout
     */
	public function testTextSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldText();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // No access specification assumes write access
        $expect['default'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check text element, Horizontal layout, with data list
     */
	public function testTextSuiteVerticalDataList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldTextDataList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' list="field_1_list"/>' . "\n"
                . '<datalist id="field_1_list">' . "\n"
                . '<option value="textlist 1"/>' . "\n"
                . '<option value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '</datalist>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check text element, Vertical layout, with labels
     */
	public function testTextSuiteVerticalLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldTextLabels();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . "<br/>\n"
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                '<span>prefix</span>'
                . '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . "<br/>\n"
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>'
                . '<span>suffix</span>' . "\n"
            )
            . "<br/>\n"
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Header</label>' . "\n"
                . '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"'
                . ' placeholder="inner"/>' . "\n"
            )
            . "<br/>\n"
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Header</label>' . "\n"
                . '<span>prefix</span>'
                . '<input id="field_1" name="field_1" type="text"'
                . ' value="the value" placeholder="inner"/>'
                . '<span>suffix</span>' . "\n"
            )
            . "<br/>\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check text element, Vertical layout, with validation
     */
	public function testTextSuiteVerticalValidation()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldTextValidation();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['required'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="text"'
                . ' required data-nf-req="1"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' maxlength="10" required data-nf-req="1"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' maxlength="10" minlength="3" required data-nf-req="1"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' maxlength="10" minlength="3"'
                . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required data-nf-req="1"/>' . "\n"
            )
            . "<br/>\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
