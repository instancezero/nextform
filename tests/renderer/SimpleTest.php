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
        $this -> assertEquals("<form method=\"post\" name=\"bad&lt;name\">\n", $data -> body);
        $data = $obj -> start(['id' => 'bad<name']);
        $this -> assertEquals("<form method=\"post\" id=\"bad&lt;name\">\n", $data -> body);
    }

    /**
     * Check a a button
     */
	public function testFormRendererSimple_Button() {
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
        $expect -> body = '<input id="button-1" name="button-1"'
            . ' value="I am Button!" type="button"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        //
        // Make it a reset
        //
        $element -> set('function', 'reset');
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1"'
            . ' value="I am Button!" type="reset"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Make it a submit
        //
        $element -> set('function', 'submit');
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1"'
            . ' value="I am Button!" type="submit"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Set it back to button
        //
        $element -> set('function', 'button');
        $data = $obj -> render($element);
        $expect -> body = '<input id="button-1" name="button-1"'
            . ' value="I am Button!" type="button"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="button-1" disabled name="button-1" value="I am Button!" type="button"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="button-1" name="button-1" value="I am Button!" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
    }

    /**
     * Test a field with label options
     */
	public function testFormRendererSimple_ButtonLabels() {
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
        $expect -> body = '<input id="button-1" name="button-1"'
            . ' value="I am Button!"' . ' type="button"/>' . $tail;
        $this -> assertEquals($expect, $data);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $expect -> body = '<span>prefix</span>' . $expect -> body;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        // Strip the tail off, add label, re-add tail
        $expect -> body = substr($expect -> body, 0, -strlen($tail))
            . '<span>suffix</span>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $expect -> body = '<label for="button-1">Stuff</label>' . "\n" . $expect -> body;
        $this -> assertEquals($expect, $data);
    }

    /**
     * @doesNotPerformAssertions
     */
	public function testFormRendererSimple_SetOptions() {
        $obj = new Simple();
        $obj -> setOptions();
    }

	public function testFormRendererSimple_FieldText() {
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
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" readonly name="field-1" type="text"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
    }

    /**
     * Test a text field with label options
     */
	public function testFormRendererSimple_FieldTextLabels() {
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
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' value="the value"' . ' type="text"/>' . $tail;
        $this -> assertEquals($expect, $data);
        //
        // Add a inner
        //
        $element -> setLabel('inner', 'Something with & in it');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' value="the value"'
            . ' placeholder="Something with &amp; in it"'
            . ' type="text"/>' . $tail;
        $this -> assertEquals($expect, $data);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $expect -> body = '<span>prefix</span>' . $expect -> body;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        // Strip the tail off, add label, re-add tail
        $expect -> body = substr($expect -> body, 0, -strlen($tail))
            . '<span>suffix</span>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $expect -> body = '<label for="field-1">Stuff</label>' . "\n" . $expect -> body;
        $this -> assertEquals($expect, $data);
    }

    /**
     * Test various validation options
     */
	public function testFormRendererSimple_FieldTextValidation() {
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
        //
        // Set a maximum length
        //
        $validation -> set('maxLength', 10);
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' type="text"'
            . ' maxlength="10" required/>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Make it match a postal code
        //
        $validation -> set('pattern', '/[a-z][0-9][a-z] ?[0-9][a-z][0-9]/');
        // Strip the tail off, add label, re-add tail
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' type="text"'
            . ' maxlength="10" pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required/>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
    }

	public function testFormRendererSimple_FieldTextDataList() {
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
        // Test view access: No list is required
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" readonly name="field-1" type="text"/>' . $tail;
        $expect -> post = null;
        $this -> assertEquals($expect, $data);
        // Test read (less than view) access
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
    }

    /**
     * Check a field as the button types
     */
	public function testFormRendererSimple_FieldButton() {
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
        $expect -> body = '<input id="field-1" name="field-1" value="Ok Bob" type="button"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $presentation -> setType('reset');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" value="Ok Bob" type="reset"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        $presentation -> setType('submit');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" value="Ok Bob" type="submit"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
   }

   /**
    * Check field as a hidden element
    */
	public function testFormRendererSimple_FieldHidden() {
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
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        //
        // Same result with view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $this -> assertEquals($expect, $data);
        //
        // Same result with read access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $this -> assertEquals($expect, $data);
    }

    /**
     * Test a hidden field with label options
     */
	public function testFormRendererSimple_FieldHiddenLabels() {
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
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' value="the value"' . ' type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Add a inner
        //
        $element -> setLabel('inner', 'Something with & in it');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        // Strip the tail off, add label, re-add tail
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
    }

    /**
     * Check a field as a number
     */
	public function testFormRendererSimple_FieldNumber() {
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
        $expect -> body = '<input id="field-1" name="field-1" value="200" type="number"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Make the field required
        //
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('required', true);
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' value="200" type="number"'
            . ' required/>' . $tail;
        $this -> assertEquals($expect, $data);
        //
        // Set minimum/maximum values
        //
        $validation -> set('minValue', -1000);
        $validation -> set('maxValue', 999.45);
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' value="200" type="number"'
            . ' max="999.45" min="-1000" required/>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Add a step
        //
        $validation -> set('step', 1.23);
        $expect -> body = '<input id="field-1" name="field-1"'
            . ' value="200" type="number"'
            . ' max="999.45" min="-1000" required step="1.23"/>' . $tail;
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Settng a pattern should have no effect!
        //
        $validation -> set('pattern', '/[+\-]?[0-9]+/');
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
    }

    /**
     * Test code generation for a radio element
     */
	public function testFormRendererSimple_FieldRadio() {
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
  <input name="field-1" type="radio" id="field-1-opt0" value="textlist 1"/>
  <label for="field-1-opt0">textlist 1</label>
</div>
<div>
  <input name="field-1" type="radio" id="field-1-opt1" value="textlist 2"/>
  <label for="field-1-opt1">textlist 2</label>
</div>
<div>
  <input name="field-1" type="radio" id="field-1-opt2" value="textlist 3"/>
  <label for="field-1-opt2">textlist 3</label>
</div>
<div>
  <input name="field-1" type="radio" id="field-1-opt3" value="textlist 4" data-sidecar="[1,2,3,4]"/>
  <label for="field-1-opt3">textlist 4</label>
</div>
<br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Same result with explicit write access
        //
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals($expect, $data);
        //
        // Set a value to trigger the checked option
        //
        $element -> setValue('textlist 3');
        $expect -> body = str_replace('list 3"', 'list 3" checked', $expect -> body);
        $data = $obj -> render($element);
        $this -> assertEquals($expect, $data);
        //
        // Test view access
        //
        $data = $obj -> render($element, ['access' => 'view']);
        $expect -> body = '<input id="field-1" readonly name="field-1" value="textlist 3" type="text"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
        //
        // Test read (less than view) access
        //
        $data = $obj -> render($element, ['access' => 'read']);
        $expect -> body = '<input id="field-1" name="field-1" value="textlist 3" type="hidden"/>' . "\n";
        $this -> assertEquals($expect, $data);
    }

}
