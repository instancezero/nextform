<?php

use Abivia\NextForm\Data\Property;

/**
 * @covers \Abivia\NextForm\Data\Property
 */
class DataPropertyTest extends \PHPUnit\Framework\TestCase {

	public function testDataPropertyInstantiation() {
        $obj = new Property();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Property', $obj);
	}

    public function testPropertyMethods() {
        $obj = new Property();
        $this->assertInstanceOf('\Abivia\NextForm\Data\Labels', $obj->getLabels());
        $this->assertEquals(null, $obj->getName());
        $obj->setName('test');
        $this->assertEquals('test', $obj->getName());
		$this->assertInstanceOf('\Abivia\NextForm\Data\Population', $obj->getPopulation());
		$this->assertInstanceOf('\Abivia\NextForm\Data\Presentation', $obj->getPresentation());
        $this->assertEquals(null, $obj->getStore());
    }

    public function testPropertyLoad() {
        $obj = new Property();
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/property.json'));
        $this->assertTrue(false != $config, 'JSON error!');
		$this->assertTrue($obj->configure($config, true));
        $result = json_encode($obj, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/property_actual.json', $result);
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/property_expect.json',
            $result
        );
		$this->assertInstanceOf('\Abivia\NextForm\Data\Population', $obj->getPopulation());
		$this->assertInstanceOf('\Abivia\NextForm\Data\Presentation', $obj->getPresentation());
    }

}
