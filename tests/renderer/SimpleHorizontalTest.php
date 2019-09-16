<?php

use Abivia\NextForm;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\ButtonElement;
use Abivia\NextForm\Element\CellElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Element\HtmlElement;
use Abivia\NextForm\Element\SectionElement;
use Abivia\NextForm\Element\StaticElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\SimpleHtml;

include_once __DIR__ . '/RendererCaseGenerator.php';
include_once __DIR__ . '/RendererCaseRunner.php';
include_once __DIR__ . '/../test-tools/HtmlTestLogger.php';

/**
 * @covers \Abivia\NextForm\Renderer\SimpleHtml
 */
class FormRendererSimpleHtmlHorizontalTest extends \PHPUnit\Framework\TestCase {
    use HtmlTestLogger;
    use RendererCaseRunner;

    protected $emptyLabel;
    protected $testObj;

    protected function column1($text, $tag = 'label', $for = 'field-1') {
        $for = $for === '' ? '' : ' for="' . $for . '"';
        $text = '<' . $tag
            . ($tag === 'label' ? $for : '')
            . ' style="display:inline-block; vertical-align:top; width:20%">'
            . ($text === '' ? '&nbsp;' : $text) . '</' . $tag . '>' . "\n";
        return $text;
    }

    protected function column2($text){
        $text = '<div style="display:inline-block; vertical-align:top; width:40%">' . "\n"
            . $text . '</div>' . "\n";
        return $text;
    }

    protected function setUp() : void {
        NextForm::boot();
        $this -> testObj = new SimpleHtml();
        $this -> testObj -> setShow('layout:hor:20%:40%');
        $this -> emptyLabel = $this -> column1('&nbsp;');
    }

    public static function setUpBeforeClass() : void {
        self::$allHtml = '<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>' . __CLASS__  . '</title>
  </head>
<body>
<form id="someform" name="someform" method="post" action="http://localhost/nextform/post.php">
';
    }

    public static function tearDownAfterClass() : void {
        self::$allHtml .= '</form></body></html>';
        file_put_contents(__DIR__ . '/' . __CLASS__  . '-out.html', self::$allHtml);
    }

	public function testInstantiation() {
		$this -> assertInstanceOf('\Abivia\NextForm\Renderer\SimpleHtml', $this -> testObj);
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
        $expect['bda'] = new Block;
        $expect['bda'] -> body =
            $this -> column1('', 'label', 'button-1')
            . $this -> column2(
                '<input id="button-1" name="button-1" type="button" value="I am Button!"/>'
            )
            . '<br/>' . "\n";

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = new Block;
        $expect['rbda'] -> body =
            $this -> column1('', 'label', 'button-1')
            . $this -> column2(
                '<input id="button-1" name="button-1" type="reset" value="I am Button!"/>'
            )
            . '<br/>' . "\n";

        // Submit button default access
        $expect['sbda'] = new Block;
        $expect['sbda'] -> body =
            $this -> column1('', 'label', 'button-1')
            . $this -> column2(
                '<input id="button-1" name="button-1" type="submit" value="I am Button!"/>'
            )
            . '<br/>' . "\n";

        // Return to button, same as "bda" case
        $expect['bda2'] = $expect['bda'];

        // View access
        $expect['bva'] = new Block;
        $expect['bva'] -> body =
            $this -> column1('', 'label', 'button-1')
            . $this -> column2(
                '<input id="button-1" name="button-1" type="button"'
                . ' value="I am Button!" disabled/>'
            )
            . '<br/>' . "\n";

        // Read access
        $expect['bra'] = new Block;
        $expect['bra'] -> body = '<input id="button-1" name="button-1" type="hidden"'
            . ' value="I am Button!"/>' . "\n";

        // Small... same as bda
        $expect['small'] = $expect['bda'];

        // Large... same as sbda
        $expect['large'] = $expect['sbda'];

        // Large warning outline... same as bda
        $expect['lg-warn-out'] = $expect['bda'];

        $this -> runCases($cases, $expect);
    }

    /**
     * Test a field with label options
     */
	public function testButtonLabels() {
        $this -> logMethod(__METHOD__);
        $tail = '<br/>' . "\n";
        $cases = RendererCaseGenerator::html_ButtonLabels();
        $expect = [];

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body =
            '<label for="button-1" style="display:inline-block; vertical-align:top; width:20%">'
            . '&nbsp;</label>' . "\n"
            . $this -> column2(
                '<input id="button-1" name="button-1" type="button"/>'
            )
            . $tail;

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body =
            '<label for="button-1" style="display:inline-block; vertical-align:top; width:20%">'
            . '&nbsp;</label>' . "\n"
            . $this -> column2(
                '<span>prefix</span>'
                . '<input id="button-1" name="button-1" type="button"/>'
            )
            . $tail;

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body =
            '<label for="button-1" style="display:inline-block; vertical-align:top; width:20%">'
            . '&nbsp;</label>' . "\n"
            . $this -> column2(
                '<input id="button-1" name="button-1" type="button"/>'
                . '<span>suffix</span>'
            )
            . $tail;

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = '<label for="button-1" style="display:inline-block; vertical-align:top; width:20%">'
            . 'Header</label>' . "\n"
            . $this -> column2(
                '<input id="button-1" name="button-1" type="button"/>'
            )
            . $tail;

        // Help
        $expect['label-help'] = Block::fromString(
            '<label for="button-1" style="display:inline-block; vertical-align:top; width:20%">'
            . '&nbsp;</label>' . "\n"
            . $this -> column2(
                '<input id="button-1" name="button-1" type="button"'
                . ' aria-describedby="button-1-formhelp"/><br/>' . "\n"
                . '<small id="button-1-formhelp">Helpful</small>' . "\n"
            )
            . $tail
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            '<label for="button-1" style="display:inline-block;'
            . ' vertical-align:top; width:20%">&nbsp;</label>' . "\n"
            . $this -> column2(
                '<input id="button-1" name="button-1" type="button" value="inner"/>'
            )
            . $tail
        );

        // All
        $expect['label-all'] = Block::fromString(
            '<label for="button-1" style="display:inline-block; vertical-align:top; width:20%">'
            . 'Header</label>' . "\n"
            . $this -> column2(
                '<span>prefix</span>'
                . '<input id="button-1" name="button-1" type="button" value="inner"'
                . ' aria-describedby="button-1-formhelp"/>'
                . '<span>suffix</span><br/>' . "\n"
                . '<small id="button-1-formhelp">Helpful</small>' . "\n"
            )
            . $tail
        );

        $this -> runCases($cases, $expect);
    }

	public function testCell() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Cell();

        $expect['basic'] = Block::fromString(
            '<div style="display:inline-block; vertical-align:top; width:40%">' . "\n",
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
        // Value reset submit
        $expect = [];
        $expect['value'] = new Block;
        $expect['value'] -> body = $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="button" value="Ok Bob"/>' . "\n"
            )
            . "<br/>\n";

        $expect['reset'] = new Block;
        $expect['reset'] -> body = $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="reset" value="Ok Bob"/>' . "\n"
            )
            . "<br/>\n";

        $expect['submit'] = new Block;
        $expect['submit'] -> body = $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="submit" value="Ok Bob"/>' . "\n"
            )
            . "<br/>\n";

        $this -> runCases($cases, $expect);
   }

    /**
     * Test code generation for a checkbox element
     */
	public function testFieldCheckbox() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldCheckbox();
        $expect = [];

        $expect['basic'] = new Block;
        $expect['basic'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="checkbox"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . "<br/>\n";

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="checkbox" value="3"'
                . ' data-sidecar="&quot;foo&quot;"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['value-view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="checkbox" value="3"'
                . ' readonly data-sidecar="&quot;foo&quot;"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="3"'
            . ' data-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        // Test view access
        $expect['view'] = new Block;
        $expect['view'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="checkbox" readonly/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // Test read (less than view) access
        $expect['read'] = new Block;
        $expect['read'] -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<span>prefix</span>'
                . '<input id="field-1" name="field-1" type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                . '<span>suffix</span>'
            )
            . '<br/>' . "\n";

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = $this -> column1('Header', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = new Block;
        $expect['label-inner'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">inner</label>' . "\n"
            )
            . '<br/>' . "\n";

        // All
        $expect['label-all'] = new Block;
        $expect['label-all'] -> body = $this -> column1('Header', 'div')
            . $this -> column2(
                '<span>prefix</span><input id="field-1" name="field-1" type="checkbox"'
                . ' value="3"/>' . "\n"
                . '<label for="field-1">inner</label>' . "\n"
                . '<span>suffix</span>'
            )
            . '<br/>' . "\n";

        // inline
        $expect['inline'] = $expect['basic'];

        // inline nolabel
        $expect['inline-nolabel'] = $expect['basic'];

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
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1[]" type="checkbox" value="textlist 1"/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2"/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3"/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1[]" type="checkbox" value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // View access
        $expect['view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1[]" type="checkbox" value="textlist 1" readonly/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2" readonly/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3" readonly/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1[]" type="checkbox" value="textlist 4" readonly data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Read access
        $expect['read'] = Block::fromString();

        // Set a value to trigger the checked option
        $expect['single-value'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1[]" type="checkbox" value="textlist 1"/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2"/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3"/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                . ' value="textlist 4" checked data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Check read access with a single value
        $expect['single-value-read'] = Block::fromString(
            '<input id="field-1-opt3" name="field-1[]" type="hidden"'
            . ' value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Set a second value to trigger the checked option
        $expect['dual-value'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1[]" type="checkbox"'
                . ' value="textlist 1" checked/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2"/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3"/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                . ' value="textlist 4" checked data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['dual-value-view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1[]" type="checkbox" value="textlist 1" checked readonly/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2" readonly/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3" readonly/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1[]" type="checkbox"'
                . ' value="textlist 4" checked readonly data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['dual-value-read'] = Block::fromString(
            '<input id="field-1-opt0" name="field-1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field-1-opt3" name="field-1[]" type="hidden"'
            . ' value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
        );

        // Inline mode, not used in simple renderer
        $expect['inline'] = $expect['basic'];

        // Inline mode, not used in simple renderer
        $expect['inline-nolabel'] = $expect['basic'];

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
            $this -> emptyLabel
            . $this -> column2('<input id="field-1" name="field-1" type="color"/>' . "\n")
            . "<br/>\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="color" value="#F0F0F0"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Same result with explicit write access
        //
        $expect['value-write'] = $expect['value'];

        // Now with view access
        $expect['value-view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="color"'
                . ' value="#F0F0F0" readonly/>' . "\n"
            )
            . "<br/>\n"
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
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="date"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="date" value="2010-10-10"/>'
                . "\n"
            )
            . "<br/>\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
                . ' min="1957-10-08" max="2099-11-06"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
                . ' readonly/>' . "\n"
            )
            . "<br/>\n"
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
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="datetime-local"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="datetime-local"'
                . ' value="2010-10-10"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
                . ' min="1957-10-08T00:00" max="2099-11-06T14:15"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
                . ' readonly/>' . "\n"
            )
            . "<br/>\n"
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
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="email"/>' . "\n"
            )
            . '<br/>' . "\n"
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="email" multiple/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this -> column1('Yer email')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="email"/>' . "\n"
            )
            . '<br/>' . "\n"
            . '<label for="field-1-confirmation"'
            . ' style="display:inline-block; vertical-align:top; width:20%">'
            . 'Confirm yer email</label>' . "\n"
            . $this -> column2(
                '<input id="field-1-confirmation" name="field-1-confirmation"'
                . ' type="email"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        //
        $expect['view'] = Block::fromString(
            $this -> column1('Yer email')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="email"'
                . ' value="snafu@fub.ar" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="file"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Now test validation
        $expect['valid'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1[]" type="file"'
                . ' accept="*.png,*.jpg" multiple/>' . "\n"
            )
            . "<br/>\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text" readonly/>' . "\n"
            )
            . "<br/>\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
            . ' value="file1.png,file2.jpg" readonly/>' . "\n"
            )
            . "<br/>\n"
        );

        // Test read access
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
            $this -> emptyLabel
            . $this -> column2('<input id="field-1" name="field-1" type="month"/>' . "\n")
            . "<br/>\n"
        );

        $expect['value'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="month" value="2010-10"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="month" value="2010-10"'
                . ' min="1957-10" max="2099-11"/>' . "\n"
            )
            . "<br/>\n"
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="month" value="2010-10"'
                . ' readonly/>' . "\n"
            )
            . "<br/>\n"
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
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="number" value="200"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="number"'
                . ' value="200"'
                . ' required/>' . "\n"
            )
            . $tail
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="number"'
                . ' value="200"'
                . ' min="-1000" max="999.45" required/>' . "\n"
            )
            . $tail
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="number"'
                . ' value="200"'
                . ' min="-1000" max="999.45" required step="1.23"/>' . "\n"
            )
            . $tail
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="number"'
                . ' value="200" readonly/>' . "\n"
            )
            . $tail
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldPassword() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldPassword();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="password"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="password" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="password" value="secret" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> column1('', 'div', '')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="radio"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> column1('', 'div', '')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="radio" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> column1('', 'div', '')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="radio" value="3" readonly/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> column1('Very Important Choice', 'div')
            . $this -> column2('<span>No need to fear</span>'
            . '<input id="field-1" name="field-1" type="radio" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<span>See? No problem!</span>')
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this -> column1('Very Important Choice', 'div')
            . $this -> column2('<span>No need to fear</span>'
            . '<input id="field-1" name="field-1" type="radio" value="3" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<span>See? No problem!</span>')
            . '<br/>' . "\n"
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
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1" type="radio" value="textlist 2"/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1" type="radio" value="textlist 3"/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1" type="radio" value="textlist 4"'
                . ' data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1" type="radio" value="textlist 2"/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1" type="radio" value="textlist 3"'
                . ' checked/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1" type="radio" value="textlist 4"'
                . ' data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1" type="radio" value="textlist 1"'
                . ' readonly/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1" type="radio" value="textlist 2"'
                . ' readonly/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1" type="radio" value="textlist 3"'
                . ' checked readonly/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1" type="radio" value="textlist 4"'
                . ' readonly data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1-opt2" name="field-1" type="hidden" value="textlist 3"/>' . "\n"
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
            $this -> column1('Very Important Choice', 'div')
            . $this -> column2(
                '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1" type="radio" value="textlist 2"/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1" type="radio" value="textlist 3" checked/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1" type="radio" value="textlist 4" data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this -> column1('Very Important Choice', 'div')
            . $this -> column2(
                '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt0" name="field-1" type="radio" value="textlist 1"'
                . ' readonly/>' . "\n"
                . '<label for="field-1-opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt1" name="field-1" type="radio" value="textlist 2"'
                . ' readonly/>' . "\n"
                . '<label for="field-1-opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt2" name="field-1" type="radio" value="textlist 3"'
                . ' checked readonly/>' . "\n"
                . '<label for="field-1-opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field-1-opt3" name="field-1" type="radio" value="textlist 4"'
                . ' readonly data-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field-1-opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read access
        $expect['labels-value-read'] = Block::fromString(
            '<input id="field-1-opt2" name="field-1" type="hidden" value="textlist 3"/>' . "\n"
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
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="range" value="200"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Making the field required should have no effect
        $expect['required'] = $expect['basic'];

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="range"'
                . ' value="200"'
                . ' min="-1000" max="999.45"/>' . "\n"
            )
            . $tail
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="range"'
                . ' value="200"'
                . ' min="-1000" max="999.45" step="20"/>' . "\n"
            )
            . $tail
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];


        // Test view access
        $expect['view'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' value="200" readonly/>' . "\n"
            )
            . $tail
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
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="search"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="search" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> column1('', 'div')
            . $this -> column2(
                '<select id="field-1" name="field-1">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2">textlist 2</option>' . "\n"
                . '<option value="textlist 3">textlist 3</option>' . "\n"
                . '<option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="hidden" value=""/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<select id="field-1" name="field-1">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
                . '<option value="textlist 3">textlist 3</option>' . "\n"
                . '<option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result for BS4 custom
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="hidden" value="textlist 2"/>' . "\n"
                . '<span>textlist 2</span>' . "\n"
            ). '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="textlist 2"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<select id="field-1" name="field-1[]" multiple>' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
                . '<option value="textlist 3">textlist 3</option>' . "\n"
                . '<option value="textlist 4" selected data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1-opt0" name="field-1[]" type="hidden" value="textlist 2"/>' . "\n"
                . '<span>textlist 2</span><br/>' . "\n"
                . '<input id="field-1-opt1" name="field-1[]" type="hidden" value="textlist 4"/>' . "\n"
                . '<span>textlist 4</span><br/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['multivalue-read'] = Block::fromString(
            '<input id="field-1-opt0" name="field-1[0]" type="hidden" value="textlist 2"/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[1]" type="hidden" value="textlist 4"/>' . "\n"
        );

        // Set the presentation to six rows
        $expect['sixrow'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<select id="field-1" name="field-1[]" multiple size="6">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
                . '<option value="textlist 3">textlist 3</option>' . "\n"
                . '<option value="textlist 4" selected data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> column1('', 'div')
            . $this -> column2(
                '<select id="field-1" name="field-1">' . "\n"
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
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="hidden" value=""/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<select id="field-1" name="field-1">' . "\n"
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
            . '<br/>' . "\n"
        );

        // No change for the BS custom presentation
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="hidden" value="S2I1"/>' . "\n"
                . '<span>Sub Two Item One</span>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['value-read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="S2I1"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<select id="field-1" name="field-1[]" multiple>' . "\n"
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
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1-opt0" name="field-1[]" type="hidden" value="Sub One Item One"/>' . "\n"
                . '<span>Sub One Item One</span><br/>' . "\n"
                . '<input id="field-1-opt1" name="field-1[]" type="hidden" value="S2I1"/>' . "\n"
                . '<span>Sub Two Item One</span><br/>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="tel"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="tel" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
        $expect['default'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2('<input id="field-1" name="field-1" type="text"/>' . "\n")
            .'<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldTextDataList() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextDataList();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' list="field-1-list"/>' . "\n"
                . "<datalist id=\"field-1-list\">\n"
                . "<option value=\"textlist 1\"/>\n"
                . "<option value=\"textlist 2\"/>\n"
                . "<option value=\"textlist 3\"/>\n"
                . "<option value=\"textlist 4\" data-sidecar=\"[1,2,3,4]\"/>\n"
                . "</datalist>\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
        $tail = "<br/>\n";

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body = $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . $tail;

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body = $this -> emptyLabel
            . $this -> column2(
                '<span>prefix</span>'
                . '<input id="field-1" name="field-1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . $tail;

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body = $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' value="the value"/>'
                . '<span>suffix</span>' . "\n"
            )
            . $tail;

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = $this -> column1('Header')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . $tail;

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = new Block;
        $expect['label-inner'] -> body = $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' value="the value"'
                . ' placeholder="inner"/>' . "\n"
            )
            . $tail;

        // All
        $expect['label-all'] = new Block;
        $expect['label-all'] -> body = $this -> column1('Header')
            . $this -> column2(
                '<span>prefix</span>'
                . '<input id="field-1" name="field-1" type="text"'
                . ' value="the value" placeholder="inner"/>'
                . '<span>suffix</span>' . "\n"
            )
            . $tail;

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
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="text"'
                . ' required/>' . "\n"
            )
            . $tail
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' maxlength="10" required/>' . "\n"
            )
            . $tail
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' maxlength="10" minlength="3" required/>' . "\n"
            )
            . $tail
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this -> column1('')
            . $this -> column2(
                '<input id="field-1" name="field-1" type="text"'
                . ' maxlength="10" minlength="3"'
                . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required/>' . "\n"
            )
            . $tail
        );

        $this -> runCases($cases, $expect);
    }

	public function testFieldTextarea() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextarea();
        $expect = [];
        $tail = "<br/>\n";

        $expect['basic'] = Block::fromString(
            $this -> emptyLabel
            . '<textarea id="field-1" name="field-1"></textarea><br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . '<textarea id="field-1" name="field-1" readonly></textarea><br/>' . "\n"
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
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="time"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="time" value="20:10"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="time" value="20:10"'
                . ' min="19:57" max="20:19"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="time" value="20:10"'
                . ' readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="url"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="url" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="week"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1"'
                . ' type="week" value="2010-W37"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="week" value="2010-W37"'
                . ' min="1957-W30" max="2099-W42"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this -> emptyLabel
            . $this -> column2(
                '<input id="field-1" name="field-1" type="week" value="2010-W37"'
                . ' readonly/>' . "\n"
            )
            . '<br/>' . "\n"
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
            $this -> column1('', 'div', '')
            . $this -> column2(
                '<p>This is some escaped html &amp;</p>'
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
