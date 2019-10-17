<?php
require_once __DIR__ . '/../test-tools/JsonComparison.php';

use Abivia\NextForm\Form\Form;

/**
 * @covers Abivia\NextForm\Form\Form
 */
class FormTest extends \PHPUnit\Framework\TestCase {
    use JsonComparison;

    public function testFormInstantiation() {
        $obj = new Form();
		$this->assertInstanceOf('\Abivia\NextForm\Form\Form', $obj);
    }

	public function testFormConfiguration() {
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/form.json'));
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Form();
        $populate = $obj->configure($config, ['strict' => true]);
        if ($populate) {
            $errors = '';
        } else {
            $errors = $obj->configureGetErrors();
            $errors = 'Form load:' . "\n" . implode("\n", $errors) . "\n";
        }
        $this->assertTrue($populate, $errors);
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(dirname(__FILE__) . '/form-dump-actual.txt', $dump);
        $this->assertEquals(sha1_file(dirname(__FILE__) . '/form-dump-expect.txt'), sha1($dump));
        $this->assertTrue(true);
	}

    /**
     * Integration test for form read/write.
     */
    public function testFormLoad() {
        $jsonFile = __DIR__ . '/form.json';
        $obj = Form::fromFile($jsonFile);
        // Save the result as JSON so we can compare
        $resultJson = json_encode($obj, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/form-out.json', $resultJson);

        // Stock JSON to stdClass for comparison; reload the config
        $result = json_decode($resultJson);
        $config = json_decode(file_get_contents($jsonFile));
        $this->assertTrue($this->jsonCompare($config, $result));
    }

}
