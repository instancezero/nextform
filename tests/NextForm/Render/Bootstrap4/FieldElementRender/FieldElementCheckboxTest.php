<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Checkbox
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Checkbox
 */
class NextFormRenderBootstrap4FieldElementRenderCheckboxTest
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
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3"'
                        . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        $expect['checked'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3" checked/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3"'
                        . ' readonly data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1"'
            . ' type="hidden" value="3"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" readonly/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Layout inline
        $expect['inline'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                        'form-check form-check-inline'
                    )
                )
            )
        );

        // Layout inline, appear nolabel
        $expect['inline-nolabel'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" aria-label="&lt;Stand-alone&gt; checkbox"/>' . "\n",
                        'form-check form-check-inline'
                    )
                )
            )
        );

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    '<span class="mr-1">prefix</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                    . '<span>suffix</span>' . "\n"
                )
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                )
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3" aria-describedby="field_1_formhelp"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    )
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>'
                    . "\n"
                )
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">inner</label>' . "\n"
                    )
                )
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header', 'div', '', 'pt-0')
                . $this->column2h(
                    '<span class="mr-1">prefix</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3" aria-describedby="field_1_formhelp"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">inner</label>' . "\n"
                    )
                    . '<span>suffix</span>' . "\n"
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>'
                    . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Horizontal layout, as buttons
     */
	public function testCheckboxSuiteHorizontalButton()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldCheckboxButton();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['toggle'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'. "\n"
                )
            )
        );

        $expect['label-none'] = $expect['toggle'];
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    '<span class="mr-1">prefix</span><div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
        );
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<span>suffix</span>' . "\n"
                )
            )
        );
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header', 'div')
                . $this->column2h(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'
                    . "\n"
                )
            )
        );
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox" aria-describedby="field_1_formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                )
            )
        );
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header', 'div')
                . $this->column2h(
                    '<span class="mr-1">prefix</span><div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox" aria-describedby="field_1_formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<span>suffix</span>' . "\n"
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Horizontal layout, as button list
     */
	public function testCheckboxSuiteHorizontalButtonList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldCheckboxButtonList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $listCommon = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1"/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
            . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
            . ' value="textlist 3" data-nf-name="tl3"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n";
        $listHelp = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
            . ' value="textlist 1" aria-describedby="field_1_formhelp"/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
            . ' value="textlist 2" aria-describedby="field_1_formhelp"'
            . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
            . ' value="textlist 3" aria-describedby="field_1_formhelp"'
            . ' data-nf-name="tl3"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
            . ' value="textlist 4" aria-describedby="field_1_formhelp"'
            . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n";

        $expect['toggle-list'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-sm-2 col-form-label">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listCommon
                . '</div>' . "\n"
                . '</div>' . "\n",
                ['element' => 'fieldset', 'class' => 'form-group']
            )
        );

        $expect['list-label-none'] = $expect['toggle-list'];

        $expect['list-label-before'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-sm-2 col-form-label">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . '<span class="mr-1">prefix</span>' . $listCommon
                . '</div>' . "\n"
                . '</div>' . "\n",
                ['element' => 'fieldset', 'class' => 'form-group']
            )
        );

        $expect['list-label-after'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-sm-2 col-form-label">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listCommon . '<span>suffix</span>' . "\n"
                . '</div>' . "\n"
                . '</div>' . "\n",
                ['element' => 'fieldset', 'class' => 'form-group']
            )
        );
        $expect['list-label-head'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-sm-2 col-form-label">Header</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listCommon
                . '</div>' . "\n"
                . '</div>' . "\n",
                ['element' => 'fieldset', 'class' => 'form-group']
            )
        );

        $expect['list-label-help'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-sm-2 col-form-label">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listHelp
                . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                . '</div>' . "\n"
                . '</div>' . "\n",
                ['element' => 'fieldset', 'class' => 'form-group']
            )
        );

        $expect['list-label-all'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-sm-2 col-form-label">Header</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . '<span class="mr-1">prefix</span>' . $listHelp . '<span>suffix</span>' . "\n"
                . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                . '</div>' . "\n"
                . '</div>' . "\n",
                ['element' => 'fieldset', 'class' => 'form-group']
            )
        );

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
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        $expect['write'] = $expect['basic'];

        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1" readonly/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled readonly'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3" readonly'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // One option set
        $expect['single-value'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['single-value-hide'] = Block::fromString(
            '<input id="field_1_opt3" name="field_1[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Two options set
        $expect['dual-value'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1" checked/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Two options set, view mode
        $expect['dual-value-view'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1" checked readonly/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled readonly'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3" readonly'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" checked readonly'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['dual-value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        $inlineClasses = 'form-check form-check-inline';
        $expect['inline'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $inlineClasses
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        $expect['inline-nolabel'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1" aria-label="textlist 1"/>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled aria-label="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' aria-label="textlist 3" data-nf-name="tl3"/>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' aria-label="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"'
                        . '/>' . "\n",
                        $inlineClasses
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        $inlineClasses = 'form-check col-sm-3 col-md-4';
        $expect['optionwidth'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    '<div class="row ml-0">' . "\n"
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $inlineClasses
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $inlineClasses
                    )
                    . '</div>'. "\n"
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

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
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"'
                    . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // Set a value
        $expect['checked'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3" checked/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"'
                    . ' readonly data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1"'
            . ' type="hidden" value="3"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" readonly/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Layout inline
        $expect['inline'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    'form-check form-check-inline'
                )
            )
        );

        // Layout inline, appear nolabel
        $expect['inline-nolabel'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" aria-label="&lt;Stand-alone&gt; checkbox"/>' . "\n",
                    'form-check form-check-inline'
                )
            )
        );

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                '<span class="mr-1">prefix</span>'
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
                . '<span>suffix</span>' . "\n"
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3" aria-describedby="field_1_formhelp"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
                . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>'
                . "\n"
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">inner</label>' . "\n"
                )
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header', 'div')
                . $this->column2(
                    '<span class="mr-1">prefix</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input" value="3" aria-describedby="field_1_formhelp"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">inner</label>' . "\n"
                    )
                    . '<span>suffix</span>' . "\n"
                )
                . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>'
                . "\n"
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Vertical layout, as buttons
     */
	public function testCheckboxSuiteVerticalButton()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldCheckboxButton();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['toggle'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'. "\n"
                )
            )
        );

        $expect['label-none'] = $expect['toggle'];
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<span class="mr-1">prefix</span><div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
        );
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<span>suffix</span>' . "\n"
                )
            )
        );
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>Header</div>' . "\n"
                    . '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'
                    . "\n"
                )
            )
        );
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox" aria-describedby="field_1_formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                )
            )
        );
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>Header</div>' . "\n"
                    . '<span class="mr-1">prefix</span><div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox" aria-describedby="field_1_formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<span>suffix</span>' . "\n"
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check checkbox element, Vertical layout, as button list
     */
	public function testCheckboxSuiteVerticalButtonList()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldCheckboxButtonList();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $listCommon = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt0" name="field_1[]" type="checkbox" value="textlist 1"/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
            . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
            . ' value="textlist 3" data-nf-name="tl3"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n";
        $listHelp = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
            . ' value="textlist 1" aria-describedby="field_1_formhelp"/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
            . ' value="textlist 2" aria-describedby="field_1_formhelp"'
            . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
            . ' value="textlist 3" aria-describedby="field_1_formhelp"'
            . ' data-nf-name="tl3"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
            . ' value="textlist 4" aria-describedby="field_1_formhelp"'
            . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n";
        $expect['toggle-list'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    $listCommon
                ),
                ['element' => 'fieldset']
            )
        );

        $expect['list-label-none'] = $expect['toggle-list'];
        $expect['list-label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<span class="mr-1">prefix</span>' . $listCommon
                ),
                ['element' => 'fieldset']
            )
        );
        $expect['list-label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    $listCommon . '<span>suffix</span>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );
        $expect['list-label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>Header</div>' . "\n"
                    . $listCommon
                ),
                ['element' => 'fieldset']
            )
        );
        $expect['list-label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    $listHelp
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );
        $expect['list-label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>Header</div>' . "\n"
                    . '<span class="mr-1">prefix</span>'
                    . $listHelp
                    . '<span>suffix</span>' . "\n"
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );

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
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );

        $expect['write'] = $expect['basic'];

        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" readonly/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' readonly data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // One option set
        $expect['single-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['single-value-hide'] = Block::fromString(
            '<input id="field_1_opt3" name="field_1[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Two options set
        $expect['dual-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" checked/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );

        // Two options set, view mode
        $expect['dual-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" checked readonly/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3" readonly'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" checked readonly'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
                ['element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['dual-value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        $inlineClasses = 'form-check form-check-inline';
        $expect['inline'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2" disabled'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $inlineClasses
                ),
                ['element' => 'fieldset']
            )
        );

        $expect['inline-nolabel'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" aria-label="textlist 1"/>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled aria-label="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' aria-label="textlist 3" data-nf-name="tl3"/>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' aria-label="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"'
                    . '/>' . "\n",
                    $inlineClasses
                ),
                ['element' => 'fieldset']
            )
        );

        $inlineClasses = 'form-check col-sm-3 col-md-4';
        $expect['optionwidth'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . '<div class="row ml-0">' . "\n"
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $inlineClasses
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $inlineClasses
                )
                . '</div>'. "\n"
                ,
                ['element' => 'fieldset']
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
