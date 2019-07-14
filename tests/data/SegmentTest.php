<?php

use \Abivia\NextForm\Data\Segment;

class DataSegmentTest extends \PHPUnit\Framework\TestCase {

    protected function getProperty($instance, $property) {
        $reflector = new \ReflectionClass($instance);
        $reflectorProperty = $reflector -> getProperty($property);
        $reflectorProperty -> setAccessible(true);

        return $reflectorProperty -> getValue($instance);
    }

	public function testDataSchemaInstantiation() {
        $obj = new Segment();
		$this -> assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj);
	}

    public function testDataSchemaLoad() {
        $obj = new Segment();
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/data-segment.json'));
        $this -> assertTrue(false != $config, 'JSON error!');
        $this -> assertTrue($obj -> configure($config));
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(dirname(__FILE__) . '/segment-dump_actual.txt', $dump);
        $this -> assertStringEqualsFile(dirname(__FILE__) . '/segment-dump_expect.txt', $dump);
    }

}
