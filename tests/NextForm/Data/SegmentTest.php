<?php

use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Segment;

/**
 * @covers \Abivia\NextForm\Data\Segment
 */
class DataSegmentTest extends \PHPUnit\Framework\TestCase {

    protected function getProperty($instance, $property) {
        $reflector = new \ReflectionClass($instance);
        $reflectorProperty = $reflector->getProperty($property);
        $reflectorProperty->setAccessible(true);

        return $reflectorProperty->getValue($instance);
    }

	public function testInstantiation() {
        $obj = new Segment();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj);
	}

    public function testLoad() {
        $obj = new Segment();
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/data-segment.json'));
        $this->assertTrue(false != $config, 'JSON error!');
        $populate = $obj->configure($config, true);
        if ($populate) {
            $errors = '';
        } else {
            $errors = $obj->configureGetErrors();
            $errors = 'Segment load:' . "\n" . implode("\n", $errors) . "\n";
        }
        $this->assertTrue($populate, $errors);
        $this->assertEquals('ObjectOne', $obj->getName());
        $this->assertInstanceOf(
            '\Abivia\NextForm\Data\Property', $obj->getProperty('id')
        );
        $this->assertNull($obj->getProperty('some-nonexistent-property'));
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(dirname(__FILE__) . '/segment-dump_actual.txt', $dump);
        $this->assertStringEqualsFile(dirname(__FILE__) . '/segment-dump_expect.txt', $dump);
    }

    public function testLoadBadPrimary()
    {
        $obj = new Segment();
        $config = json_decode(
            file_get_contents(
                dirname(__FILE__) . '/data-segment-bad-primary.json'
            )
        );
        $this->assertTrue(false != $config, 'JSON error!');
        $this->assertFalse($obj->configure($config, true));

    }

    public function testName()
    {
        $obj = new Segment();
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/data-segment.json'));
        $this->assertTrue(false != $config, 'JSON error!');
        $obj->configure($config, true);

        $this->assertEquals('ObjectOne', $obj->getName());

        $obj->setName('foo');
        $this->assertEquals('foo', $obj->getName());
    }

    public function testPrimary()
    {
        $obj = new Segment();
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/data-segment.json'));
        $this->assertTrue(false != $config, 'JSON error!');
        $obj->configure($config, true);
        $obj->setPrimary('id');
        $this->assertEquals(['id'], $obj->getPrimary());

        $this->expectException('\RuntimeException');
        $obj->setPrimary('nonexistent');
    }

    public function testProperty()
    {
        $obj = new Segment();
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/data-segment.json'));
        $this->assertTrue(false != $config, 'JSON error!');
        $obj->configure($config, true);

        $prop = new Property();
        $prop->setName('someprop');
        $obj->setProperty($prop);

        $prop = new Property();
        $this->expectException('\RuntimeException');
        $obj->setProperty($prop);
    }

}
