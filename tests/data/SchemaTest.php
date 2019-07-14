<?php
include_once __DIR__ . '/../Inspection.php';

use \Abivia\NextForm\Data\Schema;

/**
 * @covers \Abivia\NextForm\Data\Schema
 */
class DataSchemaTest extends \PHPUnit\Framework\TestCase {

    use Inspection;

	public function testDataSchemaInstantiation() {
        $obj = new Schema();
		$this -> assertInstanceOf('\Abivia\NextForm\Data\Schema', $obj);
	}

    public function testDataSchemaLoadJson() {
        $obj = new Schema();
        $populate = $obj -> loadDataSchema(__DIR__ . '/data-schema.json');
        if ($populate) {
            $msg = '';
        } else {
            $msg = implode("\n", $obj -> configureGetErrors());
        }
        $this -> assertTrue($populate, $msg);
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(__DIR__ . '/schema-dump_actual.txt', $dump);
        $this -> assertStringEqualsFile(__DIR__ . '/schema-dump_expect.txt', $dump);
    }

    public function testDataSchemaLoadYaml() {
        $obj = new Schema();
        $obj -> loadDataSchema(__DIR__ . '/data-schema.yaml', 'yaml');
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(__DIR__ . '/schema-dump_yamlactual.txt', $dump);
        $this -> assertStringEqualsFile(__DIR__ . '/schema-dump_yamlexpect.txt', $dump);
    }

}
