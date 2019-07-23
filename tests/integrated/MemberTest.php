<?php
include_once __DIR__ . '/../test-tools/JsonComparison.php';
include_once __DIR__ . '/../test-tools/NullTranslate.php';

use Abivia\NextForm;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\ContainerElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Simple;
use Illuminate\Contracts\Translation\Translator as Translator;

class FlatRenderer implements Abivia\NextForm\Contracts\Renderer {

    public function __construct($options = []) {

    }

    public function popContext(Block $block, $options = []) {
        return $block -> close();
    }

    public function pushContext($options = []) {

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
        $form  = NextForm::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> linkSchema($schema);
        $render = new FlatRenderer();
        $form -> setRenderer($render);
        $form -> setTranslator(new NullTranslate());
        $page = $form -> generate(['action' => 'myform.php']);
        print_r($page);
        $this -> assertTrue(true);
    }

    /**
     * Integration test for form generation
     * @coversNothing
     */
    public function testGeneratePopulated() {
        $form  = NextForm::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> linkSchema($schema);
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

    public function testSimpleRenderUnpopulated() {
        $form  = NextForm::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> linkSchema($schema);
        $render = new Simple();
        $form -> setRenderer($render);
        $form -> setTranslator(new NullTranslate());
        $page = $form -> generate(['action' => 'myform.php']);

        $html = "<!doctype html>\n<html lang=\"en\">\n"
            . "  <head>\n    <meta charset=\"utf-8\">\n"
            . "    <title>" . __FUNCTION__ . "</title>\n"
            . "  </head>\n"
            . "<body>\n" . $page -> body . "</body>\n</html>\n";
        file_put_contents(__DIR__ . '/' . __FUNCTION__ . '.html', $html);
        $this -> assertTrue(true);
    }

}
