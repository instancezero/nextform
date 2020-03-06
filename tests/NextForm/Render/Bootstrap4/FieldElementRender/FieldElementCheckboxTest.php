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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
            )
        );

        // Layout inline, appear nolabel
        $expect['inline-nolabel'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input"'
                        . ' aria-label="&lt;Stand-alone&gt; checkbox"/>' . "\n",
                        'form-check form-check-inline'
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                        ['invalid' => '']
                    )
                    . '<span>suffix</span>' . "\n"
                ),
                ['invalid' => '']
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
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                    ),
                    [
                        'help' =>'<small id="field_1_formhelp"'
                        . ' class="form-text text-muted">Helpful</small>' . "\n",
                        'invalid' => ''
                    ]
                ),
                ['invalid' => '']
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
                        . '<label for="field_1" class="form-check-label">'
                        . 'inner</label>' . "\n"
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                        . ' class="form-check-input" value="3"'
                        . ' aria-describedby="field_1_formhelp"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . 'inner</label>' . "\n",
                        ['invalid' => '']
                    )
                    . '<span>suffix</span>' . "\n",
                    [
                        'help' => '<small id="field_1_formhelp"'
                        . ' class="form-text text-muted">Helpful</small>'
                        . "\n"
                    ]
                ),
                ['invalid' => '']
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input is-valid"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="checkbox"'
                        . ' class="form-check-input is-invalid"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                    ),
                    ['invalid' => '']
                ),
                ['invalid' => '']
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
                ),
                ['invalid' => '']
            )
        );

        $expect['label-none'] = $expect['toggle'];
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    '<span class="mr-1">prefix</span>'
                    . '<div class="btn-group btn-group-toggle"'
                    . ' data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                ),
                ['invalid' => '']
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
                ),
                ['invalid' => '']
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
                ),
                ['invalid' => '']
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
                    . '</div>' . "\n",
                    [
                        'help' => '<small id="field_1_formhelp"'
                        . ' class="form-text text-muted">Helpful</small>' . "\n"
                    ]
                ),
                ['invalid' => '']
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
                    . '<span>suffix</span>' . "\n",
                    [
                        'help' => '<small id="field_1_formhelp"'
                        . ' class="form-text text-muted">Helpful</small>' . "\n"
                    ]
                ),
                ['invalid' => '']
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="is-valid"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'. "\n"
                ),
                ['invalid' => '']
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="is-invalid"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'. "\n"
                ),
                ['invalid' => '']
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

        $listFlex = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt0" name="field_1[]" type="checkbox"{iclass}'
            . ' value="textlist 1"{help}/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
            . '{iclass} value="textlist 2"{help}'
            . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
            . '{iclass} value="textlist 3"{help} data-nf-name="tl3"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
            . '{iclass} value="textlist 4"{help} data-nf-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n"
            . '{suffix}'
            . '<div class="invalid-feedback">This error provided by default.</div>' . "\n"
            ;
        $listCommon = str_replace(
            ['{help}', '{iclass}', '{suffix}'],
            '',
            $listFlex
        );
        $listHelp = str_replace(
            ['{help}', '{iclass}', '{suffix}'],
            [' aria-describedby="field_1_formhelp"', '', ''],
            $listFlex
        );

        $groupOptions = [
            'element' => 'fieldset',
            'class' => 'form-group',
            'invalid' => ''
        ];

        $expect['toggle-list'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listCommon
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
            )
        );

        $expect['list-label-none'] = $expect['toggle-list'];

        $expect['list-label-before'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . '<span class="mr-1">prefix</span>' . $listCommon
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
            )
        );

        $listAfter = str_replace(
            ['{help}', '{iclass}', '{suffix}'],
            ['', '', '<span>suffix</span>' . "\n"],
            $listFlex
        );
        $expect['list-label-after'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listAfter
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
            )
        );
        $expect['list-label-head'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">Header</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listCommon
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
            )
        );

        $expect['list-label-help'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listHelp
                . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
            )
        );

        $listOne = str_replace(
            ['{help}', '{iclass}', '{suffix}'],
            [' aria-describedby="field_1_formhelp"', '', '<span>suffix</span>' . "\n"],
            $listFlex
        );
        $expect['list-label-all'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">Header</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . '<span class="mr-1">prefix</span>' . $listOne
                . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
            )
        );

        $listOne = str_replace(
            ['{help}', '{iclass}', '{suffix}'],
            ['', ' class="is-valid"', ''],
            $listFlex
        );
        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listOne
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
            )
        );

        $listOne = str_replace(
            ['{help}', '{iclass}', '{suffix}'],
            ['', ' class="is-invalid"', ''],
            $listFlex
        );
        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . '<legend class="col-form-label col-sm-2">&nbsp;</legend>' . "\n"
                . '<div class="col-sm-10">' . "\n"
                . $listOne
                . '</div>' . "\n"
                . '</div>' . "\n",
                $groupOptions
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

        $groupOptions = [
            'class' => 'form-group',
            'element' => 'fieldset',
            'invalid' => ''
        ];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        ['invalid' => '']
                    )
                )
                . '</div>'. "\n",
                $groupOptions
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
                        . 'textlist 1</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled readonly'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3" readonly'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        ['invalid' => '']
                    )
                )
                . '</div>'. "\n",
                $groupOptions
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
                        . 'textlist 1</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        ['invalid' => '']
                    )
                )
                . '</div>'. "\n",
                $groupOptions
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
                        . 'textlist 1</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        ['invalid' => '']
                    )
                )
                . '</div>'. "\n",
                $groupOptions
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
                        . 'textlist 1</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled readonly'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3" readonly'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" checked readonly'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        ['invalid' => '']
                    )
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        // Test hidden access
        $expect['dual-value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="hidden"'
            . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
        );

        $checkOptions = [
            'changeclass' => 'form-check form-check-inline',
            'invalid' => ''
        ];
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
                        $checkOptions

                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                )
                . '</div>'. "\n",
                $groupOptions
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
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled aria-label="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' aria-label="textlist 3" data-nf-name="tl3"/>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' aria-label="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"'
                        . '/>' . "\n",
                        $checkOptions
                    )
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        $checkOptions = [
            'changeclass' => 'form-check col-sm-3 col-md-4',
            'invalid' => ''
        ];
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
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '</div>'. "\n"
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-valid" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-valid" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-valid" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-valid" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        ['invalid' => '']
                    )
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-invalid" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-invalid" value="textlist 2"'
                        . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-invalid" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        ['invalid' => '']
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                        . ' class="form-check-input is-invalid" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        ['invalid' => '']
                    )
                )
                . '</div>'. "\n",
                $groupOptions
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

        $checkOptions = ['invalid' => ''];
        $checkInlineOptions = [
            'changeclass' => 'form-check form-check-inline',
            'invalid' => ''
        ];
        $groupOptions = [];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                    $checkInlineOptions
                ),
                $groupOptions
            )
        );

        // Layout inline, appear nolabel
        $expect['inline-nolabel'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input"'
                    . ' aria-label="&lt;Stand-alone&gt; checkbox"/>' . "\n",
                    $checkInlineOptions
                ),
                $groupOptions
            )
        );

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                )
                . '<span>suffix</span>' . "\n",
                $groupOptions
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
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // Help
        $groupWithHelpOptions = array_merge(
            $groupOptions,
            [
                'help' => '<small id="field_1_formhelp"'
                    . ' class="form-text text-muted">Helpful</small>' . "\n"
            ]
        );
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3" aria-describedby="field_1_formhelp"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupWithHelpOptions
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . 'inner</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                        . '<label for="field_1" class="form-check-label">'
                        . 'inner</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>suffix</span>' . "\n",
                    $checkOptions
                ),
                $groupWithHelpOptions
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input is-valid"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="form-check-input is-invalid"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
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
                    '<div class="btn-group btn-group-toggle"'
                    . ' data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"'
                    . ' aria-describedby="field_1_formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<div class="invalid-feedback">'
                    . 'This error provided by default.</div>' . "\n"
                    . '<small id="field_1_formhelp"'
                    . ' class="form-text text-muted">Helpful</small>' . "\n"
                ),
                ['invalid' => '']
            )
        );
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>Header</div>' . "\n"
                    . '<span class="mr-1">prefix</span>'
                    . '<div class="btn-group btn-group-toggle"'
                    . ' data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"'
                    . ' aria-describedby="field_1_formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<span>suffix</span>' . "\n"
                    . '<div class="invalid-feedback">'
                    . 'This error provided by default.</div>' . "\n"
                    . '<small id="field_1_formhelp"'
                    . ' class="form-text text-muted">Helpful</small>' . "\n"
                ),
                ['invalid' => '']
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="is-valid"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'. "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field_1" name="field_1" type="checkbox"'
                    . ' class="is-invalid"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'. "\n"
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

        $listFlex = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
            . '{iclass} value="textlist 1"{help}/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
            . '{iclass} value="textlist 2"{help} data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
            . '{iclass} value="textlist 3"{help} data-nf-name="tl3"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
            . '{iclass} value="textlist 4"{help} data-nf-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n"
            . '{suffix}'
            . '<div class="invalid-feedback">This error provided by default.</div>' . "\n"
            ;
        $listCommon = str_replace(
            ['{help}', '{iclass}', '{suffix}'],
            '',
            $listFlex
        );
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

        $groupOptions = [
            'element' => 'fieldset',
            'invalid' =>''
        ];

        $expect['toggle-list'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    $listCommon
                ),
                $groupOptions
            )
        );

        $expect['list-label-none'] = $expect['toggle-list'];
        $expect['list-label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<span class="mr-1">prefix</span>' . $listCommon
                ),
                $groupOptions
            )
        );

        $expect['list-label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    str_replace(
                        ['{help}', '{iclass}', '{suffix}'],
                        ['', '', '<span>suffix</span>' . "\n"],
                        $listFlex
                    )
                ),
                $groupOptions
            )
        );
        $expect['list-label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>Header</div>' . "\n"
                    . $listCommon
                ),
                $groupOptions
            )
        );
        $expect['list-label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    str_replace(
                        ['{help}', '{iclass}', '{suffix}'],
                        [' aria-describedby="field_1_formhelp"', '', ''],
                        $listFlex
                    )
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                ),
                $groupOptions
            )
        );
        $expect['list-label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    '<div>Header</div>' . "\n"
                    . '<span class="mr-1">prefix</span>'
                    . str_replace(
                        ['{help}', '{iclass}', '{suffix}'],
                        [
                            ' aria-describedby="field_1_formhelp"',
                            '',
                            '<span>suffix</span>' . "\n"
                        ],
                        $listFlex
                    )
                    . '<small id="field_1_formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    str_replace(
                        ['{help}', '{iclass}', '{suffix}'],
                        ['', ' class="is-valid"', ''],
                        $listFlex
                    )
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->column2(
                    str_replace(
                        ['{help}', '{iclass}', '{suffix}'],
                        ['', ' class="is-invalid"', ''],
                        $listFlex
                    )
                ),
                $groupOptions
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
                    . 'textlist 1</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['invalid' => '']
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
                    . 'textlist 1</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' readonly data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['invalid' => '']
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
                    . 'textlist 1</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['invalid' => '']
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
                    . 'textlist 1</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' checked data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['invalid' => '']
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
                    . ' class="form-check-input" value="textlist 1"'
                    . ' checked readonly/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled readonly'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3" readonly'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' checked readonly'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['invalid' => '']
                ),
                ['element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['dual-value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[]" type="hidden"'
            . ' value="textlist 1"/>' . "\n"
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
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2" disabled'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                ),
                ['element' => 'fieldset']
            )
        );

        $expect['inline-nolabel'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"'
                    . ' aria-label="textlist 1"/>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled aria-label="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' aria-label="textlist 3" data-nf-name="tl3"/>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' aria-label="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"'
                    . '/>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
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
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['changeclass' => $inlineClasses, 'invalid' => '']
                )
                . '</div>'. "\n"
                ,
                ['element' => 'fieldset']
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-valid" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-valid" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-valid" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-valid" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['invalid' => '']
                ),
                ['element' => 'fieldset']
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-invalid" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-invalid" value="textlist 2"'
                    . ' disabled data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-invalid" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    ['invalid' => '']
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1[]" type="checkbox"'
                    . ' class="form-check-input is-invalid" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    ['invalid' => '']
                ),
                ['element' => 'fieldset']
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
