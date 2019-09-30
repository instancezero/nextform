<?php

use Abivia\NextForm\Data\Validation;

/**
 * @covers \Abivia\NextForm\Data\Validation
 */
class DataValidationTest extends \PHPUnit\Framework\TestCase {

	public function testInstantiation() {
        $obj = new Validation();
		$this -> assertInstanceOf('\Abivia\NextForm\Data\Validation', $obj);
	}

    public function testGetBadProperty() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> get('fubar8533b');
    }

    public function testBooleanProperties() {
        $props = [
            'async' => false, 'multiple' => false,
            'required' => false, 'translatePattern' => false
        ];
        $obj = new Validation();
        foreach ($props as $prop => $expectDefault) {
            $this -> assertEquals($expectDefault, $obj -> get($prop), $prop);

            $obj -> set($prop, true);
            $this -> assertEquals(true, $obj -> get($prop), $prop);

            $obj -> set($prop, false);
            $this -> assertEquals(false, $obj -> get($prop), $prop);

        }
    }

    public function testBooleanBadProperty() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('multiple', 'not boolean');
    }

    public function testAccept() {
        $obj = new Validation();
        $this -> assertEquals([], $obj -> get('accept'));

        $obj -> set('accept', 'v1,v2');
        $this -> assertEquals(['v1', 'v2'], $obj -> get('accept'));

        $obj -> set('accept', ['v2', 'v3']);
        $this -> assertEquals(['v2', 'v3'], $obj -> get('accept'));

        $this -> expectException('RuntimeException');
        $obj -> set('accept', new \stdClass);
    }

    public function testCapture() {
        $obj = new Validation();
        $this -> assertEquals(null, $obj -> get('capture'));

        $obj -> set('capture', 'user');
        $this -> assertEquals('user', $obj -> get('capture'));

        $obj -> set('capture', 'environment');
        $this -> assertEquals('environment', $obj -> get('capture'));

        $this -> expectException('RuntimeException');
        $obj -> set('capture', 'server');
    }

    public function testIsEmpty() {
        $obj = new Validation();
        $this -> assertTrue($obj -> isEmpty());
        $props = [
            'accept' => true,
            'async' => true,
            'capture' => 'user',
            'maxLength' => 100,
            'maxValue' => 100,
            'minLength' => 100,
            'minValue' => 100,
            'multiple' => true,
            'pattern' => '/./',
            'required' => true,
            'step' => 100,
            'translatePattern' => true,
        ];
        foreach ($props as $prop => $notEmpty) {
            $obj = new Validation();
            $obj -> set($prop, $notEmpty);
            $this -> assertFalse($obj -> isEmpty(), $prop);
        }
    }

    public function testMaxLength() {
        $obj = new Validation();
        $this -> assertEquals(null, $obj -> get('maxLength'));

        $obj -> set('maxLength', 100);
        $this -> assertEquals(100, $obj -> get('maxLength'));

        $obj -> set('maxLength', '100');
        $this -> assertEquals(100, $obj -> get('maxLength'));
    }

    public function testMaxLengthNotZero() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('maxLength', 0);
    }

    public function testMaxLengthNotNegative() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('maxLength', -10);
    }

    public function testMaxLengthNotString() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('maxLength', 'foo');
    }

    public function testMinLength() {
        $obj = new Validation();
        $this -> assertEquals(null, $obj -> get('minLength'));

        $obj -> set('minLength', 100);
        $this -> assertEquals(100, $obj -> get('minLength'));

        $obj -> set('minLength', '100');
        $this -> assertEquals(100, $obj -> get('minLength'));

        $obj -> set('minLength', 0);
        $this -> assertEquals(0, $obj -> get('minLength'));
    }

    public function testMinLengthNotNegative() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('minLength', -10);
    }

    public function testMinLengthNotString() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('minLength', 'foo');
    }

    public function testMaxValue() {
        $obj = new Validation();
        $this -> assertEquals(null, $obj -> get('maxValue'));

        $obj -> set('maxValue', 100);
        $this -> assertEquals(100, $obj -> get('maxValue'));

        $obj -> set('maxValue', '2000-10-20');
        $this -> assertEquals('2000-10-20', $obj -> get('maxValue'));

        $this -> expectException('RuntimeException');
        $obj -> set('maxValue', 'foo');
    }

    public function testMinValue() {
        $obj = new Validation();
        $this -> assertEquals(null, $obj -> get('minValue'));

        $obj -> set('minValue', 100);
        $this -> assertEquals(100, $obj -> get('minValue'));

        $obj -> set('minValue', '2000-10-20');
        $this -> assertEquals('2000-10-20', $obj -> get('minValue'));

        $this -> expectException('RuntimeException');
        $obj -> set('minValue', 'foo');
    }

    public function testStep() {
        $obj = new Validation();
        $this -> assertEquals(null, $obj -> get('step'));

        $obj -> set('step', 100);
        $this -> assertEquals(100, $obj -> get('step'));

        $obj -> set('step', '2000-10-20');
        $this -> assertEquals('2000-10-20', $obj -> get('step'));
    }

    public function testStepNotNegative() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('step', -100);
    }

    public function testStepNotString() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('step', 'foo');
    }

    public function testPattern() {
        $obj = new Validation();
        $this -> assertEquals(null, $obj -> get('pattern'));

        $obj -> set('pattern', '/[a-z]/');
        $this -> assertEquals('/[a-z]/', $obj -> get('pattern'));
        $this -> assertEquals('[a-z]', $obj -> get('-pattern'));
    }

    public function testPatternNotValid() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('pattern', '/foo');
    }

    public function testPatternNotString() {
        $obj = new Validation();
        $this -> expectException('RuntimeException');
        $obj -> set('pattern', ['/./']);

    }

    public function testValueRange() {
        // Min < max is good
        $config = json_decode('{"minValue":100,"maxValue":200}');
        $obj = new Validation();
        $this -> assertTrue($obj -> configure($config));

        // Min > max
        $config = json_decode('{"minValue":200,"maxValue":100}');
        $obj = new Validation();
        $this -> assertFalse($obj -> configure($config));

    }

}
