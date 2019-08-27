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

    protected function formGroup($body) {
        $text = '<div class="form-group">' . "\n"
            . $body
            . '</div>' . "\n";
        return $text;
    }

    protected function setUp() : void {
        NextForm::boot();
        $this -> testObj = new Bootstrap4();
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

	public function testFormRendererBootstrap4_Instantiation() {
		$this -> assertInstanceOf('\Abivia\NextForm\Renderer\Bootstrap4', $this -> testObj);
	}

	public function testFormRendererBootstrap4_Start() {
        $data = $this -> testObj -> start();
        $this -> assertEquals("<form method=\"post\">\n", $data -> body);
        $this -> assertEquals("</form>\n", $data -> post);
        $data = $this -> testObj -> start(['method' => 'put']);
        $this -> assertEquals("<form method=\"put\">\n", $data -> body);
        $data = $this -> testObj -> start(['action' => 'https://localhost/some file.php']);
        $this -> assertEquals("<form method=\"post\" action=\"https://localhost/some file.php\">\n", $data -> body);
        $data = $this -> testObj -> start(['name' => 'bad<name']);
        $this -> assertEquals("<form name=\"bad&lt;name\" method=\"post\">\n", $data -> body);
        $data = $this -> testObj -> start(['id' => 'bad<name']);
        $this -> assertEquals("<form id=\"bad&lt;name\" method=\"post\">\n", $data -> body);
    }

    /**
     * Check a button
     */
	public function testFormRendererBootstrap4_Button() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Button();
        $expect = [];

        // Default access
        $expect['bda'] = new Block;
        $expect['bda'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="button"'
            . ' class="btn btn-success" value="I am Button!"/>' . "\n"
        );

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = new Block;
        $expect['rbda'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="reset"'
            . ' class="btn btn-primary" value="I am Button!"/>' . "\n"
        );

        // Submit button default access
        $expect['sbda'] = new Block;
        $expect['sbda'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="submit"'
            . ' class="btn btn-primary" value="I am Button!"/>' . "\n"
        );

        // Return to button, same as "bda" case but primary
        $expect['bda2'] = new Block;
        $expect['bda2'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="button"'
            . ' class="btn btn-primary" value="I am Button!"/>' . "\n"
        );

        // View access
        $expect['bva'] = new Block;
        $expect['bva'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="button"'
            . ' class="btn btn-primary" value="I am Button!" disabled/>' . "\n"
        );

        // Readaccess
        $expect['bra'] = new Block;
        $expect['bra'] -> body = '<input id="button-1" name="button-1" type="hidden"'
            . ' value="I am Button!"/>' . "\n";

        $this -> runCases($cases, $expect);
    }

    /**
     * Test a field with label options
     */
	public function testFormRendererBootstrap4_ButtonLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_ButtonLabels();
        $expect = [];

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="button"'
            . ' class="btn btn-primary"/>'
            . "\n"
        );

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body = $this -> formGroup(
            '<span>prefix</span>'
            . '<input id="button-1" name="button-1" type="button" class="btn btn-primary"/>' . "\n"
        );

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="button" class="btn btn-primary"/>'
            . '<span>suffix</span>' . "\n"
        );

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = $this -> formGroup(
            '<label for="button-1">Header</label>' . "\n"
            . '<input id="button-1" name="button-1" type="button" class="btn btn-primary"/>' . "\n"
        );

        // Help
        $expect['label-help'] = new Block;
        $expect['label-help'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="button"'
            . ' class="btn btn-primary" aria-describedby="button-1-formhelp"/>'
            . "\n"
            . '<small id="button-1-formhelp" class="form-text text-muted">Helpful</small>'
            . "\n"
        );

        // Inner
        $expect['label-inner'] = new Block;
        $expect['label-inner'] -> body = $this -> formGroup(
            '<input id="button-1" name="button-1" type="button"'
            . ' class="btn btn-primary" value="inner"/>' . "\n"
        );

        // All
        $expect['label-all'] = new Block;
        $expect['label-all'] -> body = $this -> formGroup(
            '<label for="button-1">Header</label>' . "\n"
            . '<span>prefix</span><input id="button-1" name="button-1" type="button"'
            . ' class="btn btn-primary" value="inner" aria-describedby="button-1-formhelp"/>'
            . '<span>suffix</span>'
            . "\n"
            . '<small id="button-1-formhelp" class="form-text text-muted">Helpful</small>'
            . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFormRendererBootstrap4_Cell() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $this -> assertFalse($this -> testObj -> queryContext('inCell'));
        $element = new CellElement();
        $data = $this -> testObj -> render($element);
        $expect -> body = '<div class="form-row">' . "\n";
        $expect -> post = '</div>' . "\n";
        $expect -> onCloseDone = [$this -> testObj, 'popContext'];
        $this -> assertTrue($this -> testObj -> queryContext('inCell'));
        $this -> assertEquals($expect, $data);
    }

    /**
     * @doesNotPerformAssertions
     */
	public function testFormRendererBootstrap4_SetOptions() {

        $this -> testObj -> setOptions();
    }

    /**
     * Check a field as the button types
     */
	public function testFormRendererBootstrap4_FieldButton() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldButton();

        $expect = [];
        $expect['value'] = new Block;
        $expect['value'] -> body = $this -> formGroup(
            '<input id="field-1" name="field-1" type="button"'
            . ' class="form-control" value="Ok Bob"/>' . "\n"
        );

        $expect['reset'] = new Block;
        $expect['reset'] -> body = $this -> formGroup(
            '<input id="field-1" name="field-1" type="reset"'
            . ' class="form-control" value="Ok Bob"/>' . "\n"
        );

        $expect['submit'] = new Block;
        $expect['submit'] -> body = $this -> formGroup(
            '<input id="field-1" name="field-1" type="submit"'
            . ' class="form-control" value="Ok Bob"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
   }

    /**
     * Test code generation for a checkbox element
     */
	public function testFormRendererBootstrap4_FieldCheckbox() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldCheckbox();
        $expect = [];

        $expect['basic'] = new Block;
        $expect['basic'] -> body = $this -> column1('', 'div')
            . $this -> formGroup(
                '<input id="field-1" name="field-1[]" type="checkbox"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . "<br/>\n";

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = new Block;
        $expect['value'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // Test view access
        $expect['view'] = new Block;
        $expect['view'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1[]" type="checkbox" readonly/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // Test read (less than view) access
        $expect['read'] = new Block;
        $expect['read'] -> body = '<input id="field-1" name="field-1[]" type="hidden"/>' . "\n";

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1[]"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<span>prefix</span>'
                . '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1[]"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
                . '<span>suffix</span>'
            )
            . '<br/>' . "\n";

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = $this -> column1('Header', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            )
            . '<br/>' . "\n";

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = new Block;
        $expect['label-inner'] -> body = $this -> column1('', 'div')
            . $this -> column2(
                '<input id="field-1" name="field-1[]"'
                . ' type="checkbox" value="3"/>' . "\n"
                . '<label for="field-1">inner</label>' . "\n"
            )
            . '<br/>' . "\n";

        // All
        $expect['label-all'] = new Block;
        $expect['label-all'] -> body = $this -> column1('Header', 'div')
            . $this -> column2(
                '<span>prefix</span><input id="field-1" name="field-1[]" type="checkbox"'
                . ' value="3"/>' . "\n"
                . '<label for="field-1">inner</label>' . "\n"
                . '<span>suffix</span>'
            )
            . '<br/>' . "\n";

        $this -> runCases($cases, $expect);
    //--------------------------
        return;
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a checkbox
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // Give the element a label
        //
        $element -> setLabel('inner', '<Stand-alone> checkbox');
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1[]" type="checkbox"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue(3);
        $expect -> body = '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Give it a heading
        //
        $element -> setLabel('heading', 'Check this out');
        $expect -> body = '<div>Check this out</div>' . "\n"
            . '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some after text
        //
        $element -> setLabel('after', '(afterthought)');
        $expect -> body = '<div>Check this out</div>' . "\n"
            . '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<span>(afterthought)</span>'
            . '<br/>' . "\n";
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // And a before label
        //
        $element -> setLabel('before', 'freaky');
        $expect -> body = '<div>Check this out</div>' . "\n"
            . '<span>freaky</span>'
            . '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<span>(afterthought)</span>'
            . '<br/>' . "\n";
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<div>Check this out</div>' . "\n"
            . '<span>freaky</span>'
            . '<input id="field-1" name="field-1[]" type="checkbox" value="3" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<span>(afterthought)</span>'
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1[]" type="hidden" value="3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test code generation for a checkbox element with a list
     */
	public function testFormRendererBootstrap4_FieldCheckboxList() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change textWithList to a checkbox
        //
        $schema -> getProperty('test/textWithList') -> getPresentation() -> setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        // No access specification assumes write access
        $data = $this -> testObj -> render($element);
        $expect -> body = '<div>
  <input id="field-1-opt0" name="field-1[]" type="checkbox" value="textlist 1"/>
  <label for="field-1-opt0">textlist 1</label>
</div>
<div>
  <input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2"/>
  <label for="field-1-opt1">textlist 2</label>
</div>
<div>
  <input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3"/>
  <label for="field-1-opt2">textlist 3</label>
</div>
<div>
  <input id="field-1-opt3" name="field-1[]" type="checkbox" value="textlist 4" data-sidecar="[1,2,3,4]"/>
  <label for="field-1-opt3">textlist 4</label>
</div>
<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value to trigger the checked option
        //
        $element -> setValue('textlist 3');
        $expect -> body = str_replace('list 3"', 'list 3" checked', $expect -> body);
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a second value to trigger the checked option
        //
        $element -> setValue(['textlist 1', 'textlist 3']);
        $expect -> body = str_replace('list 1"', 'list 1" checked', $expect -> body);
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<div>
  <input id="field-1-opt0" name="field-1[]" type="checkbox" value="textlist 1" readonly checked/>
  <label for="field-1-opt0">textlist 1</label>
</div>
<div>
  <input id="field-1-opt1" name="field-1[]" type="checkbox" value="textlist 2" readonly/>
  <label for="field-1-opt1">textlist 2</label>
</div>
<div>
  <input id="field-1-opt2" name="field-1[]" type="checkbox" value="textlist 3" readonly checked/>
  <label for="field-1-opt2">textlist 3</label>
</div>
<div>
  <input id="field-1-opt3" name="field-1[]" type="checkbox" value="textlist 4" readonly data-sidecar="[1,2,3,4]"/>
  <label for="field-1-opt3">textlist 4</label>
</div>
<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt0" name="field-1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field-1-opt2" name="field-1[]" type="hidden" value="textlist 3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a color element
    */
	public function testFormRendererBootstrap4_FieldColor() {
        $this -> logMethod(__METHOD__);

        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('color');
        $config = json_decode('{"type": "field","object": "test/text"}');

        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="color"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('#F0F0F0');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="color" value="#F0F0F0"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="color" value="#F0F0F0" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="#F0F0F0"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a date element
    */
	public function testFormRendererBootstrap4_FieldDate() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('date');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="date"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-10-10');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="date" value="2010-10-10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10-08');
        $validation -> set('maxValue', 'Nov 6th 2099');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
            . ' min="1957-10-08" max="2099-11-06"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-10-10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a datetime-local element
    */
	public function testFormRendererBootstrap4_FieldDatetimeLocal() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('datetime-local');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-10-10');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10-08');
        $validation -> set('maxValue', '2:15 pm Nov 6th 2099');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
            . ' min="1957-10-08T00:00" max="2099-11-06T14:15"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-10-10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererBootstrap4_FieldEmail() {
        $this -> logMethod(__METHOD__);

        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('email');
        $config = json_decode('{"type": "field","object": "test/text"}');

        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="email"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('multiple', true);
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="email" multiple/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Turn confirmation on and set some test labels
        //
        $presentation -> setConfirm(true);
        $element -> setLabel('heading', 'Yer email');
        $element -> setLabel('confirm', 'Confirm yer email');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<label for="field-1">Yer email</label>' . "\n"
            . '<input id="field-1" name="field-1" type="email" multiple/><br/>' . "\n"
            . '<label for="field-1-confirmation">Confirm yer email</label>' . "\n"
            . '<input id="field-1-confirmation" name="field-1-confirmation" type="email" multiple/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $element -> setValue('snafu@fub.ar');
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<label for="field-1">Yer email</label>' . "\n"
            . '<input id="field-1" name="field-1" type="email" value="snafu@fub.ar" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="snafu@fub.ar"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererBootstrap4_FieldFile() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('file');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="file"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('accept', '*.png,*.jpg');
        $validation -> set('multiple', true);
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1[]" type="file" accept="*.png,*.jpg" multiple/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="text" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view with a value
        //
        $element -> setValue(['file1.png', 'file2.jpg']);
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="file1.png,file2.jpg" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt0" name="field-1[0]" type="hidden" value="file1.png"/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[1]" type="hidden" value="file2.jpg"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a hidden element
    */
	public function testFormRendererBootstrap4_FieldHidden() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test a hidden field with label options
     */
	public function testFormRendererBootstrap4_FieldHiddenLabels() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $ret = $element -> setValue('the value');
        $this -> assertTrue($element === $ret);
        //
        // Make sure the value shows up
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"'
            . ' value="the value"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a inner
        //
        $element -> setLabel('inner', 'Something with & in it');
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        // Strip the tail off, add label, re-add tail
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a month element
    */
	public function testFormRendererBootstrap4_FieldMonth() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('month');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="month"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-10');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="month" value="2010-10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10');
        $validation -> set('maxValue', 'Nov 2099');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="month" value="2010-10"'
            . ' min="1957-10" max="2099-11"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="month" value="2010-10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a number
     */
	public function testFormRendererBootstrap4_FieldNumber() {
        $this -> logMethod(__METHOD__);
        $tail = "<br/>\n";
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a number
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('number');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $element -> setValue('200');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="number" value="200"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Make the field required
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('required', true);
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="number"'
            . ' value="200"'
            . ' required/>' . $tail;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set minimum/maximum values
        //
        $validation -> set('minValue', -1000);
        $validation -> set('maxValue', 999.45);
        $expect -> body = '<input id="field-1" name="field-1" type="number"'
            . ' value="200"'
            . ' min="-1000" max="999.45" required/>' . $tail;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a step
        //
        $validation -> set('step', 1.23);
        $expect -> body = '<input id="field-1" name="field-1" type="number"'
            . ' value="200"'
            . ' min="-1000" max="999.45" required step="1.23"/>' . $tail;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Settng a pattern should have no effect!
        //
        $validation -> set('pattern', '/[+\-]?[0-9]+/');
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $expect -> body = '<input id="field-1" name="field-1" type="number"'
            . ' value="200" readonly/>' . $tail;
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererBootstrap4_FieldPassword() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a password
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('password');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="password"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="password" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view with a value
        //
        $element -> setValue('secret');
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="password" value="secret" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="secret"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test code generation for a radio element
     */
	public function testFormRendererBootstrap4_FieldRadio() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a radio
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // Give the element a label
        //
        $element -> setLabel('inner', '<Stand-alone> radio');
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="radio"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue(3);
        $expect -> body = '<input id="field-1" name="field-1" type="radio" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n";
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="radio" value="3" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test code generation for single radio element with labels
     */
	public function testFormRendererBootstrap4_FieldRadioLabels() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a radio
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // Give the element some labels and a value
        //
        $element -> setLabel('before', 'No need to fear');
        $element -> setLabel('heading', 'Very Important Choice');
        $element -> setLabel('inner', '<Stand-alone> radio');
        $element -> setLabel('after', 'See? No problem!');
        $element -> setValue(3);
        $expect -> body = '<div>Very Important Choice</div>' . "\n"
            . '<span>No need to fear</span>'
            . '<input id="field-1" name="field-1" type="radio" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<span>See? No problem!</span><br/>' . "\n"
            ;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data, 'full access');
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<div>Very Important Choice</div>' . "\n"
            . '<span>No need to fear</span>'
            . '<input id="field-1" name="field-1" type="radio" value="3" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<span>See? No problem!</span><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data, 'view only');
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data, 'read(hidden) only');
    }

    /**
     * Test code generation for a radio element with a list
     */
	public function testFormRendererBootstrap4_FieldRadioList() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change textWithList to a radio
        //
        $schema -> getProperty('test/textWithList') -> getPresentation() -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        // No access specification assumes write access
        $data = $this -> testObj -> render($element);
        $expect -> body = '<div>
  <input id="field-1-opt0" name="field-1" type="radio" value="textlist 1"/>
  <label for="field-1-opt0">textlist 1</label>
</div>
<div>
  <input id="field-1-opt1" name="field-1" type="radio" value="textlist 2"/>
  <label for="field-1-opt1">textlist 2</label>
</div>
<div>
  <input id="field-1-opt2" name="field-1" type="radio" value="textlist 3"/>
  <label for="field-1-opt2">textlist 3</label>
</div>
<div>
  <input id="field-1-opt3" name="field-1" type="radio" value="textlist 4" data-sidecar="[1,2,3,4]"/>
  <label for="field-1-opt3">textlist 4</label>
</div>
<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value to trigger the checked option
        //
        $element -> setValue('textlist 3');
        $expect -> body = str_replace('list 3"', 'list 3" checked', $expect -> body);
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<div>
  <input id="field-1-opt0" name="field-1" type="radio" value="textlist 1" readonly/>
  <label for="field-1-opt0">textlist 1</label>
</div>
<div>
  <input id="field-1-opt1" name="field-1" type="radio" value="textlist 2" readonly/>
  <label for="field-1-opt1">textlist 2</label>
</div>
<div>
  <input id="field-1-opt2" name="field-1" type="radio" value="textlist 3" readonly checked/>
  <label for="field-1-opt2">textlist 3</label>
</div>
<div>
  <input id="field-1-opt3" name="field-1" type="radio" value="textlist 4" readonly data-sidecar="[1,2,3,4]"/>
  <label for="field-1-opt3">textlist 4</label>
</div>
<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt2" name="field-1" type="hidden" value="textlist 3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test code generation for a radio element with a list and labels
     */
	public function testFormRendererBootstrap4_FieldRadioListLabels() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a radio
        //
        $presentation = $schema -> getProperty('test/textWithList') -> getPresentation();
        $presentation -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // Give the element some labels and a value
        //
        $element -> setLabel('before', 'No need to fear');
        $element -> setLabel('heading', 'Very Important Choice');
        $element -> setLabel('inner', '<Stand-alone> radio');
        $element -> setLabel('after', 'See? No problem!');
        $element -> setValue('textlist 3');
        $expect -> body = '<div>Very Important Choice</div>' . "\n"
            . '<div>No need to fear</div>' . "\n"
            . '<div>
  <input id="field-1-opt0" name="field-1" type="radio" value="textlist 1"/>
  <label for="field-1-opt0">textlist 1</label>
</div>
<div>
  <input id="field-1-opt1" name="field-1" type="radio" value="textlist 2"/>
  <label for="field-1-opt1">textlist 2</label>
</div>
<div>
  <input id="field-1-opt2" name="field-1" type="radio" value="textlist 3" checked/>
  <label for="field-1-opt2">textlist 3</label>
</div>
<div>
  <input id="field-1-opt3" name="field-1" type="radio" value="textlist 4" data-sidecar="[1,2,3,4]"/>
  <label for="field-1-opt3">textlist 4</label>
</div>' . "\n"
            . '<div>See? No problem!</div>' . "\n"
            . '<br/>' . "\n"
            ;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data, 'full access');
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<div>Very Important Choice</div>' . "\n"
            . '<div>No need to fear</div>' . "\n"
            . '<div>
  <input id="field-1-opt0" name="field-1" type="radio" value="textlist 1" readonly/>
  <label for="field-1-opt0">textlist 1</label>
</div>
<div>
  <input id="field-1-opt1" name="field-1" type="radio" value="textlist 2" readonly/>
  <label for="field-1-opt1">textlist 2</label>
</div>
<div>
  <input id="field-1-opt2" name="field-1" type="radio" value="textlist 3" readonly checked/>
  <label for="field-1-opt2">textlist 3</label>
</div>
<div>
  <input id="field-1-opt3" name="field-1" type="radio" value="textlist 4" readonly data-sidecar="[1,2,3,4]"/>
  <label for="field-1-opt3">textlist 4</label>
</div>' . "\n"
            . '<div>See? No problem!</div>' . "\n"
            . '<br/>' . "\n"
            ;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data, 'view only');
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt2" name="field-1" type="hidden" value="textlist 3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data, 'read(hidden) only');
    }

    /**
     * Check a field as a range
     */
	public function testFormRendererBootstrap4_FieldRange() {
        $this -> logMethod(__METHOD__);
        $tail = "<br/>\n";
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a range
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('range');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $element -> setValue('200');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="range" value="200"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Making the field required should have no effect
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('required', true);
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="range"'
            . ' value="200"/>' . $tail;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set minimum/maximum values
        //
        $validation -> set('minValue', -1000);
        $validation -> set('maxValue', 999.45);
        $expect -> body = '<input id="field-1" name="field-1" type="range"'
            . ' value="200"'
            . ' min="-1000" max="999.45"/>' . $tail;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a step
        //
        $validation -> set('step', 20);
        $expect -> body = '<input id="field-1" name="field-1" type="range"'
            . ' value="200"'
            . ' min="-1000" max="999.45" step="20"/>' . $tail;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Settng a pattern should have no effect!
        //
        $validation -> set('pattern', '/[+\-]?[0-9]+/');
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="200" readonly/>' . $tail;
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a search
     */
	public function testFormRendererBootstrap4_FieldSearch() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a search
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('search');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="search"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="search" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a simple select
     */
	public function testFormRendererBootstrap4_FieldSelect() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a select
        //
        $presentation = $schema -> getProperty('test/textWithList') -> getPresentation();
        $presentation -> setType('select');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<select id="field-1" name="field-1">' . "\n"
            . '  <option value="textlist 1">textlist 1</option>' . "\n"
            . '  <option value="textlist 2">textlist 2</option>' . "\n"
            . '  <option value="textlist 3">textlist 3</option>' . "\n"
            . '  <option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value=""/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now let's give it a value...
        //
        $element -> setValue('textlist 2');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<select id="field-1" name="field-1">' . "\n"
            . '  <option value="textlist 1">textlist 1</option>' . "\n"
            . '  <option value="textlist 2" selected>textlist 2</option>' . "\n"
            . '  <option value="textlist 3">textlist 3</option>' . "\n"
            . '  <option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="textlist 2"/>' . "\n"
            . '<span>textlist 2</span>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="textlist 2"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set multiple and give it two values
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('multiple', true);
        $element -> setValue(['textlist 2', 'textlist 4']);
        $data = $this -> testObj -> render($element);
        $expect -> body = '<select id="field-1" name="field-1[]" multiple>' . "\n"
            . '  <option value="textlist 1">textlist 1</option>' . "\n"
            . '  <option value="textlist 2" selected>textlist 2</option>' . "\n"
            . '  <option value="textlist 3">textlist 3</option>' . "\n"
            . '  <option value="textlist 4" data-sidecar="[1,2,3,4]" selected>textlist 4</option>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1-opt0" name="field-1[]" type="hidden" value="textlist 2"/>' . "\n"
            . '<span>textlist 2</span><br/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[]" type="hidden" value="textlist 4"/>' . "\n"
            . '<span>textlist 4</span><br/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt0" name="field-1[0]" type="hidden" value="textlist 2"/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[1]" type="hidden" value="textlist 4"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set the presentation to one row
        //
        $presentation -> setRows(6);
        $data = $this -> testObj -> render($element);
        $expect -> body = '<select id="field-1" name="field-1[]" size="6" multiple>' . "\n"
            . '  <option value="textlist 1">textlist 1</option>' . "\n"
            . '  <option value="textlist 2" selected>textlist 2</option>' . "\n"
            . '  <option value="textlist 3">textlist 3</option>' . "\n"
            . '  <option value="textlist 4" data-sidecar="[1,2,3,4]" selected>textlist 4</option>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a nested select
     */
	public function testFormRendererBootstrap4_FieldSelectNested() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a select
        //
        $presentation = $schema -> getProperty('test/textWithNestedList') -> getPresentation();
        $presentation -> setType('select');
        $config = json_decode('{"type": "field","object": "test/textWithNestedList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<select id="field-1" name="field-1">' . "\n"
            . '  <option value="General">General</option>' . "\n"
            . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
            . '  <option value="Sub One Item One">Sub One Item One</option>' . "\n"
            . '  <option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '<optgroup label="Subgroup Two">' . "\n"
            . '  <option value="S2I1" data-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
            . '  <option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value=""/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now let's give it a value...
        //
        $element -> setValue('S2I1');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<select id="field-1" name="field-1">' . "\n"
            . '  <option value="General">General</option>' . "\n"
            . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
            . '  <option value="Sub One Item One">Sub One Item One</option>' . "\n"
            . '  <option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '<optgroup label="Subgroup Two">' . "\n"
            . '  <option value="S2I1" data-sidecar="&quot;s2i1 side&quot;" selected>Sub Two Item One</option>' . "\n"
            . '  <option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="S2I1"/>' . "\n"
            . '<span>Sub Two Item One</span>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="S2I1"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set multiple an give it two values
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('multiple', true);
        $element -> setValue(['S2I1', 'Sub One Item One']);
        $data = $this -> testObj -> render($element);
        $expect -> body = '<select id="field-1" name="field-1[]" multiple>' . "\n"
            . '  <option value="General">General</option>' . "\n"
            . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
            . '  <option value="Sub One Item One" selected>Sub One Item One</option>' . "\n"
            . '  <option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '<optgroup label="Subgroup Two">' . "\n"
            . '  <option value="S2I1" data-sidecar="&quot;s2i1 side&quot;" selected>Sub Two Item One</option>' . "\n"
            . '  <option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1-opt0" name="field-1[]" type="hidden" value="Sub One Item One"/>' . "\n"
            . '<span>Sub One Item One</span><br/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[]" type="hidden" value="S2I1"/>' . "\n"
            . '<span>Sub Two Item One</span><br/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt0" name="field-1[0]" type="hidden" value="S2I1"/>' . "\n"
            . '<input id="field-1-opt1" name="field-1[1]" type="hidden" value="Sub One Item One"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a tel
     */
	public function testFormRendererBootstrap4_FieldTel() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a tel
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('tel');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="tel"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="tel" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererBootstrap4_FieldText() {
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

	public function testFormRendererBootstrap4_FieldTextDataList() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $tail = "<br/>\n";
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        // No access assumes write access
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' list="field-1-list"/>'
            . "<datalist id=\"field-1-list\">\n"
            . "  <option value=\"textlist 1\"/>\n"
            . "  <option value=\"textlist 2\"/>\n"
            . "  <option value=\"textlist 3\"/>\n"
            . "  <option value=\"textlist 4\" data-sidecar=\"[1,2,3,4]\"/>\n"
            . "</datalist>\n" . $tail;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        // Test view access: No list is required
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="text" readonly/>' . $tail;
        $expect -> post = null;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        // Test read (less than view) access
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test a text field with label options
     */
	public function testFormRendererBootstrap4_FieldTextLabels() {
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
	public function testFormRendererBootstrap4_FieldTextValidation() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $tail = "<br/>\n";
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $validation = $element -> getDataProperty() -> getValidation();
        //
        // Make the field required
        //
        $validation -> set('required', true);
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' type="text"'
            . ' required/>' . $tail;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a maximum length
        //
        $validation -> set('maxLength', 10);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' maxlength="10" required/>' . $tail;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a minimum length
        //
        $validation -> set('minLength', 3);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' maxlength="10" minlength="3" required/>' . $tail;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Make it match a postal code
        //
        $validation -> set('pattern', '/[a-z][0-9][a-z] ?[0-9][a-z][0-9]/');
        // Strip the tail off, add label, re-add tail
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' maxlength="10" minlength="3"'
            . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required/>' . $tail;
        $data = $this -> testObj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererBootstrap4_FieldTextarea() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a textarea
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('textarea');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<textarea id="field-1" name="field-1"></textarea><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<textarea id="field-1" name="field-1" readonly></textarea><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a time element
    */
	public function testFormRendererBootstrap4_FieldTime() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('time');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="time"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('20:10');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '19:57');
        $validation -> set('maxValue', '20:19');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"'
            . ' min="19:57" max="20:19"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="20:10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a url
     */
	public function testFormRendererBootstrap4_FieldUrl() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a search
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('url');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="url"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="url" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a week element
    */
	public function testFormRendererBootstrap4_FieldWeek() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('week');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="week"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-W37');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-W30');
        $validation -> set('maxValue', '2099-W42');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"'
            . ' min="1957-W30" max="2099-W42"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-W37"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a html element
     */
	public function testFormRendererBootstrap4_Html() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $config = json_decode('{"type":"html","value":"<p>This is some escaped html &amp;<\/p>"}');
        $element = new HtmlElement();
        $element -> configure($config);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = '<p>This is some escaped html &amp;</p>';
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererBootstrap4_Section() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $element = new SectionElement();
        $data = $this -> testObj -> render($element);
        $expect -> body = '<fieldset>' . "\n";
        $expect -> post = '</fieldset>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now add a label
        //
        $element -> setLabel('heading', 'This is legendary');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<fieldset>' . "\n"
            . '<legend>This is legendary</legend>' . "\n";
        $expect -> post = '</fieldset>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same for view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same for read access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a static element
     */
	public function testFormRendererBootstrap4_Static() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $config = json_decode('{"type":"static","value":"This is unescaped text with <stuff>!"}');
        $element = new StaticElement();
        $element -> configure($config);
        //
        // No access specification assumes write access
        //
        $data = $this -> testObj -> render($element);
        $expect -> body = 'This is unescaped text with &lt;stuff&gt;!';
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $this -> testObj -> render($element, ['access' => 'read']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

}