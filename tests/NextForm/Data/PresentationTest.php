<?php

use Abivia\NextForm\Data\Presentation;

/**
 * @covers \Abivia\NextForm\Data\Presentation
 */
class DataPresentationTest extends \PHPUnit\Framework\TestCase {

    /**
     * This will fail if the W3C ever defines an input with type fringle-daffle.
     */
    const BAD_TYPE = 'fringle-daffle';

	public function testInstantiation() {
        $obj = new Presentation();
		$this->assertInstanceOf('\Abivia\NextForm\Data\Presentation', $obj);
	}

    public function testBuild()
    {
        $obj = Presentation::build('text');
		$this->assertInstanceOf('\Abivia\NextForm\Data\Presentation', $obj);
        $this->assertEquals('text', $obj->getType());
        $this->expectException('\RuntimeException');
        $obj = Presentation::build(self::BAD_TYPE);
    }

	public function testConfiguration() {
        $config = json_decode('{"cols": "1","type": "text"}');
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Presentation();
        $this->assertTrue($obj->configure($config, true));
		$this->assertEquals(1, $obj->getCols());
		$this->assertFalse($obj->getConfirm());
		$this->assertEquals('text', $obj->getType());
    }

    public function testTypeValidation() {
        $knownTypes = [
            'checkbox', 'file', 'hidden', 'radio', 'select', 'text', 'textarea', //'textauto',
        ];
        $config = json_decode('{"cols": "1","type": "text"}');
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Presentation();
        foreach ($knownTypes as $type) {
            $config->type = $type;
            $this->assertTrue($obj->configure($config, true));
        }
        $config->type = '&^%* this will never be valid!!';
        $this->assertFalse($obj->configure($config, true));
    }

    public function testSetType() {
        $config = json_decode('{"cols": "1","type": "text"}');
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new Presentation();
        $this->assertTrue($obj->configure($config, true));
        $this->assertEquals('text', $obj->getType());
        $this->assertInstanceOf('\Abivia\NextForm\Data\Presentation', $obj->setType('textarea'));
        $this->assertEquals('textarea', $obj->getType());
        $this->expectException('\RuntimeException');
        $obj->setType(self::BAD_TYPE);
    }

}
