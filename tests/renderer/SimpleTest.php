<?php

use Abivia\NextForm;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\ButtonElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Simple;

/**
 * @covers \Abivia\NextForm\Renderer\Simple
 */
class FormRendererSimpleTest extends \PHPUnit\Framework\TestCase {

    static protected $allHtml;

    public static function setUpBeforeClass() : void {
        self::$allHtml = '<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>FormRendererSimpleTest</title>
  </head>
<body>
<form id="someform" name="someform" method="post" action="http://localhost/nextform/post.php">
';
    }

    public static function tearDownAfterClass() : void {
        self::$allHtml .= '</form></body></html>';
        file_put_contents(__DIR__ . '/simple-out.html', self::$allHtml);
    }

    protected function logMethod($method) {
        self::$allHtml .= '<h3>' . $method . "</h3>\n";
    }

    protected function logResult($block) {
        $closing = clone $block;
        $closing -> close();
        self::$allHtml .= $closing -> body;
    }

	public function testFormRendererSimple_Instantiation() {
        $obj = new Simple();
		$this -> assertInstanceOf('\Abivia\NextForm\Renderer\Simple', $obj);
	}

	public function testFormRendererSimple_Start() {
        NextForm::boot();
        $obj = new Simple();
        $data = $obj -> start();
        $this -> assertEquals("<form method=\"post\">\n", $data -> body);
        $this -> assertEquals("</form>\n", $data -> post);
        $data = $obj -> start(['method' => 'put']);
        $this -> assertEquals("<form method=\"put\">\n", $data -> body);
        $data = $obj -> start(['action' => 'https://localhost/some file.php']);
        $this -> assertEquals("<form method=\"post\" action=\"https://localhost/some file.php\">\n", $data -> body);
        $data = $obj -> start(['name' => 'bad<name']);
        $this -> assertEquals("<form name=\"bad&lt;name\" method=\"post\">\n", $data -> body);
        $data = $obj -> start(['id' => 'bad<name']);
        $this -> assertEquals("<form id=\"bad&lt;name\" method=\"post\">\n", $data -> body);
    }

    /**
     * Check a a button
     */
	public function testFormRendererSimple_Button() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $config = json_decode('{"type":"button","labels":{"inner":"I am Button!"}}');
        $obj = new Simple();
        $element = new ButtonElement();
        $element -> configure($config);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1" type="button"'
            . ' value="I am Button!"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Make it a reset
        //
        $element -> set('function', 'reset');
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1" type="reset"'
            . ' value="I am Button!"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Make it a submit
        //
        $element -> set('function', 'submit');
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1" type="submit"'
            . ' value="I am Button!"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set it back to button
        //
        $element -> set('function', 'button');
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1" type="button"'
            . ' value="I am Button!"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="button-1" name="button-1" type="button" value="I am Button!" disabled/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="button-1" name="button-1" type="hidden" value="I am Button!"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test a field with label options
     */
	public function testFormRendererSimple_ButtonLabels() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $tail = "<br/>\n";
        $config = json_decode('{"type":"button","labels":{"inner":"I am Button!"}}');
        $obj = new Simple();
        $element = new ButtonElement();
        $element -> configure($config);
        //
        // Make sure the value shows up
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1" type="button"'
            . ' value="I am Button!"/>' . $tail;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $expect -> body = '<span>prefix</span>' . $expect -> body;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        // Strip the tail off, add label, re-add tail
        $expect -> body = substr($expect -> body, 0, -strlen($tail))
            . '<span>suffix</span>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $expect -> body = '<label for="button-1">Stuff</label>' . "\n" . $expect -> body;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * @doesNotPerformAssertions
     */
	public function testFormRendererSimple_SetOptions() {
        $obj = new Simple();
        $obj -> setOptions();
    }

	public function testFormRendererSimple_FieldText() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="text"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="text" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test a text field with label options
     */
	public function testFormRendererSimple_FieldTextLabels() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $tail = "<br/>\n";
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $ret = $element -> setValue('the value');
        $this -> assertTrue($element === $ret);
        //
        // Make sure the value shows up
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="the value"/>' . $tail;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a inner
        //
        $element -> setLabel('inner', 'Something with & in it');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="the value"'
            . ' placeholder="Something with &amp; in it"'
            . '/>' . $tail;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $expect -> body = '<span>prefix</span>' . $expect -> body;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        // Strip the tail off, add label, re-add tail
        $expect -> body = substr($expect -> body, 0, -strlen($tail))
            . '<span>suffix</span>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $expect -> body = '<label for="field-1">Stuff</label>' . "\n" . $expect -> body;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test various validation options
     */
	public function testFormRendererSimple_FieldTextValidation() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $tail = "<br/>\n";
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $validation = $element -> getDataProperty() -> getValidation();
        //
        // Make the field required
        //
        $validation -> set('required', true);
        $data = $obj -> render($element);
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
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a minimum length
        //
        $validation -> set('minLength', 3);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' maxlength="10" minlength="3" required/>' . $tail;
        $data = $obj -> render($element);
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
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererSimple_FieldTextDataList() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $tail = "<br/>\n";
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        // No access assumes write access
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' list="field-1-list"/>' . $tail;
        $expect -> post = "<datalist id=\"field-1-list\">\n"
            . "  <option value=\"textlist 1\"/>\n"
            . "  <option value=\"textlist 2\"/>\n"
            . "  <option value=\"textlist 3\"/>\n"
            . "  <option value=\"textlist 4\" data-sidecar=\"[1,2,3,4]\"/>\n"
            . "</datalist>\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        // Test view access: No list is required
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="text" readonly/>' . $tail;
        $expect -> post = null;
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        // Test read (less than view) access
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as the button types
     */
	public function testFormRendererSimple_FieldButton() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a button
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('button');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $element -> setValue('Ok Bob');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="button" value="Ok Bob"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        $presentation -> setType('reset');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="reset" value="Ok Bob"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        $presentation -> setType('submit');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="submit" value="Ok Bob"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
   }

    /**
     * Test code generation for a checkbox element
     */
	public function testFormRendererSimple_FieldCheckbox() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a checkbox
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // Give the element a label
        //
        $element -> setLabel('inner', '<Stand-alone> checkbox');
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1[]" type="checkbox"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue(3);
        $expect -> body = '<input id="field-1" name="field-1[]" type="checkbox" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1[]" type="checkbox" value="3" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; checkbox</label>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1[]" type="hidden" value="3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test code generation for a checkbox element with a list
     */
	public function testFormRendererSimple_FieldCheckboxList() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change textWithList to a checkbox
        //
        $schema -> getProperty('test/textWithList') -> getPresentation() -> setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        // No access specification assumes write access
        $data = $obj -> render($element);
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
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value to trigger the checked option
        //
        $element -> setValue('textlist 3');
        $expect -> body = str_replace('list 3"', 'list 3" checked', $expect -> body);
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a second value to trigger the checked option
        //
        $element -> setValue(['textlist 1', 'textlist 3']);
        $expect -> body = str_replace('list 1"', 'list 1" checked', $expect -> body);
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
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
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt0" name="field-1[]" type="hidden" value="textlist 1"/>' . "\n"
            . '<input id="field-1-opt2" name="field-1[]" type="hidden" value="textlist 3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a color element
    */
	public function testFormRendererSimple_FieldColor() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('color');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="color"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('#F0F0F0');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="color" value="#F0F0F0"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="color" value="#F0F0F0" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="#F0F0F0"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a date element
    */
	public function testFormRendererSimple_FieldDate() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('date');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="date"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-10-10');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="date" value="2010-10-10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10-08');
        $validation -> set('maxValue', 'Nov 6th 2099');
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
            . ' min="1957-10-08" max="2099-11-06"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="date" value="2010-10-10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-10-10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a datetime-local element
    */
	public function testFormRendererSimple_FieldDatetimeLocal() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('datetime-local');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-10-10');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10-08');
        $validation -> set('maxValue', '2:15 pm Nov 6th 2099');
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
            . ' min="1957-10-08T00:00" max="2099-11-06T14:15"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="datetime-local" value="2010-10-10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-10-10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererSimple_FieldEmail() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('email');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="email"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('multiple', true);
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="email" multiple/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Turn confirmation on and set some test labels
        //
        $presentation -> setConfirm(true);
        $element -> setLabel('heading', 'Yer email');
        $element -> setLabel('confirm', 'Confirm yer email');
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<label for="field-1">Yer email</label>' . "\n"
            . '<input id="field-1" name="field-1" type="email" multiple/><br/>' . "\n"
            . '<label for="field-1-confirm">Confirm yer email</label>' . "\n"
            . '<input id="field-1-confirm" name="field-1-confirm" type="email" multiple/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $element -> setValue('snafu@fub.ar');
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<label for="field-1">Yer email</label>' . "\n"
            . '<input id="field-1" name="field-1" type="email" value="snafu@fub.ar" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="snafu@fub.ar"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererSimple_FieldFile() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('file');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="file"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('accept', '*.png,*.jpg');
        $validation -> set('multiple', true);
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1[]" type="file" accept="*.png,*.jpg" multiple/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="text" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view with a value
        //
        $element -> setValue(['file1.png', 'file2.jpg']);
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="file1.png,file2.jpg" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1[]" type="hidden" value="file1.png"/>' . "\n"
            . '<input id="field-1" name="field-1[]" type="hidden" value="file2.jpg"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a hidden element
    */
	public function testFormRendererSimple_FieldHidden() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test a hidden field with label options
     */
	public function testFormRendererSimple_FieldHiddenLabels() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $ret = $element -> setValue('the value');
        $this -> assertTrue($element === $ret);
        //
        // Make sure the value shows up
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"'
            . ' value="the value"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a inner
        //
        $element -> setLabel('inner', 'Something with & in it');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        // Strip the tail off, add label, re-add tail
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a month element
    */
	public function testFormRendererSimple_FieldMonth() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('month');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="month"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-10');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="month" value="2010-10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10');
        $validation -> set('maxValue', 'Nov 2099');
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="month" value="2010-10"'
            . ' min="1957-10" max="2099-11"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="month" value="2010-10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a number
     */
	public function testFormRendererSimple_FieldNumber() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $tail = "<br/>\n";
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a number
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('number');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $element -> setValue('200');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="number" value="200"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Make the field required
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('required', true);
        $data = $obj -> render($element);
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
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a step
        //
        $validation -> set('step', 1.23);
        $expect -> body = '<input id="field-1" name="field-1" type="number"'
            . ' value="200"'
            . ' min="-1000" max="999.45" required step="1.23"/>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Settng a pattern should have no effect!
        //
        $validation -> set('pattern', '/[+\-]?[0-9]+/');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $expect -> body = '<input id="field-1" name="field-1" type="number"'
            . ' value="200" readonly/>' . $tail;
        $data = $obj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

	public function testFormRendererSimple_FieldPassword() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a password
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('password');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="password"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="password" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view with a value
        //
        $element -> setValue('secret');
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="password" value="secret" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="secret"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test code generation for a radio element
     */
	public function testFormRendererSimple_FieldRadio() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a radio
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // Give the element a label
        //
        $element -> setLabel('inner', '<Stand-alone> radio');
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="radio"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue(3);
        $expect -> body = '<input id="field-1" name="field-1" type="radio" value="3"/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n";
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="radio" value="3" readonly/>' . "\n"
            . '<label for="field-1">&lt;Stand-alone&gt; radio</label>' . "\n"
            . '<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Test code generation for a radio element with a list
     */
	public function testFormRendererSimple_FieldRadioList() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change textWithList to a radio
        //
        $schema -> getProperty('test/textWithList') -> getPresentation() -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        // No access specification assumes write access
        $data = $obj -> render($element);
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
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value to trigger the checked option
        //
        $element -> setValue('textlist 3');
        $expect -> body = str_replace('list 3"', 'list 3" checked', $expect -> body);
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
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
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1-opt2" name="field-1" type="hidden" value="textlist 3"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a range
     */
	public function testFormRendererSimple_FieldRange() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $tail = "<br/>\n";
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a range
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('range');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $element -> setValue('200');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="range" value="200"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Making the field required should have no effect
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('required', true);
        $data = $obj -> render($element);
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
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Add a step
        //
        $validation -> set('step', 20);
        $expect -> body = '<input id="field-1" name="field-1" type="range"'
            . ' value="200"'
            . ' min="-1000" max="999.45" step="20"/>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Settng a pattern should have no effect!
        //
        $validation -> set('pattern', '/[+\-]?[0-9]+/');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $expect -> body = '<input id="field-1" name="field-1" type="text"'
            . ' value="200" readonly/>' . $tail;
        $data = $obj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a search
     */
	public function testFormRendererSimple_FieldSearch() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a search
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('search');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="search"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="search" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a tel
     */
	public function testFormRendererSimple_FieldTel() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a search
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('tel');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="tel"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="tel" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a time element
    */
	public function testFormRendererSimple_FieldTime() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('time');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="time"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('20:10');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '19:57');
        $validation -> set('maxValue', '20:19');
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"'
            . ' min="19:57" max="20:19"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="time" value="20:10"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="20:10"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

    /**
     * Check a field as a url
     */
	public function testFormRendererSimple_FieldUrl() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a search
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('url');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="url"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="url" readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

   /**
    * Check field as a week element
    */
	public function testFormRendererSimple_FieldWeek() {
        $this -> logMethod(__METHOD__);
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('week');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        //
        // No access specification assumes write access
        //
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="week"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Set a value
        //
        $element -> setValue('2010-W37');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-W30');
        $validation -> set('maxValue', '2099-W42');
        $data = $obj -> render($element, ['access' => 'write']);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"'
            . ' min="1957-W30" max="2099-W42"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Now with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" name="field-1" type="week" value="2010-W37"'
            . ' readonly/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
        //
        // Convert to hidden for read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden" value="2010-W37"/>' . "\n";
        $this -> assertEquals($expect, $data);
        $this -> logResult($data);
    }

}
