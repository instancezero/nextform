<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Checkbox
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Checkbox
 */
class NextFormRenderSimpleHtmlFieldElementRenderCheckboxTest
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
     * Check checkbox element, Horizontal layout
     */
	public function testCheckboxSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldCheckbox();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . "<br/>\n"
        );

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="checkbox" value="3"'
                    . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['checked'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="checkbox" value="3"'
                    . ' checked/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="checkbox" value="3"'
                    . ' readonly data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="checkbox" readonly/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = new Block();
        $expect['hide']->body = '<input id="field_1" name="field_1" type="hidden"/>' . "\n";

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="checkbox" value="3"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<span>prefix</span>'
                    . '<input id="field_1" name="field_1" type="checkbox" value="3"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="checkbox" value="3"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    . '<span>suffix</span>'
                )
            )
            . '<br/>' . "\n"
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="checkbox" value="3"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="checkbox" value="3"/>' . "\n"
                    . '<label for="field_1">inner</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header', 'div')
                . $this->column2(
                    '<span>prefix</span><input id="field_1" name="field_1" type="checkbox"'
                    . ' value="3"/>' . "\n"
                    . '<label for="field_1">inner</label>' . "\n"
                    . '<span>suffix</span>'
                )
            )
            . '<br/>' . "\n"
        );

        // inline
        $expect['inline'] = $expect['basic'];

        // inline nolabel
        $expect['inline-nolabel'] = $expect['basic'];

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Horizontal layout, with list
     */
	public function testCheckboxSuiteHorizontalList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldCheckboxList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' value="textlist 2" disabled'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' value="textlist 3" data-nf-name="tl3"/>'
                    . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // View access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1" readonly/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' value="textlist 2" disabled readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' value="textlist 3" readonly'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1[]" type="checkbox" value="textlist 4" readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Set a value to trigger the checked option
        $expect['single-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' value="textlist 2" disabled'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' value="textlist 3" data-nf-name="tl3"/>'
                    . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' value="textlist 4" checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Check hidden access with a single value
        $expect['single-value-hide'] = Block::fromString(
            '<input id="field_1_opt3" name="field_1[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Set a second value to trigger the checked option
        $expect['dual-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' value="textlist 1" checked/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' value="textlist 2" disabled'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' value="textlist 3" data-nf-name="tl3"/>'
                    . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' value="textlist 4" checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['dual-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1" checked readonly/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' value="textlist 2" disabled readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' value="textlist 3" readonly'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' value="textlist 4" checked readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['dual-value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Inline mode, not used in simple renderer
        $expect['inline'] = $expect['basic'];

        // Inline mode, not used in simple renderer
        $expect['inline-nolabel'] = $expect['basic'];

        // Option width, not used in simple renderer
        $expect['optionwidth'] = $expect['basic'];

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Vertical layout
     */
	public function testCheckboxSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldCheckbox();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="checkbox" value="3"'
                . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['checked'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="checkbox" value="3" checked/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="checkbox" value="3"'
                . ' readonly data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="checkbox" readonly/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                '<span>prefix</span>'
                . '<input id="field_1" name="field_1" type="checkbox" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                . '<span>suffix</span>'
            )
            . '<br/>' . "\n"
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                '<div>Header</div>' . "\n"
                . '<input id="field_1" name="field_1" type="checkbox" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field_1">inner</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                '<div>Header</div>' . "\n"
                . '<span>prefix</span><input id="field_1" name="field_1" type="checkbox"'
                . ' value="3"/>' . "\n"
                . '<label for="field_1">inner</label>' . "\n"
                . '<span>suffix</span>'
            )
            . '<br/>' . "\n"
        );

        // inline
        $expect['inline'] = $expect['basic'];

        // inline nolabel
        $expect['inline-nolabel'] = $expect['basic'];

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Vertical layout, with list
     */
	public function testCheckboxSuiteVerticalList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldCheckboxList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                . ' value="textlist 2" disabled'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                . ' value="textlist 3" data-nf-name="tl3"/>'
                . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1[]" type="checkbox" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // View access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1" readonly/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                . ' value="textlist 2" disabled readonly'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                . ' value="textlist 3" readonly'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1[]" type="checkbox" value="textlist 4" readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Set a value to trigger the checked option
        $expect['single-value'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                . ' value="textlist 2" disabled'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                . ' value="textlist 3" data-nf-name="tl3"/>'
                . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1[]" type="checkbox" value="textlist 4" checked'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Check hidden access with a single value
        $expect['single-value-hide'] = Block::fromString(
            '<input id="field_1_opt3" name="field_1[]" type="hidden" value="textlist 4"'
            . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Set a second value to trigger the checked option
        $expect['dual-value'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1" checked/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                . ' value="textlist 2" disabled'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                . ' value="textlist 3" data-nf-name="tl3"/>'
                . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1[]" type="checkbox" value="textlist 4"'
                . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['dual-value-view'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1" checked readonly/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                . ' value="textlist 2" disabled readonly'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                . ' value="textlist 3" readonly'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1[]" type="checkbox" value="textlist 4"'
                . ' checked readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['dual-value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="hidden" value="textlist 4"'
            . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Inline mode, not used in simple renderer
        $expect['inline'] = $expect['basic'];

        // Inline mode, not used in simple renderer
        $expect['inline-nolabel'] = $expect['basic'];

        // Option width, not used in simple renderer
        $expect['optionwidth'] = $expect['basic'];

        $this->runElementCases($cases, $expect);
    }

}
