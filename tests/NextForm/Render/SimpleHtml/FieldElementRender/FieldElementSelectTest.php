<?php

use Abivia\NextForm\Render\SimpleHtml\FieldElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\FieldElementRender\Select
 * @covers \Abivia\NextForm\Render\Html\FieldElementRender\Select
 */
class NextFormRenderSimpleHtmlFieldElementRenderSelectTest
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
     * Check select element, Horizontal layout
     */
	public function testSelectSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldSelect();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                    . "\n"
                    . '<option value="textlist 3"'
                    . ' data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected'
                    . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                    . "\n"
                    . '<option value="textlist 3"'
                    . ' data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            ). '<br/>' . "\n"
        );

        // Same result for BS4 custom
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="hidden" value="textlist 2"/>' . "\n"
                    . '<span>textlist 2</span>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="textlist 2"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'field_1[]')
                . $this->column2(
                    '<select id="field_1" name="field_1[]" multiple>' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected'
                    . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                    . "\n"
                    . '<option value="textlist 3"'
                    . ' data-nf-name="tl3">textlist 3</option>'
                    . "\n"
                    . '<option value="textlist 4" selected data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', '')
                . $this->column2(
                    '<input id="field_1_opt0" name="field_1[]" type="hidden" value="textlist 2"/>' . "\n"
                    . '<span>textlist 2</span><br/>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="hidden" value="textlist 4"/>' . "\n"
                    . '<span>textlist 4</span><br/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['multivalue-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="textlist 2"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="textlist 4"/>' . "\n"
        );

        // Set the presentation to six rows
        $expect['sixrow'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'field_1[]')
                . $this->column2(
                    '<select id="field_1" name="field_1[]" multiple size="6">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected'
                    . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>' . "\n"
                    . '<option value="textlist 3"'
                    . ' data-nf-name="tl3">textlist 3</option>'
                    . "\n"
                    . '<option value="textlist 4" selected data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1" class="nf-valid">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                    . "\n"
                    . '<option value="textlist 3"'
                    . ' data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1" class="nf-invalid">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2"'
                    . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                    . "\n"
                    . '<option value="textlist 3"'
                    . ' data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check select element, Horizontal layout, nested
     */
	public function testSelectSuiteHorizontalNested()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_FieldSelectNested();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1">' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1">' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" selected data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // No change for the BS custom presentation
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', '')
                . $this->column2(
                    '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
                    . '<span>Sub Two Item One</span>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'field_1[]')
                . $this->column2(
                    '<select id="field_1" name="field_1[]" multiple>' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One" selected>Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" selected data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', '')
                . $this->column2(
                    '<input id="field_1_opt0" name="field_1[]" type="hidden" value="Sub One Item One"/>' . "\n"
                    . '<span>Sub One Item One</span><br/>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="hidden" value="S2I1"/>' . "\n"
                    . '<span>Sub Two Item One</span><br/>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['multivalue-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="S2I1"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden"'
            . ' value="Sub One Item One"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1" class="nf-valid">' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                $this->column1('')
                . $this->column2(
                    '<select id="field_1" name="field_1" class="nf-invalid">' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check select element, Vertical layout
     */
	public function testSelectSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldSelect();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result for BS4 custom
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value="textlist 2"/>' . "\n"
                . '<span>textlist 2</span>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="textlist 2"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1[]" multiple>' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" selected data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1_opt0" name="field_1[]" type="hidden"'
                . ' value="textlist 2"/>' . "\n"
                . '<span>textlist 2</span><br/>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="hidden" value="textlist 4"/>' . "\n"
                . '<span>textlist 4</span><br/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['multivalue-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="textlist 2"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="textlist 4"/>' . "\n"
        );

        // Set the presentation to six rows
        $expect['sixrow'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1[]" multiple size="6">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>' . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" selected data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1" class="nf-valid">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1" class="nf-invalid">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check select element, Vertical layout, nested
     */
	public function testSelectSuiteVerticalNested()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_FieldSelectNested();
        foreach ($cases as &$case) {
            $case[0] = new FieldElementRender($this->render, $case[0]);
        }

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" selected data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // No change for the BS custom presentation
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
                . '<span>Sub Two Item One</span>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1[]" multiple>' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One" selected>Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" selected data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1_opt0" name="field_1[]" type="hidden" value="Sub One Item One"/>' . "\n"
                . '<span>Sub One Item One</span><br/>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="hidden" value="S2I1"/>' . "\n"
                . '<span>Sub Two Item One</span><br/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['multivalue-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="S2I1"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden"'
            . ' value="Sub One Item One"/>' . "\n"
        );

        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1" class="nf-valid">' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['invalid'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1" class="nf-invalid">' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
