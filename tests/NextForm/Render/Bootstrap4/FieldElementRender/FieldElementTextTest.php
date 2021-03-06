<?php

use Abivia\NextForm\Render\Bootstrap4\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\FieldElementRender\Common
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Common
 */
class NextFormRenderBootstrap4FieldElementRenderTextTest
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

        $groupOptions = ['invalid' => ''];

        $expect = [];

        // No access specification assumes write access
        $expect['default'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text" class="form-control"/>'
                    . "\n"
                ),
                $groupOptions
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text" class="form-control" readonly/>'
                    . "\n"
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-valid"/>'
                    . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-invalid"/>'
                    . "\n"
                ),
                $groupOptions
            )
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

        $groupOptions = ['invalid' => ''];

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
            $this->column1h('', 'label')
                . "<datalist id=\"field_1_list\">\n"
                . "<option value=\"textlist 1\"/>\n"
                . "<option value=\"textlist 2\" data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                . "<option value=\"textlist 3\" data-nf-name=\"tl3\"/>\n"
                . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                . "</datalist>\n"
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" list="field_1_list"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this->formGroup(
            $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" readonly/>' . "\n"
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
            $this->column1h('', 'label')
                . "<datalist id=\"field_1_list\">\n"
                . "<option value=\"textlist 1\"/>\n"
                . "<option value=\"textlist 2\" data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                . "<option value=\"textlist 3\" data-nf-name=\"tl3\"/>\n"
                . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                . "</datalist>\n"
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-valid"'
                    . ' list="field_1_list"/>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
            $this->column1h('', 'label')
                . "<datalist id=\"field_1_list\">\n"
                . "<option value=\"textlist 1\"/>\n"
                . "<option value=\"textlist 2\" data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                . "<option value=\"textlist 3\" data-nf-name=\"tl3\"/>\n"
                . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                . "</datalist>\n"
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-invalid"'
                    . ' list="field_1_list"/>' . "\n"
                ),
                $groupOptions
            )
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

        $groupOptions = ['invalid' => ''];

        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n",
                    'input-group'
                ),
                $groupOptions
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n",
                    'input-group'
                ),
                $groupOptions
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' aria-describedby="field_1_help"/>' . "\n"
                    . '<span class="w-100"></span>' . "\n",
                    [
                        'help' => '<small id="field_1_help"'
                        . ' class="form-text text-muted">Helpful</small>' . "\n"
                    ]
                ),
                $groupOptions
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' placeholder="inner"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header')
                . $this->column2h(
                    '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value" placeholder="inner"'
                    . ' aria-describedby="field_1_help"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<span class="w-100"></span>' . "\n",
                    [
                        'class' => 'input-group',
                        'help' => '<small id="field_1_help"'
                        . ' class="form-text text-muted">Helpful</small>' . "\n"
                    ]
                ),
                $groupOptions
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-valid"'
                    . ' value="the value"/>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-invalid"'
                    . ' value="the value"/>' . "\n"
                ),
                $groupOptions
            )
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

        $groupOptions = ['invalid' => ''];

        $expect = [];

        $expect['required'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1"'
                    . ' type="text" class="form-control"'
                    . ' required data-nf-req="1"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" required data-nf-req="1"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3" required data-nf-req="1"/>' . "\n"
                ),
                $groupOptions
            )
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3"'
                    . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required data-nf-req="1"/>' . "\n"
                ),
                $groupOptions
            )
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

        $expect['default'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' class="form-control"/>'
                . "\n"
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text" class="form-control" readonly/>'
                . "\n"
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' class="form-control is-valid"/>'
                . "\n"
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' class="form-control is-invalid"/>'
                . "\n"
            )
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
                $this->column1('')
                . "<datalist id=\"field_1_list\">\n"
                . "<option value=\"textlist 1\"/>\n"
                . "<option value=\"textlist 2\""
                . " data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                . "<option value=\"textlist 3\" data-nf-name=\"tl3\"/>\n"
                . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                . "</datalist>\n"
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" list="field_1_list"/>' . "\n"
                )
            )
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" readonly/>' . "\n"
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
                . "<datalist id=\"field_1_list\">\n"
                . "<option value=\"textlist 1\"/>\n"
                . "<option value=\"textlist 2\""
                . " data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                . "<option value=\"textlist 3\" data-nf-name=\"tl3\"/>\n"
                . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                . "</datalist>\n"
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-valid"'
                    . ' list="field_1_list"/>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . "<datalist id=\"field_1_list\">\n"
                . "<option value=\"textlist 1\"/>\n"
                . "<option value=\"textlist 2\""
                . " data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                . "<option value=\"textlist 3\" data-nf-name=\"tl3\"/>\n"
                . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                . "</datalist>\n"
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-invalid"'
                    . ' list="field_1_list"/>' . "\n"
                )
            )
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

        $groupOptions = ['invalid' => ''];

        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                )
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<div class="input-group">' . "\n"
                    . '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                    . '<div class="invalid-feedback">'
                    . 'This error provided by default.</div>' . "\n"
                    . '</div>' . "\n"
                ),
                $groupOptions
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<div class="input-group">' . "\n"
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<div class="invalid-feedback">'
                    . 'This error provided by default.</div>' . "\n"
                    . '</div>' . "\n"
                ),
                $groupOptions
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                )
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' aria-describedby="field_1_help"/>' . "\n"
                    . '<span class="w-100"></span>' . "\n"
                ),
                [
                    'help' => '<small id="field_1_help" class="form-text text-muted">Helpful</small>'
                        . "\n"
                ]
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' placeholder="inner"/>' . "\n"
                )
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1('Header')
                . $this->column2(
                    '<div class="input-group">' . "\n"
                    . '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value" placeholder="inner"'
                    . ' aria-describedby="field_1_help"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<span class="w-100"></span>' . "\n"
                    . '<div class="invalid-feedback">'
                    . 'This error provided by default.</div>' . "\n"
                    . '<small id="field_1_help" class="form-text text-muted">Helpful</small>'
                    . "\n"
                    . '</div>' . "\n"
                ),
                $groupOptions
            )
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-valid"'
                    . ' value="the value"/>' . "\n"
                )
            )
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control is-invalid"'
                    . ' value="the value"/>' . "\n"
                )
            )
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
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1"'
                    . ' type="text" class="form-control"'
                    . ' required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3" required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3"'
                    . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required data-nf-req="1"/>' . "\n"
                )
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
