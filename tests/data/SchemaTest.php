<?php
include_once __DIR__ . '/../test-tools/Inspection.php';

use Abivia\NextForm\Data\Schema;

/**
 * @covers \Abivia\NextForm\Data\Schema
 */
class DataSchemaTest extends \PHPUnit\Framework\TestCase {

    use Inspection;

	public function testInstantiation() {
        $obj = new Schema();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Schema', $obj);
	}

    public function testFromFileJson() {
        $obj = Schema::fromFile(__DIR__ . '/data-schema.json');
		$this->assertInstanceOf('\Abivia\NextForm\Data\Schema', $obj);
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(__DIR__ . '/schema-dump_actual.txt', $dump);
        $this->assertStringEqualsFile(__DIR__ . '/schema-dump_expect.txt', $dump);
    }

    public function testFromFileJsonBad() {
        // Expect an error on a bad source
        $this->expectException('\RuntimeException');
        Schema::fromFile(__DIR__ . '/data-schema-bad.json');
    }

    public function testFromFileJsonBadDefault() {
        // Expect an error on a bad source
        $this->expectException('\RuntimeException');
        Schema::fromFile(__DIR__ . '/data-schema-bad-default.json');
    }

    public function testFromFileYaml() {
        $obj = Schema::fromFile(__DIR__ . '/data-schema.yaml');
		$this->assertInstanceOf('\Abivia\NextForm\Data\Schema', $obj);
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(__DIR__ . '/schema-dump_yamlactual.txt', $dump);
        $this->assertStringEqualsFile(__DIR__ . '/schema-dump_expect.txt', $dump);
    }

    public function testGetProperty() {
        $obj = Schema::fromFile(__DIR__ . '/data-schema.json');
        $this->assertNull($obj->getProperty('foo/bar'));
        $this->assertInstanceOf(
            '\Abivia\NextForm\Data\Property', $obj->getProperty('ObjectOne/id')
        );
        $this->assertInstanceOf(
            '\Abivia\NextForm\Data\Property', $obj->getProperty('ObjectOne', 'id2')
        );
    }

}
