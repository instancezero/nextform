<?php

use Abivia\NextForm;
//use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
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
class FormRendererSimpleHtmlTest extends \PHPUnit\Framework\TestCase {
    use HtmlTestLogger;
    use RendererCaseRunner;

    protected $testObj;

    protected function setUp() : void {
        NextForm::boot();
        $this -> testObj = new SimpleHtml();
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

	public function testFormRendererSimpleHtml_Instantiation() {
		$this -> assertInstanceOf('\Abivia\NextForm\Renderer\SimpleHtml', $this -> testObj);
	}

	public function testFormRendererSimpleHtml_Start() {
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
	public function testFormRendererSimpleHtml_Button() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_Button();
        $expect = [];

        // Default access
        $expect['bda'] = new Block;
        $expect['bda'] -> body = '<input id="button-1" name="button-1" type="button"'
            . ' value="I am Button!"/><br/>' . "\n";

        // Write access same as default
        $expect['bwa'] = $expect['bda'];

        // Reset button default access
        $expect['rbda'] = new Block;
        $expect['rbda'] -> body = '<input id="button-1" name="button-1" type="reset"'
            . ' value="I am Button!"/><br/>' . "\n";

        // Submit button default access
        $expect['sbda'] = new Block;
        $expect['sbda'] -> body = '<input id="button-1" name="button-1" type="submit"'
            . ' value="I am Button!"/><br/>' . "\n";

        // Return to button, same as "bda" case
        $expect['bda2'] = $expect['bda'];

        // View access
        $expect['bva'] = new Block;
        $expect['bva'] -> body = '<input id="button-1" name="button-1" type="button"'
            . ' value="I am Button!" disabled/><br/>' . "\n";

        // Readaccess
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
	public function testFormRendererSimpleHtml_ButtonLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_ButtonLabels();
        $expect = [];

        // no labels
        $expect['label-none'] = Block::fromString(
            '<input id="button-1" name="button-1" type="button"/>'
            . '<br/>' . "\n"
        );

        // before
        $expect['label-before'] = Block::fromString(
            '<span>prefix</span>'
            . '<input id="button-1" name="button-1" type="button"/><br/>' . "\n"
        );

        // After
        $expect['label-after'] = Block::fromString(
            '<input id="button-1" name="button-1" type="button"/>'
            . '<span>suffix</span><br/>' . "\n"
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            '<label for="button-1">Header</label>' . "\n"
            . '<input id="button-1" name="button-1" type="button"/><br/>' . "\n"
        );

        // Help
        $expect['label-help'] = Block::fromString(
            '<input id="button-1" name="button-1" type="button"'
            . ' aria-describedby="button-1-formhelp"/><br/>' . "\n"
            . '<small id="button-1-formhelp">Helpful</small>' . "\n"
            . '<br/>' . "\n"
        );

        // Inner
        $expect['label-inner'] = Block::fromString(
            '<input id="button-1" name="button-1" type="button"'
            . ' value="inner"/><br/>' . "\n"
        );

        // All
        $expect['label-all'] = Block::fromString(
            '<label for="button-1">Header</label>' . "\n"
            . '<span>prefix</span><input id="button-1" name="button-1" type="button"'
            . ' value="inner" aria-describedby="button-1-formhelp"/>'
            . '<span>suffix</span><br/>' . "\n"
            . '<small id="button-1-formhelp">Helpful</small>' . "\n"
            . '<br/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFormRendererSimpleHtml_Cell() {
        $this -> logMethod(__METHOD__);
        $expect = new Block;
        $this -> assertFalse($this -> testObj -> queryContext('inCell'));
        $element = new CellElement();
        $data = $this -> testObj -> render($element);
        $expect -> body = '<div>' . "\n";
        $expect -> post = '</div>' . "\n";
        $expect -> onCloseDone = [$this -> testObj, 'popContext'];
        $this -> assertTrue($this -> testObj -> queryContext('inCell'));
        $this -> assertEquals($expect, $data);
    }

    /**
     * @doesNotPerformAssertions
     */
	public function testFormRendererSimpleHtml_SetOptions() {

        $this -> testObj -> setOptions();
    }

    /**
     * Check a field as the button types
     */
	public function testFormRendererSimpleHtml_FieldButton() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldButton();
        // Value reset submit
        $expect = [];
        $expect['value'] = Block::fromString(
            '<input id="field-1" name="field-1" type="button" value="Ok Bob"/>' . "\n"
            . '<br/>' . "\n"
        );

        $expect['reset'] = Block::fromString(
            '<input id="field-1" name="field-1" type="reset" value="Ok Bob"/>' . "\n"
            . '<br/>' . "\n"
        );

        $expect['submit'] = Block::fromString(
            '<input id="field-1" name="field-1" type="submit" value="Ok Bob"/>' . "\n"
            . '<br/>' . "\n"
        );

        $this -> runCases($cases, $expect);
   }

    /**
     * Test code generation for a checkbox element
     */
	public function testFormRendererSimpleHtml_FieldCheckbox() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldCheckbox();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="checkbox"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write']  = $expect['basic'];

        // Set a value
        $expect['value'] = new Block;
        $expect['value'] -> body = '<input id="field-1" name="field-1" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";

        // Test view access
        $expect['view'] = new Block;
        $expect['view'] -> body = '<input id="field-1" name="field-1" type="checkbox" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";

        // Test read (less than view) access
        $expect['read'] = new Block;
        $expect['read'] -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body = '<input id="field-1" name="field-1"'
            . ' type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body = '<span>prefix</span>'
            . '<input id="field-1" name="field-1" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body = '<input id="field-1" name="field-1"'
            . ' type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<span>suffix</span>'
            . '<br/>' . "\n";

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = '<div>Header</div>' . "\n"
            . '<input id="field-1" name="field-1" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = new Block;
        $expect['label-inner'] -> body = '<input id="field-1" name="field-1"'
            . ' type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">inner</label>' . "\n"
            . '<br/>' . "\n";

        // All
        $expect['label-all'] = new Block;
        $expect['label-all'] -> body = '<div>Header</div>' . "\n"
            . '<span>prefix</span><input id="field-1" name="field-1" type="checkbox"'
            . ' value="3"/>' . "\n"
            . '<label for="field-1">inner</label>' . "\n"
            . '<span>suffix</span><br/>' . "\n";

        // inline
        $expect['inline'] = $expect['basic'];

        // inline nolabel
        $expect['inline-nolabel'] = $expect['basic'];

        $this -> runCases($cases, $expect);
    }

    /**
     * Test code generation for a checkbox element with a list
     */
	public function testFormRendererSimpleHtml_FieldCheckboxList() {
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
	public function testFormRendererSimpleHtml_FieldColor() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldColor();

        $expect = [];

        $expect['default'] = Block::fromString(
            '<input id="field-1" name="field-1" type="color"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            '<input id="field-1" name="field-1" type="color" value="#F0F0F0"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        //
        $expect['value-write'] = $expect['value'];

        // Now with view access
        $expect['value-view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="color" value="#F0F0F0" readonly/>' . "\n"
            . '<br/>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldDate() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldDate();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="date"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            '<input id="field-1" name="field-1" type="date" value="2010-10-10"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
            . ' min="1957-10-08" max="2099-11-06"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
            . ' readonly/>' . "\n"
            . '<br/>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldDatetimeLocal() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldDatetimeLocal();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="datetime-local"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            '<input id="field-1" name="field-1" type="datetime-local"'
            . ' value="2010-10-10"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
            . ' min="1957-10-08T00:00" max="2099-11-06T14:15"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
            . ' readonly/>' . "\n"
            . '<br/>' . "\n"
        );

        // Convert to hidden for read access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="2010-10-10"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFormRendererSimpleHtml_FieldEmail() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldEmail();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="email"/>' . "\n"
            . '<br/>' . "\n"
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            '<input id="field-1" name="field-1" type="email" multiple/>' . "\n"
            . '<br/>' . "\n"
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            '<label for="field-1">Yer email</label>' . "\n"
            . '<input id="field-1" name="field-1" type="email"/>' . "\n"
            . '<br/>' . "\n"
            . '<label for="field-1-confirmation">Confirm yer email</label>' . "\n"
            . '<input id="field-1-confirmation" name="field-1-confirmation"'
            . ' type="email"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Test view access
        //
        $expect['view'] = Block::fromString(
            '<label for="field-1">Yer email</label>' . "\n"
            . '<input id="field-1" name="field-1" type="email"'
            . ' value="snafu@fub.ar" readonly/>' . "\n"
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden" value="snafu@fub.ar"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFormRendererSimpleHtml_FieldFile() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldFile();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="file"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Now test validation
        $expect['valid'] = Block::fromString(
            '<input id="field-1" name="field-1[]" type="file"'
            . ' accept="*.png,*.jpg" multiple/>' . "\n"
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text" readonly/>' . "\n"
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text"'
            . ' value="file1.png,file2.jpg" readonly/>' . "\n"
            . '<br/>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldHidden() {
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
	public function testFormRendererSimpleHtml_FieldHiddenLabels() {
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
	public function testFormRendererSimpleHtml_FieldMonth() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldMonth();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="month"/>' . "\n"
            . '<br/>' . "\n"
        );

        $expect['value'] = Block::fromString(
            '<input id="field-1" name="field-1"'
            . ' type="month" value="2010-10"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            '<input id="field-1" name="field-1" type="month" value="2010-10"'
            . ' min="1957-10" max="2099-11"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            '<input id="field-1" name="field-1"'
            . ' type="month" value="2010-10"'
            . ' readonly/>' . "\n"
            . '<br/>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldNumber() {
        $this -> logMethod(__METHOD__);
        $tail = "<br/>\n";
        $cases = RendererCaseGenerator::html_FieldNumber();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1"'
            . ' type="number" value="200"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            '<input id="field-1" name="field-1" type="number"'
            . ' value="200"'
            . ' required/>' . "\n" . $tail
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            '<input id="field-1" name="field-1" type="number"'
            . ' value="200"'
            . ' min="-1000" max="999.45" required/>' . "\n" . $tail
        );

        // Add a step
        $expect['step'] = Block::fromString(
            '<input id="field-1" name="field-1" type="number"'
            . ' value="200"'
            . ' min="-1000" max="999.45" required step="1.23"/>' . "\n" . $tail
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="number"'
            . ' value="200" readonly/>' . "\n" . $tail
        );

        $this -> runCases($cases, $expect);
    }

	public function testFormRendererSimpleHtml_FieldPassword() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldPassword();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="password"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1"'
            . ' type="password" readonly/>' . "\n"
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            '<input id="field-1" name="field-1"'
            . ' type="password" value="secret" readonly/>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldRadio() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldRadio();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="radio"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            '<input id="field-1" name="field-1" type="radio" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="radio" value="3" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldRadioLabels() {
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
            . '<span>See? No problem!</span>'
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
	public function testFormRendererSimpleHtml_FieldRadioList() {
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
	public function testFormRendererSimpleHtml_FieldRadioListLabels() {
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
	public function testFormRendererSimpleHtml_FieldRange() {
        $this -> logMethod(__METHOD__);
        $tail = "<br/>\n";
        $cases = RendererCaseGenerator::html_FieldRange();

        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="range" value="200"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Making the field required should have no effect
        $expect['required'] = $expect['basic'];

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            '<input id="field-1" name="field-1" type="range"'
            . ' value="200"'
            . ' min="-1000" max="999.45"/>' . "\n" . $tail
        );

        // Add a step
        $expect['step'] = Block::fromString(
            '<input id="field-1" name="field-1" type="range"'
            . ' value="200"'
            . ' min="-1000" max="999.45" step="20"/>' . "\n" . $tail
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];


        // Test view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text"'
            . ' value="200" readonly/>' . "\n" . $tail
        );

        $this -> runCases($cases, $expect);
    }

    /**
     * Check a field as a search
     */
	public function testFormRendererSimpleHtml_FieldSearch() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldSearch();

        $expect = [];
        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="search"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="search" readonly/>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldSelect() {
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
            . '<option value="textlist 1">textlist 1</option>' . "\n"
            . '<option value="textlist 2">textlist 2</option>' . "\n"
            . '<option value="textlist 3">textlist 3</option>' . "\n"
            . '<option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
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
            . '<option value="textlist 1">textlist 1</option>' . "\n"
            . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
            . '<option value="textlist 3">textlist 3</option>' . "\n"
            . '<option value="textlist 4" data-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
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
            . '<option value="textlist 1">textlist 1</option>' . "\n"
            . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
            . '<option value="textlist 3">textlist 3</option>' . "\n"
            . '<option value="textlist 4" data-sidecar="[1,2,3,4]" selected>textlist 4</option>' . "\n"
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
            . '<option value="textlist 1">textlist 1</option>' . "\n"
            . '<option value="textlist 2" selected>textlist 2</option>' . "\n"
            . '<option value="textlist 3">textlist 3</option>' . "\n"
            . '<option value="textlist 4" data-sidecar="[1,2,3,4]" selected>textlist 4</option>' . "\n"
            . '</select>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a nested select
     */
	public function testFormRendererSimpleHtml_FieldSelectNested() {
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
            . '<option value="General">General</option>' . "\n"
            . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
            . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
            . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '<optgroup label="Subgroup Two">' . "\n"
            . '<option value="S2I1" data-sidecar="&quot;s2i1 side&quot;" selected>Sub Two Item One</option>' . "\n"
            . '<option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
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
            . '<option value="General">General</option>' . "\n"
            . '<optgroup label="Subgroup One" data-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
            . '<option value="Sub One Item One" selected>Sub One Item One</option>' . "\n"
            . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
            . '</optgroup>' . "\n"
            . '<optgroup label="Subgroup Two">' . "\n"
            . '<option value="S2I1" data-sidecar="&quot;s2i1 side&quot;" selected>Sub Two Item One</option>' . "\n"
            . '<option value="S2I2" data-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldTel() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTel();

        $expect = [];
        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="tel"/>' . "\n"
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="tel" readonly/>' . "\n"
            . '<br/>' . "\n"
        );

        // Test read (less than view) access
        $expect['read'] = Block::fromString(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n"
        );

        $this -> runCases($cases, $expect);
    }

	public function testFormRendererSimpleHtml_FieldText() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldText();
        $expect = [];

        // No access specification assumes write access
        $expect['default'] = new Block;
        $expect['default'] -> body = '<input id="field-1" name="field-1" type="text"/>' . "\n"
            . '<br/>' . "\n";

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = new Block;
        $expect['view'] -> body = '<input id="field-1" name="field-1"'
            . ' type="text" readonly/>' . "\n"
            . '<br/>' . "\n";

        // Test read (less than view) access
        $expect['read'] = new Block;
        $expect['read'] -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";

        $this -> runCases($cases, $expect);
    }

    /**
     * Text field with a data list
     */
	public function testFormRendererSimpleHtml_FieldTextDataList() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextDataList();
        $expect = [];

        $expect['basic'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text"'
            . ' list="field-1-list"/>' . "\n"
            . "<datalist id=\"field-1-list\">\n"
            . "<option value=\"textlist 1\"/>\n"
            . "<option value=\"textlist 2\"/>\n"
            . "<option value=\"textlist 3\"/>\n"
            . "<option value=\"textlist 4\" data-sidecar=\"[1,2,3,4]\"/>\n"
            . "</datalist>\n"
            . '<br/>' . "\n"
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text" readonly/>' . "\n"
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
	public function testFormRendererSimpleHtml_FieldTextLabels() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextLabels();
        $expect = [];
        $tail = "<br/>\n";

        // no labels
        $expect['label-none'] = new Block;
        $expect['label-none'] -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="the value"/>' . "\n" . $tail;

        // before
        $expect['label-before'] = new Block;
        $expect['label-before'] -> body = '<span>prefix</span>'
            . '<input id="field-1" name="field-1" type="text"'
            . ' value="the value"/>' . "\n" . $tail;

        // After
        $expect['label-after'] = new Block;
        $expect['label-after'] -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="the value"/>'
            . '<span>suffix</span>' . "\n" . $tail;

        // Heading
        $expect['label-head'] = new Block;
        $expect['label-head'] -> body = '<label for="field-1">Header</label>' . "\n"
            . '<input id="field-1" name="field-1" type="text"'
            . ' value="the value"/>' . "\n" . $tail;

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = new Block;
        $expect['label-inner'] -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="the value"'
            . ' placeholder="inner"/>' . "\n" . $tail;

        // All
        $expect['label-all'] = new Block;
        $expect['label-all'] -> body = '<label for="field-1">Header</label>' . "\n"
            . '<span>prefix</span>'
            . '<input id="field-1" name="field-1" type="text"'
            . ' value="the value" placeholder="inner"/>'
            . '<span>suffix</span>' . "\n" . $tail;

        $this -> runCases($cases, $expect);
    }

    /**
     * Test various validation options
     */
	public function testFormRendererSimpleHtml_FieldTextValidation() {
        $this -> logMethod(__METHOD__);
        $cases = RendererCaseGenerator::html_FieldTextValidation();
        $expect = [];
        $tail = "<br/>\n";
        $expect['required'] = Block::fromString(
            '<input id="field-1" name="field-1"'
            . ' type="text"'
            . ' required/>' . "\n" . $tail
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text"'
            . ' maxlength="10" required/>' . "\n" . $tail
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text"'
            . ' maxlength="10" minlength="3" required/>' . "\n" . $tail
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            '<input id="field-1" name="field-1" type="text"'
            . ' maxlength="10" minlength="3"'
            . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required/>' . "\n" . $tail
        );

        $this -> runCases($cases, $expect);
    }

	public function testFormRendererSimpleHtml_FieldTextarea() {
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
        $expect -> body = '<textarea id="field-1" name="field-1"></textarea>'
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
        $expect -> body = '<textarea id="field-1" name="field-1" readonly></textarea>'
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
    }

   /**
    * Check field as a time element
    */
	public function testFormRendererSimpleHtml_FieldTime() {
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
        $expect -> body = '<input id="field-1" name="field-1" type="time"/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('20:10');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"/>' . "\n"
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
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '19:57');
        $validation -> set('maxValue', '20:19');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"'
            . ' min="19:57" max="20:19"/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"'
            . ' readonly/>' . "\n"
            . '<br/>' . "\n";
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
	public function testFormRendererSimpleHtml_FieldUrl() {
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
        $expect -> body = '<input id="field-1" name="field-1" type="url"/>' . "\n"
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
        $expect -> body = '<input id="field-1" name="field-1" type="url" readonly/>' . "\n"
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
    }

   /**
    * Check field as a week element
    */
	public function testFormRendererSimpleHtml_FieldWeek() {
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
        $expect -> body = '<input id="field-1" name="field-1" type="week"/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-W37');
        $data = $this -> testObj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' type="week" value="2010-W37"/>' . "\n"
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
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-W30');
        $validation -> set('maxValue', '2099-W42');
        $data = $this -> testObj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"'
            . ' min="1957-W30" max="2099-W42"/>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $this -> testObj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"'
            . ' readonly/>' . "\n"
            . '<br/>' . "\n";
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
	public function testFormRendererSimpleHtml_Html() {
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

	public function testFormRendererSimpleHtml_Section() {
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
	public function testFormRendererSimpleHtml_Static() {
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
