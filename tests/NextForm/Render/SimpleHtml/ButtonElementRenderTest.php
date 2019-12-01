<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\ButtonElement;
use Abivia\NextForm\Render\SimpleHtml\ButtonElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/../SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml\ButtonElementRender
 * @covers \Abivia\NextForm\Render\Html\ButtonElementRenderBase
 */
class NextFormRenderSimpleHtmlButtonElementRenderTest
extends SimpleHtmlRenderFrame
{
    public $render;
    public $testObj;

    public function setUp() : void
    {
        $this->render = new SimpleHtml();
        $this->testObj = new ButtonElementRender(
            $this->render,
            Binding::fromElement(new ButtonElement()));
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new SimpleHtml());
    }

	public function testInstantiation()
    {
		$this->assertInstanceOf(
            '\Abivia\NextForm\Render\SimpleHtml\ButtonElementRender',
            $this->testObj
        );
	}

    /**
     * Check button element, Horizontal layout
     */
	public function testButtonSuiteHorizontal()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');

        $cases = RenderCaseGenerator::html_Button();
        foreach ($cases as &$case) {
            $case[0] = new ButtonElementRender($this->render, $case[0]);
        }

        $expect = [];

        // Default access
        $expect['bda'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'button_1')
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'button_1')
                . $this->column2(
                    '<input id="button_1" name="button_1" type="reset"'
                    . ' value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Submit button default access
        $expect['sbda'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'button_1')
                . $this->column2(
                    '<input id="button_1" name="button_1" type="submit"'
                    . ' value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Return to button, same as "bda" case
        $expect['bda2'] = $expect['bda'];

        // View access
        $expect['bva'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'button_1')
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' value="I am Button!" disabled/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Hidden access
        $expect['bra'] = Block::fromString(
            '<input id="button_1" name="button_1" type="hidden"'
            . ' value="I am Button!"/>' . "\n"
        );

        // Small... same as bda
        $expect['small'] = $expect['bda'];

        // Large... same as sbda
        $expect['large'] = $expect['sbda'];

        // Large warning outline... same as bda
        $expect['lg-warn-out'] = $expect['bda'];

        // Disabled
        $expect['disabled'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'button_1')
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' value="I am Button!" disabled/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Not visible
        $expect['hidden'] = Block::fromString(
            $this->formGroup(
                $this->column1('', 'label', 'button_1')
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1', 'classAppend' => 'nf-hidden']
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check button element with labels, Horizontal layout
     */
	public function testButtonSuiteHorizontalLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('h');
        $tail = '<br/>' . "\n";

        $cases = RenderCaseGenerator::html_ButtonLabels();
        foreach ($cases as &$case) {
            $case[0] = new ButtonElementRender($this->render, $case[0]);
        }

        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1" style="display:inline-block; vertical-align:top; width:20%">'
                . '&nbsp;</label>' . "\n"
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . $tail
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1" style="display:inline-block; vertical-align:top; width:20%">'
                . '&nbsp;</label>' . "\n"
                . $this->column2(
                    '<span>prefix</span>'
                    . '<input id="button_1" name="button_1" type="button"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . $tail
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1" style="display:inline-block; vertical-align:top; width:20%">'
                . '&nbsp;</label>' . "\n"
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"/>'
                    . '<span>suffix</span>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . $tail
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1" style="display:inline-block; vertical-align:top; width:20%">'
                . 'Header</label>' . "\n"
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . $tail
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1" style="display:inline-block; vertical-align:top; width:20%">'
                . '&nbsp;</label>' . "\n"
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' aria-describedby="button_1_formhelp"/>' . "\n"
                    . '<br/>' . "\n"
                    . '<small id="button_1_formhelp">Helpful</small>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . $tail
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1" style="display:inline-block;'
                . ' vertical-align:top; width:20%">&nbsp;</label>' . "\n"
                . $this->column2(
                    '<input id="button_1" name="button_1" type="button" value="inner"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . $tail
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1" style="display:inline-block; vertical-align:top; width:20%">'
                . 'Header</label>' . "\n"
                . $this->column2(
                    '<span>prefix</span>'
                    . '<input id="button_1" name="button_1" type="button" value="inner"'
                    . ' aria-describedby="button_1_formhelp"/>'
                    . '<span>suffix</span>' . "\n"
                    . '<br/>' . "\n"
                    . '<small id="button_1_formhelp">Helpful</small>' . "\n"
                ),
                ['id' => 'button_1']
            )
            . $tail
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check button element, Vertical layout
     */
	public function testButtonSuiteVertical()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_Button();
        foreach ($cases as &$case) {
            $case[0] = new ButtonElementRender($this->render, $case[0]);
        }

        $expect = [];

        // Default access
        $expect['bda'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="reset"'
                . ' value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Submit button default access
        $expect['sbda'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="submit"'
                . ' value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Return to button, same as "bda" case
        $expect['bda2'] = $expect['bda'];

        // View access
        $expect['bva'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' value="I am Button!" disabled/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Hidden access
        $expect['bra'] = new Block();
        $expect['bra']->body = '<input id="button_1" name="button_1" type="hidden"'
            . ' value="I am Button!"/>' . "\n";

        // Small... same as bda
        $expect['small'] = $expect['bda'];

        // Large... same as sbda
        $expect['large'] = $expect['sbda'];

        // Large warning outline... same as bda
        $expect['lg-warn-out'] = $expect['bda'];

        // Disabled
        $expect['disabled'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' value="I am Button!" disabled/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Not visible
        $expect['hidden'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' value="I am Button!"/>' . "\n",
                ['id' => 'button_1', 'classAppend' => 'nf-hidden']
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

    /**
     * Check button element, Vertical layout with labels
     */
	public function testButtonSuiteVerticalLabels()
    {
        $this->logMethod(__METHOD__);
        $this->setMode('v');

        $cases = RenderCaseGenerator::html_ButtonLabels();
        foreach ($cases as &$case) {
            $case[0] = new ButtonElementRender($this->render, $case[0]);
        }

        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                '<span>prefix</span>'
                . '<input id="button_1" name="button_1" type="button"/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"/>'
                . '<span>suffix</span>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1">Header</label>' . "\n"
                . '<input id="button_1" name="button_1" type="button"/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' aria-describedby="button_1_formhelp"/>' . "\n"
                . '<br/>' . "\n"
                . '<small id="button_1_formhelp">Helpful</small>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' value="inner"/>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1">Header</label>' . "\n"
                . '<span>prefix</span><input id="button_1" name="button_1" type="button"'
                . ' value="inner" aria-describedby="button_1_formhelp"/>'
                . '<span>suffix</span>' . "\n" . '<br/>' . "\n"
                . '<small id="button_1_formhelp">Helpful</small>' . "\n",
                ['id' => 'button_1']
            )
            . '<br/>' . "\n"
        );

        $this->runElementCases($cases, $expect);
    }

}
