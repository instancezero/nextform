<?php

use Abivia\NextForm;
use Abivia\NextForm\Data\Schema;
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
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        // No access assumes write access
        $data = $obj -> render($element);
        $this -> assertEquals(
            '<input id="field-1" name="field-1" type="text"/><br/>' . "\n",
            $data -> body
        );
        // Same result with explicit write access
        $data = $obj -> render($element, ['access' => 'write']);
        $this -> assertEquals(
            '<input id="field-1" name="field-1" type="text"/><br/>' . "\n",
            $data -> body
        );
        // Test view access
        $data = $obj -> render($element, ['access' => 'view']);
        $this -> assertEquals(
            '<input id="field-1" readonly name="field-1" type="text"/><br/>' . "\n",
            $data -> body
        );
        // Test read (less than view) access
        $data = $obj -> render($element, ['access' => 'read']);
        $this -> assertEquals(
            '<input id="field-1" name="field-1" type="hidden"/>' . "\n",
            $data -> body
        );
    }

    /**
     * Test various label options
     */
	public function testFormRendererSimpleFieldTextLabels() {
        NextForm::boot();
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $obj = new Simple();
        $element = new FieldElement();
        $element -> configure($config);
        $element -> linkSchema($schema);
        $ret = $element -> setValue('the value');
        $this -> assertTrue($element === $ret);
        $data = $obj -> render($element);
        $expect = '<input id="field-1" name="field-1"'
            . ' value="the value"'
            . ' type="text"/>';
        $tail = "<br/>\n";
        $this -> assertEquals($expect . $tail, $data -> body);
        $element -> setLabel('placeholder', 'Something with & in it');
        $data = $obj -> render($element);
        $expect = '<input id="field-1" name="field-1"'
            . ' value="the value"'
            . ' placeholder="Something with &amp; in it"'
            . ' type="text"/>';
        $tail = "<br/>\n";
        $this -> assertEquals($expect . $tail, $data -> body);
        //
        // Some text before
        //
        $element -> setLabel('before', 'prefix');
        $expect = '<span>prefix</span>' . $expect;
        $data = $obj -> render($element);
        $this -> assertEquals($expect . $tail, $data -> body);
        //
        // Some text after
        //
        $element -> setLabel('after', 'suffix');
        $expect .= '<span>suffix</span>';
        $data = $obj -> render($element);
        $this -> assertEquals($expect . $tail, $data -> body);
        //
        // Add a heading
        //
        $element -> setLabel('heading', 'Stuff');
        $data = $obj -> render($element);
        $expect = '<label for="field-1">Stuff</label>' . "\n" . $expect;
        $this -> assertEquals($expect . $tail, $data -> body);
    }

}
