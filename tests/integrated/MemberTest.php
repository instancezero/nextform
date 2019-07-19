<?php
include_once __DIR__ . '/../JsonComparison.php';

use \Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\FieldElement;
use \Abivia\NextForm;
use Abivia\NextForm\Render\Block;
use Illuminate\Contracts\Translation\Translator as Translator;

class FlatRenderer implements Abivia\NextForm\Contracts\Renderer {

    public function render(Element $element, Translator $translate, $readOnly) {
        $result = new Block;
        $result -> body = $element -> getName();
        if ($element instanceof FieldElement) {
            $result -> body .= ' object = ' . $element -> getObject();
        }
        if ($element instanceof ContainerElement) {
            $result -> post = 'Close ' . $result -> body;
        }
        $result -> body .= "\n";
        return $result;
    }
}

class NullTranslate implements Translator {

    public function trans($key, array $replace = [], $locale = null) {
        return $key;
    }

    public function transChoice($key, $number, array $replace = [], $locale = null) {
        return $key;
    }

    public function getLocale() {
        return 'no-CA';
    }

    public function setLocale($locale) {
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
        $page = $form -> generate('myform.php');
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
        $form -> generate('myform.php');
        $this -> assertTrue(true);
    }

}
