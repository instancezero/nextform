<?php

use Abivia\NextForm;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Simple;
use Abivia\NextForm\Element\FieldElement;

/**
 * @covers \Abivia\NextForm\Renderer\Simple
 */
class FormRendererSimpleTest extends \PHPUnit\Framework\TestCase {

	public function testFormRendererSimpleInstantiation() {
        $obj = new Simple();
		$this -> assertInstanceOf('\Abivia\NextForm\Renderer\Simple', $obj);
	}

	public function testFormRendererSimpleStart() {
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
     * @doesNotPerformAssertions
     */
	public function testFormRendererSimpleSetOptions() {
        $obj = new Simple();
        $obj -> setOptions();
    }

	public function testFormRendererSimpleFieldText() {
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
     * Test various label options
     */
	public function testFormRendererSimpleFieldTextLabels() {
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
	public function testFormRendererSimpleFieldTextValidation() {
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
        return;
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $expect -> body = '<label for="field-1">Stuff</label>' . "\n" . $expect -> body;
        $this -> assertEquals($expect, $data);
    }

	public function testFormRendererSimpleFieldTextDataList() {
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
     * Check a simple button
     */
	public function testFormRendererSimpleFieldButton() {
        NextForm::boot();
        $expect = new Block;
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a button
        //
        $schema -> getProperty('test/text') -> getPresentation() -> setType('button');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $element -> setValue('Ok Bob');
        $data = $obj -> render($element);
        $expect -> body = '<input id="field-1" name="field-1" value="Ok Bob" type="button"/><br/>' . "\n";
        $this -> assertEquals($expect, $data);
    }

    /**
     * Test code generation for a radio element
     */
	public function testFormRendererSimpleFieldRadio() {
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
