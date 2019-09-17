<?php
include_once __DIR__ . '/../test-tools/JsonComparison.php';
include_once __DIR__ . '/../test-tools/NullTranslate.php';

use Abivia\NextForm;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\ContainerElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\SimpleHtml;
use Abivia\NextForm\Renderer\Bootstrap4;

class FlatRenderer implements Abivia\NextForm\Contracts\Renderer {

    public function __construct($options = []) {

    }

    public function popContext() {
    }

    public function pushContext() {

    }

    public function render(Element $element, $options = []) {
        $result = new Block;
        $type = $element -> getType();
        $result -> body = $type;
        $name = $element -> getName();
        if ($name) {
            $result -> body .= ' (' . $name . ')';
        }
        if ($element instanceof FieldElement) {
            $result -> body .= ' object = ' . $element -> getObject();
        }
        $result -> body .= "\n";
        if ($element instanceof ContainerElement) {
            $result -> post = 'Close ' . $type . "\n";
        }
        return $result;
    }

    public function setOptions($options = []) {

    }

    public function setShow($settings) {

    }

    public function start($options = []) {
        $result = new Block;
        $result -> body = "Form\n";
        $result -> post = "End form\n";
        return $result;
    }

}

class MemberTest extends \PHPUnit\Framework\TestCase {
    use JsonComparison;

    /**
     * Integration test for schema read/write.
     * @coversNothing
     */
    public function testSchemaLoad() {
        $obj = new Schema();
        $jsonFile = __DIR__ . '/member-schema.json';
        $config = json_decode(file_get_contents($jsonFile));
        $this -> assertTrue(false != $config, 'Error JSON decoding schema.');
        $populate = $obj -> configure($config, true);
        if ($populate) {
            $errors = '';
        } else {
            $errors = $obj -> configureGetErrors();
            $errors = 'Schema load:' . "\n" . implode("\n", $errors) . "\n";
        }
        $this -> assertTrue($populate, $errors);
        // Save the result as JSON so we can compare
        $resultJson = json_encode($obj, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/member-schema-out.json', $resultJson);
        // Stock JSON to stdClass for comparison
        $result = json_decode($resultJson);
        $this -> assertTrue($this -> jsonCompare($config, $result));
    }

    /**
     * Integration test for form read/write.
     * @coversNothing
     */
    public function testFormLoad() {
        NextForm::boot();
        $obj = new NextForm();
        $jsonFile = __DIR__ . '/member-form.json';
        $config = json_decode(file_get_contents($jsonFile));
        $this -> assertTrue(false != $config, 'Error JSON decoding form.');
        $populate = $obj -> configure($config, true);
        if ($populate) {
            $errors = '';
        } else {
            $errors = $obj -> configureGetErrors();
            $errors = 'Form load:' . "\n" . implode("\n", $errors) . "\n";
        }
        $this -> assertTrue($populate, $errors);
        // Save the result as JSON so we can compare
        $resultJson = json_encode($obj, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/member-form-out.json', $resultJson);
        // Stock JSON to stdClass for comparison
        $result = json_decode($resultJson);
        $this -> assertTrue($this -> jsonCompare($config, $result));
    }

    /**
     * Integration test for form generation
     * @coversNothing
     */
    public function testGenerateUnpopulated() {
        NextForm::boot();
        $form  = NextForm::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> bindSchema($schema);
        $render = new FlatRenderer();
        $form -> setRenderer($render);
        $form -> setTranslator(new NullTranslate());
        $page = $form -> generate(['action' => 'myform.php']);
        $this -> assertTrue(true);
    }

    /**
     * Integration test for form generation
     * @coversNothing
     */
    public function testGeneratePopulated() {
        NextForm::boot();
        $form  = NextForm::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> bindSchema($schema);
        $data = [
            'id' => 0,
        ];
        $form -> populate($data, 'members');
        $render = new FlatRenderer;
        $form -> setRenderer($render);
        $form -> setTranslator(new NullTranslate());
        $form -> generate(['action' => 'myform.php']);
        $this -> assertTrue(true);
    }

    public function testSimpleHtmlRenderUnpopulated() {
        NextForm::boot();
        $form  = NextForm::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> bindSchema($schema);
        $render = new SimpleHtml();
        $form -> setRenderer($render);
        $form -> setTranslator(new NullTranslate());
        $page = $form -> generate(['action' => 'http://localhost/nextform/post.php']);

        $html = "<!doctype html>\n<html lang=\"en\">\n"
            . "  <head>\n    <meta charset=\"utf-8\">\n"
            . "    <title>" . __FUNCTION__ . "</title>\n"
            . "  </head>\n"
            . "<body>\n" . $page -> body . "</body>\n</html>\n";
        file_put_contents(__DIR__ . '/' . __FUNCTION__ . '.html', $html);
        $this -> assertTrue(true);
    }

    public function testBootstrap4RenderUnpopulated() {
        NextForm::boot();
        $form  = NextForm::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> bindSchema($schema);
        $render = new Bootstrap4();
        $form -> setRenderer($render);
        $form -> setTranslator(new NullTranslate());
        $html = $form -> generate(['action' => 'http://localhost/nextform/post.php']);

        $page = file_get_contents(__DIR__ . '/../test-tools/boilerplate.html');
        $page = str_replace(
            ['{{title}}', '<!--{{head}}-->', '{{form}}', '<!--{{scripts}}-->'],
            [__FUNCTION__, $html -> head, $html -> body, implode("\n", $html -> scripts)],
            $page
        );
        file_put_contents(__DIR__ . '/' . __FUNCTION__ . '.html', $page);
        $this -> assertTrue(true);
    }

}
