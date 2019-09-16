<?php

use Abivia\NextForm;
//use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\ButtonElement;
use Abivia\NextForm\Element\CellElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Element\HtmlElement;
use Abivia\NextForm\Element\SectionElement;
use Abivia\NextForm\Element\StaticElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Bootstrap4;

include_once __DIR__ . '/RendererCaseGenerator.php';
include_once __DIR__ . '/RendererCaseRunner.php';
include_once __DIR__ . '/../test-tools/HtmlTestLogger.php';

/**
 * @covers \Abivia\NextForm\Renderer\Bootstrap4
 */
class FormRendererBootstrap4Test extends \PHPUnit\Framework\TestCase {
    use HtmlTestLogger;
    use RendererCaseRunner;

    protected $testObj;

    protected function column1($text, $tag = 'label', $for = 'field-1'){
        if ($text === '') {
            return '';
        }
        $text = '<' . $tag
            . ($tag == 'label' ? ' for="' . $for . '"' : '')
            . '>'
            . ($text) . '</' . $tag . '>' . "\n";
        return $text;
    }

    protected function column2($text){
        //$text = '<div class="col-sm-10">' . "\n"
        //    . $text . '</div>' . "\n";
        return $text;
    }

    protected function formCheck($body, $changeClass = '') {
        $changeClass = $changeClass === '' ? 'form-check' : $changeClass;
        $text = '<div class="' . $changeClass . '">' . "\n"
            . $body
            . '</div>' . "\n";
        return $text;
    }

    protected function formGroup($body, $changeClass = '', $element = 'div') {
        $changeClass = $changeClass === '' ? 'form-group col-sm-10' : $changeClass;
        $text = '<' . $element . ' class="' . $changeClass . '">' . "\n"
            . $body
            . '</' . $element . '>' . "\n";
        return $text;
    }

    protected function setUp() : void {
        NextForm::boot();
        $this -> testObj = new Bootstrap4();
        $this -> testObj -> setShow('layout:vertical:10');
    }

    public static function setUpBeforeClass() : void {
        self::$allHtml = '<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>' . __CLASS__  . '</title>
    {{head}}
  </head>
<body>
<div class="container">
<form id="someform" name="someform" method="post" action="http://localhost/nextform/post.php">
';
    }

    public static function tearDownAfterClass() : void {
        $obj = new Bootstrap4();
        $data = $obj -> start();
        self::$allHtml = str_replace('{{head}}', $data -> head, self::$allHtml);
        self::$allHtml .= '</div></form></body>' . implode("\n", $data -> scripts) . '</html>';
        file_put_contents(__DIR__ . '/' . __CLASS__  . '-out.html', self::$allHtml);
    }

	public function testInstantiation() {
		$this -> assertInstanceOf('\Abivia\NextForm\Renderer\Bootstrap4', $this -> testObj);
	}

	public function testStart() {
        $data = $this -> testObj -> start();
        $this -> assertEquals("<form method=\"post\">\n", $data -> body);
        $this -> assertEquals("</form>\n", $data -> post);
        $data = $this -> testObj -> start(['method' => 'put']);
        $this -> assertEquals("<form method=\"put\">\n", $data -> body);
        $data = $this -> testObj -> start(['action' => 'https://localhost/some file.php']);
        $this -> assertEquals("<form action=\"https://localhost/some file.php\" method=\"post\">\n", $data -> body);
        $data = $this -> testObj -> start(['name' => 'bad<name']);
        $this -> assertEquals("<form name=\"bad&lt;name\" method=\"post\">\n", $data -> body);
        $data = $this -> testObj -> start(['id' => 'bad<name']);
        $this -> assertEquals("<form id=\"bad&lt;name\" method=\"post\">\n", $data -> body);
    }

    /**
     * Check a button
     */
	public function testButton() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Button();
        $expect = [];

        // Default access
        $expect['bda'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-success" value="I am Button!"/>' . "\n"
            )
        );

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="reset"'
                . ' class="btn btn-primary" value="I am Button!"/>' . "\n"
            )
        );

        // Submit button default access
        $expect['sbda'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="submit"'
                . ' class="btn btn-primary" value="I am Button!"/>' . "\n"
            )
        );

        // Return to button, same as "bda" case but primary
        $expect['bda2'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-primary" value="I am Button!"/>' . "\n"
            )
        );

        // View access
        $expect['bva'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-primary" value="I am Button!" disabled/>' . "\n"
            )
        );

        // Readaccess
        $expect['bra'] = Block::fromString(
            '<input id="button-1" name="button-1" type="hidden"'
            . ' value="I am Button!"/>' . "\n"
        );

        // Small button... based on bda
        $expect['small'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-success btn-sm" value="I am Button!"/>' . "\n"
            )
        );

        // Large button... based on sbda
        $expect['large'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="submit"'
                . ' class="btn btn-primary btn-lg" value="I am Button!"/>' . "\n"
            )
        );

        // Large warning outline
        $expect['lg-warn-out'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-outline-warning btn-lg" value="I am Button!"/>' . "\n"
            )
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test a field with label options
     */
	public function testButtonLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_ButtonLabels();
        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-primary"/>'
                . "\n"
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this -> formGroup(
                '<span class="mr-1">prefix</span>'
                . '<input id="button-1" name="button-1" type="button" class="btn btn-primary"/>' . "\n"
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button" class="btn btn-primary"/>'
                . '<span>suffix</span>' . "\n"
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this -> formGroup(
                '<label for="button-1">Header</label>' . "\n"
                . '<input id="button-1" name="button-1" type="button" class="btn btn-primary"/>' . "\n"
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-primary" aria-describedby="button-1-formhelp"/>'
                . "\n"
                . '<small id="button-1-formhelp" class="form-text text-muted">Helpful</small>'
                . "\n"
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this -> formGroup(
                '<input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-primary" value="inner"/>' . "\n"
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this -> formGroup(
                '<label for="button-1">Header</label>' . "\n"
                . '<span class="mr-1">prefix</span><input id="button-1" name="button-1" type="button"'
                . ' class="btn btn-primary" value="inner" aria-describedby="button-1-formhelp"/>'
                . '<span>suffix</span>'
                . "\n"
                . '<small id="button-1-formhelp" class="form-text text-muted">Helpful</small>'
                . "\n"
            )
        );

        $this -> runCases($cases, $expect);
    }

	public function testCell() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Cell();

        $expect['basic'] = Block::fromString(
            '<div class="form-row">' . "\n",
            '</div>' . "\n"
        );
        $expect['basic'] -> onCloseDone = [$this -> testObj, 'popContext'];

        $this -> runCases($cases, $expect);
    }

	public function testCellContext() {
        $this -> logMethod(__METHOD__);
        $element = new CellElement();
        $this -> assertFalse($this -> testObj -> queryContext('inCell'));
        $this -> testObj -> render($element);
        $this -> assertTrue($this -> testObj -> queryContext('inCell'));
    }

    /**
     * @doesNotPerformAssertions
     */
	public function testSetOptions() {

        $this -> testObj -> setOptions();
    }

    /**
     * Check a field as the button types
     */
	public function testFieldButton() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldButton();

        $expect = [];
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="button"'
                . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
            )
        );

        $expect['reset'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="reset"'
                . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
            )
        );

        $expect['submit'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="submit"'
                . ' class="btn btn-primary" value="Ok Bob"/>' . "\n"
            )
        );

        $this -> runCases($cases, $expect);
   }

    /**
     * Test code generation for a checkbox element
     */
	public function testFieldCheckbox() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldCheckbox();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3"'
                    . ' data-sidecar="&quot;foo&quot;"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3"'
                    . ' readonly data-sidecar="&quot;foo&quot;"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1"'
            . ' type="hidden" value="3"'
            . ' data-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" readonly/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        // Layout inline
        $expect['inline'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n",
                    'form-check form-check-inline'
                )
            )
        );

        // Layout inline, appear nolabel
        $expect['inline-nolabel'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" aria-label="&lt;Stand-alone&gt; checkbox"/>' . "\n",
                    'form-check form-check-inline'
                )
            )
        );

        // no labels
        $expect['label-none'] = Block::fromString(
            $this -> formGroup(
                $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this -> formGroup(
                '<span class="mr-1">prefix</span>'
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this -> formGroup(
                $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
                . '<span>suffix</span>' . "\n"
            )
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('Header', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
            )
        );

        // Help
        $expect['label-help'] = Block::fromString(
            $this -> formGroup(
                $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3" aria-describedby="field-1-formhelp"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; checkbox</label>' . "\n"
                )
                . '<small id="field-1-formhelp" class="form-text text-muted">Helpful</small>'
                . "\n"
            )
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1" name="field-1" type="checkbox"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">inner</label>' . "\n"
                )
            )
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('Header', 'div')
                . $this -> column2(
                    '<span class="mr-1">prefix</span>'
                    . $this -> formCheck(
                        '<input id="field-1" name="field-1" type="checkbox"'
                        . ' class="form-check-input" value="3" aria-describedby="field-1-formhelp"/>' . "\n"
                        . '<label for="field-1" class="form-check-label">inner</label>' . "\n"
                    )
                    . '<span>suffix</span>' . "\n"
                )
                . '<small id="field-1-formhelp" class="form-text text-muted">Helpful</small>'
                . "\n"
            )
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for a checkbox styled as a button element
     */
	public function testFieldCheckboxButton() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldCheckboxButton();
        $expect = [];

        $expect['toggle'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field-1" name="field-1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'. "\n"
                )
            )
        );

        $expect['label-none'] = $expect['toggle'];
        $expect['label-before'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<span class="mr-1">prefix</span><div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field-1" name="field-1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                )
            )
        );
        $expect['label-after'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field-1" name="field-1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<span>suffix</span>' . "\n"
                )
            )
        );
        $expect['label-head'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<div>Header</div>' . "\n"
                    . '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field-1" name="field-1" type="checkbox"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>'
                    . "\n"
                )
            )
        );
        $expect['label-help'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field-1" name="field-1" type="checkbox" aria-describedby="field-1-formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<small id="field-1-formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                )
            )
        );
        $expect['label-all'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<div>Header</div>' . "\n"
                    . '<span class="mr-1">prefix</span><div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
                    . '<label class="btn btn-primary">' . "\n"
                    . '<input id="field-1" name="field-1" type="checkbox" aria-describedby="field-1-formhelp"/>' . "\n"
                    . 'CheckButton!</label>' . "\n"
                    . '</div>' . "\n"
                    . '<span>suffix</span>' . "\n"
                    . '<small id="field-1-formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                )
            )
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for a checkbox list styled as button elements
     */
	public function testFieldCheckboxButtonList() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldCheckboxButtonList();
        $expect = [];

        $listCommon = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field-1-opt0" name="field-1[]" type="checkbox" value="textlist 1"/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
            . ' value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n";
        $listHelp = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
            . ' value="textlist 1" aria-describedby="field-1-formhelp"/>' . "\n"
            . 'textlist 1</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
            . ' value="textlist 2" aria-describedby="field-1-formhelp"/>' . "\n"
            . 'textlist 2</label>' . "\n"
            . '<label class="btn btn-primary">' . "\n"
            . '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
            . ' value="textlist 3" aria-describedby="field-1-formhelp"/>' . "\n"
            . 'textlist 3</label>' . "\n"
            . '<label class="btn btn-danger">' . "\n"
            . '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
            . ' value="textlist 4" aria-describedby="field-1-formhelp"'
            . ' data-sidecar="[1,2,3,4]"/>' . "\n"
            . 'textlist 4</label>' . "\n"
            . '</div>' . "\n";
        $expect['toggle-list'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    $listCommon
                ),
                '', 'fieldset'
            )
        );

        $expect['list-label-none'] = $expect['toggle-list'];
        $expect['list-label-before'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<span class="mr-1">prefix</span>' . $listCommon
                ),
                '', 'fieldset'
            )
        );
        $expect['list-label-after'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    $listCommon . '<span>suffix</span>' . "\n"
                ),
                '', 'fieldset'
            )
        );
        $expect['list-label-head'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<div>Header</div>' . "\n"
                    . $listCommon
                ),
                '', 'fieldset'
            )
        );
        $expect['list-label-help'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    $listHelp
                    . '<small id="field-1-formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                ),
                '', 'fieldset'
            )
        );
        $expect['list-label-all'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> column2(
                    '<div>Header</div>' . "\n"
                    . '<span class="mr-1">prefix</span>'
                    . $listHelp
                    . '<span>suffix</span>' . "\n"
                    . '<small id="field-1-formhelp" class="form-text text-muted">Helpful</small>' . "\n"
                ),
                '', 'fieldset'
            )
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for a checkbox element with a list
     */
	public function testFieldCheckboxList() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldCheckboxList();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        $expect['write'] = $expect['basic'];

        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" readonly/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2" readonly/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3" readonly/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" readonly data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        // Test read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1[]" type="hidden"/>' . "\n"
        );

        // One option set
        $expect['single-value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' checked data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        // Test read access
        $expect['single-value-read'] = Block::fromString(
            '<input id="field-1-opt3" name="field-1[]" type="hidden"'
            . ' value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Two options set
        $expect['dual-value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" checked/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' checked data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        // Two options set, view mode
        $expect['dual-value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" checked readonly/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2" readonly/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3" readonly/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" checked readonly'
                    . ' data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        // Test read access
        $expect['dual-value-read'] = Block::fromString(
            '<input id="field-1-opt0" name="field-1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field-1-opt3" name="field-1[]" type="hidden"'
            . ' value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
        );

        $inlineClasses = 'form-check form-check-inline';
        $expect['inline'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n",
                    $inlineClasses
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2"/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n",
                    $inlineClasses
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3"/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n",
                    $inlineClasses
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n",
                    $inlineClasses
                ),
            '', 'fieldset')
        );

        $expect['inline-nolabel'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 1" aria-label="textlist 1"/>' . "\n",
                    $inlineClasses
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 2" aria-label="textlist 2"/>' . "\n",
                    $inlineClasses
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 3" aria-label="textlist 3"/>' . "\n",
                    $inlineClasses
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' aria-label="textlist 4"'
                    . ' data-sidecar="[1,2,3,4]"'
                    . '/>' . "\n",
                    $inlineClasses
                ),
            '', 'fieldset')
        );

        $this -> runCases($cases, $expect);
    }

   /**
    * Check field as a color element
    */
	public function testFieldColor() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldColor();

        $expect = [];

        $expect['default'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="color"'
                . ' class="form-control"/>' . "\n"
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="color"'
                . ' class="form-control" value="#F0F0F0"/>' . "\n"
            )
        );

        // Same result with explicit write access
        //
        $expect['value-write'] = $expect['value'];

        // Now with view access
        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="color"'
                . ' class="form-control" value="#F0F0F0" readonly/>' . "\n"
            )
        );

        // Convert to hidden for read access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="#F0F0F0"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

   /**
    * Check field as a date element
    */
	public function testFieldDate() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldDate();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="date" class="form-control"/>' . "\n"
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="date" class="form-control" value="2010-10-10"/>' . "\n"
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="date" class="form-control" value="2010-10-10"'
                . ' min="1957-10-08" max="2099-11-06"/>' . "\n"
            )
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="date" class="form-control" value="2010-10-10"'
                . ' readonly/>' . "\n"
            )
        );

        // Convert to hidden for read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="2010-10-10"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

   /**
    * Check field as a datetime-local element
    */
	public function testFieldDatetimeLocal() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldDatetimeLocal();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="datetime-local"'
                . ' class="form-control"/>' . "\n"
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="datetime-local"'
                . ' class="form-control" value="2010-10-10"/>' . "\n"
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="datetime-local"'
                . ' class="form-control" value="2010-10-10"'
                . ' min="1957-10-08T00:00" max="2099-11-06T14:15"/>' . "\n"
            )
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="datetime-local"'
                . ' class="form-control" value="2010-10-10"'
                . ' readonly/>' . "\n"
            )
        );

        // Convert to hidden for read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="2010-10-10"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldEmail() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldEmail();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="email" class="form-control"/>'
                . "\n"
            )
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="email"'
                . ' class="form-control" multiple/>' . "\n"
            )
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this -> formGroup(
                '<label for="field-1">Yer email</label>' . "\n"
                . '<input id="field-1" name="field-1" type="email" class="form-control"/>'
                . "\n"
            )
            . $this -> formGroup(
                '<label for="field-1-confirmation">Confirm yer email</label>' . "\n"
               . '<input id="field-1-confirmation" name="field-1-confirmation"'
                . ' type="email" class="form-control"/>'
               . "\n"
            )
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                '<label for="field-1">Yer email</label>' . "\n"
                . '<input id="field-1" name="field-1" type="email" class="form-control"'
                . ' value="snafu@fub.ar" readonly/>'
                . "\n"
            )
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="snafu@fub.ar"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldFile() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldFile();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="file" class="form-control-file"/>' . "\n"
            )
        );

        // Now test validation
        $expect['valid'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1[]" type="file" class="form-control-file"'
                . ' accept="*.png,*.jpg" multiple/>' . "\n"
            )
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="text"'
                . ' class="form-control-file" readonly/>' . "\n"
            )
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="text" class="form-control-file"'
                . ' value="file1.png,file2.jpg" readonly/>' . "\n"
            )
        );

        // Test read access
        //
        $expect['value-read'] = Block::fromString(
            '<input id="field-1-opt0" name="field-1[0]" type="hidden" value="file1.png"/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[1]" type="hidden" value="file2.jpg"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

   /**
    * Check field as a hidden element
    */
	public function testFieldHidden() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldHidden();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Same result with view access
        $expect['view'] = $expect['basic'];

        // Same result with read access
        $expect['read'] = $expect['basic'];

        $this -> runCases($cases, $expect);
    }

    /**
     * Test a hidden field with label options
     */
	public function testFieldHiddenLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldHiddenLabels();

        $expect = [];

        $expect['label-none'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"'
            . ' value="the value"/>' . "\n"
        );

        $expect['label-inner'] = $expect['label-none'];
        $expect['label-before'] = $expect['label-none'];
        $expect['label-after'] = $expect['label-none'];
        $expect['label-head'] = $expect['label-none'];
        $expect['label-help'] = $expect['label-none'];
        $expect['label-all'] = $expect['label-none'];

        $this -> runCases($cases, $expect);
    }

   /**
    * Check field as a month element
    */
	public function testFieldMonth() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldMonth();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="month" class="form-control"/>' . "\n"
            )
        );

        $expect['value'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="month" class="form-control"'
                . ' value="2010-10"/>' . "\n"
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="month" class="form-control"'
                . ' value="2010-10" min="1957-10" max="2099-11"/>' . "\n"
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="month" class="form-control"'
                . ' value="2010-10"'
                . ' readonly/>' . "\n"
            )
        );

        // Read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="2010-10"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a number
     */
	public function testFieldNumber() {
        $this -> logMethod(__METHOD__);
        $tail = "<br/>\n";
        $cases = RendererCaseGenerator::html_FieldNumber();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="number"'
                . ' class="form-control" value="200"/>' . "\n"
            )
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="number"'
                . ' class="form-control" value="200"'
                . ' required/>' . "\n"
            )
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="number"'
                . ' class="form-control" value="200"'
                . ' min="-1000" max="999.45" required/>' . "\n"
            )
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="number"'
                . ' class="form-control" value="200"'
                . ' min="-1000" max="999.45" required step="1.23"/>' . "\n"
            )
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="number"'
                . ' class="form-control" value="200" readonly/>' . "\n"
            )
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldPassword() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldPassword();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="password"'
                . ' class="form-control"/>' . "\n"
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="password"'
                . ' class="form-control" readonly/>' . "\n"
            )
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="password"'
                . ' class="form-control" value="secret" readonly/>' . "\n"
            )
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="secret"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for a radio element
     */
	public function testFieldRadio() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldRadio();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> formCheck(
                    '<input id="field-1" name="field-1" type="radio"'
                    . ' class="form-check-input"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                $this -> formCheck(
                    '<input id="field-1" name="field-1" type="radio"'
                    . ' class="form-check-input" value="3"/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> formCheck(
                    '<input id="field-1" name="field-1" type="radio"'
                    . ' class="form-check-input" value="3" readonly/>' . "\n"
                    . '<label for="field-1" class="form-check-label">'
                    . '&lt;Stand-alone&gt; radio</label>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="3"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for single radio element with labels
     */
	public function testFieldRadioLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldRadioLabels();

        $expect = [];
        $expect['labels-value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('Very Important Choice', 'div')
                . $this -> column2(
                    '<span class="mr-1">No need to fear</span>'
                    . $this -> formCheck(
                        '<input id="field-1" name="field-1" type="radio"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field-1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n"
                    )
                    . '<span>See? No problem!</span>' . "\n"
                )
            )
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('Very Important Choice', 'div')
                . $this -> column2(
                    '<span class="mr-1">No need to fear</span>'
                    . $this -> formCheck(
                        '<input id="field-1" name="field-1" type="radio"'
                        . ' class="form-check-input" value="3" readonly/>' . "\n"
                        . '<label for="field-1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n"
                    )
                    . '<span>See? No problem!</span>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['labels-value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="3"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for a radio element with a list
     */
	public function testFieldRadioList() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldRadioList();

        $expect = [];
        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 2"/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 3"/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 1"/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 2"/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 3" checked/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 4"'
                    . ' data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('', 'div')
                . $this -> formCheck(
                    '<input id="field-1-opt0" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 1" readonly/>' . "\n"
                    . '<label for="field-1-opt0" class="form-check-label">'
                    . 'textlist 1</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt1" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 2" readonly/>' . "\n"
                    . '<label for="field-1-opt1" class="form-check-label">'
                    . 'textlist 2</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt2" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 3"'
                    . ' checked readonly/>' . "\n"
                    . '<label for="field-1-opt2" class="form-check-label">'
                    . 'textlist 3</label>' . "\n"
                )
                . $this -> formCheck(
                    '<input id="field-1-opt3" name="field-1" type="radio"'
                    . ' class="form-check-input" value="textlist 4" readonly'
                    . ' data-sidecar="[1,2,3,4]"/>' . "\n"
                    . '<label for="field-1-opt3" class="form-check-label">'
                    . 'textlist 4</label>' . "\n"
                ),
            '', 'fieldset')
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1-opt2" name="field-1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for a radio element with a list and labels
     */
	public function testFieldRadioListLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldRadioListLabels();

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('Very Important Choice', 'div')
                . $this -> column2(
                    '<div>No need to fear</div>'
                    . $this -> formCheck(
                        '<input id="field-1-opt0" name="field-1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field-1-opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this -> formCheck(
                        '<input id="field-1-opt1" name="field-1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"/>' . "\n"
                        . '<label for="field-1-opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this -> formCheck(
                        '<input id="field-1-opt2" name="field-1" type="radio"'
                        . ' class="form-check-input" value="textlist 3" checked/>' . "\n"
                        . '<label for="field-1-opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this -> formCheck(
                        '<input id="field-1-opt3" name="field-1" type="radio"'
                        . ' class="form-check-input" value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field-1-opt3" class="form-check-label">textlist 4</label>' . "\n"
                    )
                    . '<div>See? No problem!</div>' . "\n"
                ),
            '', 'fieldset')
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('Very Important Choice', 'div')
                . $this -> column2(
                    '<div>No need to fear</div>'
                        . $this -> formCheck(
                            '<input id="field-1-opt0" name="field-1" type="radio"'
                            . ' class="form-check-input" value="textlist 1"'
                            . ' readonly/>' . "\n"
                            . '<label for="field-1-opt0" class="form-check-label">'
                            . 'textlist 1</label>' . "\n"
                        )
                        . $this -> formCheck(
                            '<input id="field-1-opt1" name="field-1" type="radio"'
                            . ' class="form-check-input" value="textlist 2"'
                            . ' readonly/>' . "\n"
                            . '<label for="field-1-opt1" class="form-check-label">'
                            . 'textlist 2</label>' . "\n"
                        )
                        . $this -> formCheck(
                            '<input id="field-1-opt2" name="field-1" type="radio"'
                            . ' class="form-check-input" value="textlist 3"'
                            . ' checked readonly/>' . "\n"
                            . '<label for="field-1-opt2" class="form-check-label">'
                            . 'textlist 3</label>' . "\n"
                        )
                        . $this -> formCheck(
                            '<input id="field-1-opt3" name="field-1" type="radio"'
                            . ' class="form-check-input" value="textlist 4" readonly'
                            . ' data-sidecar="[1,2,3,4]"/>' . "\n"
                            . '<label for="field-1-opt3" class="form-check-label">textlist 4</label>' . "\n"
                        )
                    . '<div>See? No problem!</div>' . "\n"
                ),
                '', 'fieldset'
            )
        );

        // Test read access
        $expect['labels-value-read'] = Block::fromString(
            '<input id="field-1-opt2" name="field-1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a range
     */
	public function testFieldRange() {
        $this -> logMethod(__METHOD__);
        $tail = "<br/>\n";
        $cases = RendererCaseGenerator::html_FieldRange();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="range"'
                . ' class="form-control" value="200"/>' . "\n"
            )
        );

        // Making the field required should have no effect
        $expect['required'] = $expect['basic'];

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="range"'
                . ' class="form-control" value="200"'
                . ' min="-1000" max="999.45"/>' . "\n"
            )
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="range"'
                . ' class="form-control" value="200"'
                . ' min="-1000" max="999.45" step="20"/>' . "\n"
            )
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];


        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                '<input id="field-1" name="field-1" type="text"'
                . ' class="form-control" value="200" readonly/>' . "\n"
            )
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a search
     */
	public function testFieldSearch() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldSearch();

        $expect = [];
        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="search"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="search"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a simple select
     */
	public function testFieldSelect() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldSelect();

        $expect = [];
        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1" class="form-control">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2">textlist 2</option>' . "\n"
                    . '<option value="textlist 3">textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="hidden" value=""/>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1" class="form-control">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
                    . '<option value="textlist 3">textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // BS4 custom
        $expect['value-bs4custom'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1" class="custom-select">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
                    . '<option value="textlist 3">textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="hidden" value="textlist 2"/>' . "\n"
                    . '<span>textlist 2</span>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="textlist 2"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1[]" class="form-control" multiple>' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
                    . '<option value="textlist 3">textlist 3</option>' . "\n"
                    . '<option value="textlist 4" selected data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1-opt0" name="field-1[]" type="hidden" value="textlist 2"/>' . "\n"
                    . '<span>textlist 2</span><br/>' . "\n"
                    . '<input id="field-1-opt1" name="field-1[]" type="hidden" value="textlist 4"/>' . "\n"
                    . '<span>textlist 4</span><br/>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['multivalue-read'] = Block::fromString(
            '<input id="field-1-opt0" name="field-1[0]" type="hidden" value="textlist 2"/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[1]" type="hidden" value="textlist 4"/>' . "\n"
        );

        // Set the presentation to six rows
        $expect['sixrow'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1[]" class="form-control" multiple size="6">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
                    . '<option value="textlist 3">textlist 3</option>' . "\n"
                    . '<option value="textlist 4" selected data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a nested select
     */
	public function testFieldSelectNested() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldSelectNested();

        $expect = [];
        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1" class="form-control">' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" data-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="hidden" value=""/>' . "\n"
                )
            )
        );

        // Test read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1" class="form-control">' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" selected data-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Test the BS custom presentation
        $expect['value-bs4custom'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1" class="custom-select">' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" selected data-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="hidden" value="S2I1"/>' . "\n"
                    . '<span>Sub Two Item One</span>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="S2I1"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<select id="field-1" name="field-1[]" class="form-control" multiple>' . "\n"
                    . '<option value="General">General</option>' . "\n"
                    . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                    . '<option value="Sub One Item One" selected>Sub One Item One</option>' . "\n"
                    . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '<optgroup label="Subgroup Two">' . "\n"
                    . '<option value="S2I1" selected data-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                    . '<option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                    . '</optgroup>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1-opt0" name="field-1[]" type="hidden" value="Sub One Item One"/>' . "\n"
                    . '<span>Sub One Item One</span><br/>' . "\n"
                    . '<input id="field-1-opt1" name="field-1[]" type="hidden" value="S2I1"/>' . "\n"
                    . '<span>Sub Two Item One</span><br/>' . "\n"
                )
            )
        );

        // Test read access
        $expect['multivalue-read'] = Block::fromString(
            '<input id="field-1-opt0" name="field-1[0]" type="hidden" value="S2I1"/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[1]" type="hidden"'
            . ' value="Sub One Item One"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a tel
     */
	public function testFieldTel() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTel();

        $expect = [];
        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="tel"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="tel"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldText() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldText();
        $expect = [];

        // No access specification assumes write access
        $expect['default'] = new Block;
        $expect['default'] -> body = $this -> formGroup(
            '<input id="field-1" name="field-1" type="text" class="form-control"/>'
            . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = new Block;
        $expect['view'] -> body = $this -> formGroup(
            '<input id="field-1" name="field-1" type="text" class="form-control" readonly/>'
            . "\n"
        );

        // Test read (less than view) access
        $expect['read'] = new Block;
        $expect['read'] -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";

        $this -> runCases($cases, $expect);
    }

	public function testFieldTextDataList() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextDataList();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" list="field-1-list"/>' . "\n"
                    . "<datalist id=\"field-1-list\">\n"
                    . "<option value=\"textlist 1\"/>\n"
                    . "<option value=\"textlist 2\"/>\n"
                    . "<option value=\"textlist 3\"/>\n"
                    . "<option value=\"textlist 4\" data-sidecar=\"[1,2,3,4]\"/>\n"
                    . "</datalist>\n"
                )
            )
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test a text field with label options
     */
	public function testFieldTextLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextLabels();
        $expect = [];
        $tail = "\n";

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body = $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" value="the value"/>' . $tail
                )
            );

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body = $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<div class="input-group">' . $tail
                    . '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                    . '</div>' . "\n"
                )
            );

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body = $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<div class="input-group">' . $tail
                    . '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n"
                    . '</div>' . "\n"
                )
            );

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = $this -> formGroup(
                $this -> column1('Header')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" value="the value"/>' . $tail
                )
            );

        // Help
        $expect['label-help'] = new Block;
        $expect['label-help'] -> body = $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' aria-describedby="field-1-help"/>' . $tail
                    . '<small id="field-1-help" class="form-text text-muted">Helpful</small>'
                    . $tail
                )
            );

        // Inner
        $expect['label-inner'] = new Block;
        $expect['label-inner'] -> body = $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' placeholder="inner"/>' . $tail
                )
            );

        // All
        $expect['label-all'] = new Block;
        $expect['label-all'] -> body = $this -> formGroup(
                $this -> column1('Header')
                . $this -> column2(
                    '<div class="input-group">' . $tail
                    . '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" value="the value" placeholder="inner"'
                    . ' aria-describedby="field-1-help"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<span class="w-100"></span>' . "\n"
                    . '<small id="field-1-help" class="form-text text-muted">Helpful</small>' . "\n"
                    . '</div>' . "\n"
                )
            );

        $this -> runCases($cases, $expect);
    }

    /**
     * Test various validation options
     */
	public function testFieldTextValidation() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextValidation();
        $expect = [];
        $tail = "<br/>\n";
        $expect['required'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1"'
                    . ' type="text" class="form-control"'
                    . ' required/>' . "\n"
                )
            )
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" maxlength="10" required/>' . "\n"
                )
            )
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3" required/>' . "\n"
                )
            )
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3"'
                    . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required/>' . "\n"
                )
            )
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldTextarea() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextarea();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<textarea id="field-1" name="field-1"></textarea><br/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<textarea id="field-1" name="field-1" readonly></textarea><br/>' . "\n"
                )
            )
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

   /**
    * Check field as a time element
    */
	public function testFieldTime() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTime();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="time"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="time"'
                    . ' class="form-control" value="20:10"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="time"'
                    . ' class="form-control" value="20:10"'
                    . ' min="19:57" max="20:19"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="time"'
                    . ' class="form-control" value="20:10"'
                    . ' readonly/>' . "\n"
                )
            )
        );

        // Convert to hidden for read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="20:10"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a url
     */
	public function testFieldUrl() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldUrl();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="url"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="url"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
        );

        // Test read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

   /**
    * Check field as a week element
    */
	public function testFieldWeek() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldWeek();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="week"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="week"'
                    . ' class="form-control" value="2010-W37"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' min="1957-W30" max="2099-W42"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<input id="field-1" name="field-1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' readonly/>' . "\n"
                )
            )
        );

        // Convert to hidden for read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="2010-W37"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a html element
     */
	public function testHtml() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Html();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> formGroup(
                $this -> column1('')
                . $this -> column2(
                    '<p>This is some escaped html &amp;</p>'
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = $expect['basic'];

        // Test read (less than view) access
        $expect['read'] = $expect['view'];

        $this -> runCases($cases, $expect);
    }

	public function testSection() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Section();
        $expect = [];

        $expect['empty'] = Block::fromString(
            '<fieldset>' . "\n",
            '</fieldset>' . "\n"
        );
        // Now add a label
        $expect['label'] = Block::fromString(
            '<fieldset>' . "\n"
            . '<legend>This is legendary</legend>' . "\n",
            '</fieldset>' . "\n"
        );

        // Same for view access
        $expect['label-view'] = $expect['label'];

        // Same for read access
        $expect['label-read'] = $expect['label'];

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a static element
     */
	public function testStatic() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Static();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> column1('', 'div', '')
            . $this -> column2('This is unescaped text with &lt;stuff&gt;!')
            . "<br/>\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = $expect['basic'];

        // Test read (less than view) access
        $expect['read'] = $expect['basic'];

        $this -> runCases($cases, $expect);
    }

}
