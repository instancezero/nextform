<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderSimpleHtmlFieldElementRenderRadioTest
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
     * Check radio element, Horizontal layout
     */
	public function testRadioSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldRadio();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="radio"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="radio" value="3"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="radio" value="3" readonly/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="radio"'
                    . ' class="nf-valid"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="radio"'
                    . ' class="nf-invalid"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check radio element, Horizontal layout, with labels
     */
	public function testRadioSuiteHorizontalLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldRadioLabels();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2('<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>')
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2('<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio" value="3" readonly/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>')
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2('<span>No need to fear</span>'
                    . '<input id="field_1" name="field_1" type="radio"'
                    . ' class="nf-valid" value="3"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                    . '<span>See? No problem!</span>'
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2('<span>No need to fear</span>'
                    . '<input id="field_1" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="3"/>' . "\n"
                    . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                    . '<span>See? No problem!</span>'
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check radio element, Horizontal layout, with list
     */
	public function testRadioSuiteHorizontalList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldRadioList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                    . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' value="textlist 3" data-nf-name="tl3"/>'
                    . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                    . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' value="textlist 3" checked data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"'
                    . ' readonly/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' value="textlist 2" readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' value="textlist 3" checked readonly'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                    . ' readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>'
                    . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 3"'
                    . ' checked data-nf-name="tl3"/>'
                    . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>'
                    . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 3" checked'
                    . ' data-nf-name="tl3"/>'
                    . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check radio element, Horizontal layout, with list
     */
	public function testRadioSuiteHorizontalListLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldRadioListLabels();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                    . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' value="textlist 3" checked'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>See? No problem!</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"'
                    . ' readonly/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' value="textlist 2" readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' value="textlist 3" checked readonly'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                    . ' readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>See? No problem!</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>'
                    . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 3" checked'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="nf-valid" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>See? No problem!</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>'
                    . "\n"
                    . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 3" checked'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>' . "\n"
                    . '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="nf-invalid" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div>See? No problem!</div>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check radio element, Vertical layout
     */
	public function testRadioSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldRadio();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio" value="3" readonly/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio"'
                . ' class="nf-valid"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio"'
                . ' class="nf-invalid"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check radio element, Vertical layout, with labels
     */
	public function testRadioSuiteVerticalLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldRadioLabels();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>'
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio" value="3" readonly/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>'
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio"'
                . ' class="nf-valid" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>'
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio"'
                . ' class="nf-invalid" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>'
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check radio element, Vertical layout, with list
     */
	public function testRadioSuiteVerticalList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldRadioList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" data-nf-name="tl3"/>'
                . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"'
                . ' readonly/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" readonly'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked readonly'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 3" checked'
                . ' data-nf-name="tl3"/>'
                . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 3" checked'
                . ' data-nf-name="tl3"/>'
                . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check radio element, Vertical layout, with list and labels
     */
	public function testRadioSuiteVerticalListLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldRadioListLabels();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"'
                . ' readonly/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" readonly'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked readonly'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 3" checked'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio"'
                . ' class="nf-valid" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 3" checked'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio"'
                . ' class="nf-invalid" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
