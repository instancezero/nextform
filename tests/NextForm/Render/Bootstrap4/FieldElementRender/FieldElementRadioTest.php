<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderRadioTest
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = ['invalid' => ''];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                ),
                $groupOptions
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                ),
                $groupOptions
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3" readonly/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                ),
                $groupOptions
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                ),
                $groupOptions
            )
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = ['invalid' => ''];

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Very Important Choice', 'div', '', 'pt-0')
                . $this->column2h(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Very Important Choice', 'div')
                . $this->column2h(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3" readonly/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Very Important Choice', 'div', '', 'pt-0')
                . $this->column2h(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Very Important Choice', 'div', '', 'pt-0')
                . $this->column2h(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = [
            'class' => 'form-group',
            'element' => 'fieldset',
            'invalid' => ''
        ];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3" checked'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1" readonly/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' readonly data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' checked readonly data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4" readonly'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden"'
            . ' value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid" value="textlist 3"'
                        . ' checked data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid" value="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
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
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid" value="textlist 3"'
                        . ' checked data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid" value="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                )
                . '</div>'. "\n",
                $groupOptions
            )
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = [
            'class' => 'form-group',
            'element' => 'fieldset',
            'invalid' => ''
        ];

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('Very Important Choice', 'legend', '', 'pt-0')
                . $this->column2h(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' checked data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '<div>See? No problem!</div>' . "\n"
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('Very Important Choice', 'legend')
                . $this->column2h(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"'
                        . ' readonly/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' readonly'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' checked readonly data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4" readonly'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3"'
                        . ' class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '<div>See? No problem!</div>' . "\n"
                )
                . '</div>' . "\n",
                $groupOptions
            )
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden"'
            . ' value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('Very Important Choice', 'legend', '', 'pt-0')
                . $this->column2h(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 3"'
                        . ' checked data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3"'
                        . ' class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '<div>See? No problem!</div>' . "\n"
                )
                . '</div>'. "\n",
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('Very Important Choice', 'legend', '', 'pt-0')
                . $this->column2h(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 3"'
                        . ' checked data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '<div>See? No problem!</div>' . "\n"
                )
                . '</div>'. "\n",
                $groupOptions
            )
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = [];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="radio"'
                    . ' class="form-check-input"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="radio"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="radio"'
                    . ' class="form-check-input" value="3" readonly/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="radio"'
                    . ' class="form-check-input is-valid"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->formCheck(
                    '<input id="field_1" name="field_1" type="radio"'
                    . ' class="form-check-input is-invalid"/>' . "\n"
                    . '<label for="field_1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = [];

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3" readonly/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n",
                        $checkOptions
                    )
                    . '<span>See? No problem!</span>' . "\n"
                ),
                $groupOptions
            )
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = ['element' => 'fieldset'];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 3" checked'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 1" readonly/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 2"'
                    . ' readonly data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' checked readonly data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="form-check-input" value="textlist 4" readonly'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden"'
            . ' value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="form-check-input is-valid"'
                    . ' value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="form-check-input is-valid"'
                    . ' value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="form-check-input is-valid"'
                    . ' value="textlist 3" checked'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="form-check-input is-valid"'
                    . ' value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'div')
                . $this->formCheck(
                    '<input id="field_1_opt0" name="field_1" type="radio"'
                    . ' class="form-check-input is-invalid"'
                    . ' value="textlist 1"/>' . "\n"
                    . '<label for="field_1_opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt1" name="field_1" type="radio"'
                    . ' class="form-check-input is-invalid"'
                    . ' value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                    . '<label for="field_1_opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt2" name="field_1" type="radio"'
                    . ' class="form-check-input is-invalid"'
                    . ' value="textlist 3" checked'
                    . ' data-nf-name="tl3"/>' . "\n"
                    . '<label for="field_1_opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $checkOptions
                )
                . $this->formCheck(
                    '<input id="field_1_opt3" name="field_1" type="radio"'
                    . ' class="form-check-input is-invalid"'
                    . ' value="textlist 4"'
                    . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field_1_opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $checkOptions
                ),
                $groupOptions
            )
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

        $checkOptions = ['invalid' => ''];
        $groupOptions = ['element' => 'fieldset'];

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3" checked'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '<div>See? No problem!</div>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>'
                        . $this->formCheck(
                            '<input id="field_1_opt0" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 1"'
                            . ' readonly/>' . "\n"
                            . '<label for="field_1_opt0" class="form-check-label">'
                            . 'textlist 1</label>' . "\n",
                        $checkOptions
                        )
                        . $this->formCheck(
                            '<input id="field_1_opt1" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 2"'
                            . ' readonly data-nf-group="[&quot;grpX&quot;]"/>'
                            . "\n"
                            . '<label for="field_1_opt1" class="form-check-label">'
                            . 'textlist 2</label>' . "\n",
                        $checkOptions
                        )
                        . $this->formCheck(
                            '<input id="field_1_opt2" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 3"'
                            . ' checked readonly data-nf-name="tl3"/>' . "\n"
                            . '<label for="field_1_opt2" class="form-check-label">'
                            . 'textlist 3</label>' . "\n",
                        $checkOptions
                        )
                        . $this->formCheck(
                            '<input id="field_1_opt3" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 4" readonly'
                            . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                            . '<label for="field_1_opt3" class="form-check-label">'
                            . 'textlist 4</label>' . "\n",
                        $checkOptions
                        )
                    . '<div>See? No problem!</div>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden"'
            . ' value="textlist 3"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 3" checked'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input is-valid"'
                        . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '<div>See? No problem!</div>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('Very Important Choice', 'div')
                . $this->column2(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 3" checked'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n",
                        $checkOptions
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input is-invalid"'
                        . ' value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n",
                        $checkOptions
                    )
                    . '<div>See? No problem!</div>' . "\n"
                ),
                $groupOptions
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
