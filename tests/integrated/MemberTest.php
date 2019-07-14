<?php
include_once __DIR__ . '/../JsonComparison.php';

use \Abivia\NextForm\Data\Schema;
use \Abivia\NextForm\Form\Form;

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
        $obj = new Form();
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
    public function testGenerate() {
        $obj = new \Abivia\NextForm;
        $form  = Form::fromFile(__DIR__ . '/member-form.json');
        $schema = Schema::fromFile(__DIR__ . '/member-schema.json');
        $form -> linkSchema($schema);
        $obj -> setForm($form);
        $obj -> generate();
        $this -> assertTrue(true);
    }

}
