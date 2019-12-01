<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\ButtonElement;
use Abivia\NextForm\Render\Bootstrap4\ButtonElementRender;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/../Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4\ButtonElementRender
 * @covers \Abivia\NextForm\Render\Html\ButtonElementRenderBase
 */
class NextFormRenderBootstrap4ButtonElementRenderTest
extends Bootstrap4RenderFrame
{
    public $render;
    public $testObj;

    public function setUp() : void
    {
        $this->render = new Bootstrap4();
        $this->testObj = new ButtonElementRender(
            $this->render,
            Binding::fromElement(new ButtonElement()));
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Bootstrap4());
    }

	public function testInstantiation()
    {
		$this->assertInstanceOf(
            '\Abivia\NextForm\Render\Bootstrap4\ButtonElementRender',
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
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-success" value="I am Button!"/>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="reset"'
                    . ' class="btn btn-primary" value="I am Button!"/>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Submit button default access
        $expect['sbda'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="submit"'
                    . ' class="btn btn-primary" value="I am Button!"/>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Return to button
        $expect['bda2'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary" value="I am Button!"/>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // View access
        $expect['bva'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary" value="I am Button!" disabled/>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Hidden access
        $expect['bra'] = Block::fromString(
            '<input id="button_1" name="button_1" type="hidden"'
            . ' value="I am Button!"/>' . "\n"
        );

        // Small button... based on bda
        $expect['small'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-success btn-sm" value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Large button... based on sbda
        $expect['large'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="submit"'
                    . ' class="btn btn-primary btn-lg" value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Large warning outline
        $expect['lg-warn-out'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-outline-warning btn-lg" value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Disabled
        $expect['disabled'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary" value="I am Button!" disabled/>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Not visible
        $expect['hidden'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary" value="I am Button!"/>' . "\n"
                ),
                ['id' => 'button_1', 'classPrepend' => 'nf-hidden']
            )
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

        $cases = RenderCaseGenerator::html_ButtonLabels();
        foreach ($cases as &$case) {
            $case[0] = new ButtonElementRender($this->render, $case[0]);
        }

        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary"/>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<span class="mr-1">prefix</span>'
                . '<input id="button_1" name="button_1" type="button" class="btn btn-primary"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button" class="btn btn-primary"/>'
                . '<span>suffix</span>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button" class="btn btn-primary"/>' . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary" aria-describedby="button_1_formhelp"/>'
                    . "\n"
                    . '<small id="button_1_formhelp" class="form-text text-muted">Helpful</small>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'button_1')
                . $this->column2h(
                    '<input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary" value="inner"/>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Header', 'label', 'button_1')
                . $this->column2h(
                    '<span class="mr-1">prefix</span><input id="button_1" name="button_1" type="button"'
                    . ' class="btn btn-primary" value="inner" aria-describedby="button_1_formhelp"/>'
                    . '<span>suffix</span>'
                    . "\n"
                    . '<small id="button_1_formhelp" class="form-text text-muted">Helpful</small>'
                    . "\n"
                ),
                ['id' => 'button_1']
            )
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
                . ' class="btn btn-success" value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="reset"'
                . ' class="btn btn-primary" value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Submit button default access
        $expect['sbda'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="submit"'
                . ' class="btn btn-primary" value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Return to button, same as "bda" case but primary
        $expect['bda2'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary" value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // View access
        $expect['bva'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary" value="I am Button!" disabled/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Hidden access
        $expect['bra'] = Block::fromString(
            '<input id="button_1" name="button_1" type="hidden"'
            . ' value="I am Button!"/>' . "\n"
        );

        // Small button... based on bda
        $expect['small'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-success btn-sm" value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Large button... based on sbda
        $expect['large'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="submit"'
                . ' class="btn btn-primary btn-lg" value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Large warning outline
        $expect['lg-warn-out'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-outline-warning btn-lg" value="I am Button!"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Disabled
        $expect['disabled'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary" value="I am Button!" disabled/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Not visible
        $expect['hidden'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary" value="I am Button!"/>' . "\n",
                ['id' => 'button_1', 'classPrepend' => 'nf-hidden']
            )
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
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary"/>'
                . "\n",
                ['id' => 'button_1']
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                '<span class="mr-1">prefix</span>'
                . '<input id="button_1" name="button_1" type="button" class="btn btn-primary"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button" class="btn btn-primary"/>'
                . '<span>suffix</span>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1">Header</label>' . "\n"
                . '<input id="button_1" name="button_1" type="button" class="btn btn-primary"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary" aria-describedby="button_1_formhelp"/>'
                . "\n"
                . '<small id="button_1_formhelp" class="form-text text-muted">Helpful</small>'
                . "\n",
                ['id' => 'button_1']
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                '<input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary" value="inner"/>' . "\n",
                ['id' => 'button_1']
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                '<label for="button_1">Header</label>' . "\n"
                . '<span class="mr-1">prefix</span><input id="button_1" name="button_1" type="button"'
                . ' class="btn btn-primary" value="inner" aria-describedby="button_1_formhelp"/>'
                . '<span>suffix</span>'
                . "\n"
                . '<small id="button_1_formhelp" class="form-text text-muted">Helpful</small>'
                . "\n",
                ['id' => 'button_1']
            )
        );

        $this->runElementCases($cases, $expect);
    }

}
