<?php
include_once __DIR__ . '/test-tools/JsonComparison.php';

class JsonEncoderMain implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'protArr' => 'array',   // left as string to test string-to-array
        'protObj' => [],
        'protVar' => [],
        'privArr' => ['array'],
        'privVar' => [],
        'pubArr' => ['array'],
        'pubVar' => [],
    ];

    protected $protArr = ['key0' => 'element0'];
    protected $protObj = ['protected' => 'protected'];
    protected $protVar = 'protected var';
    private $privArr = ['private' => 'element0'];
    private $privVar = 5;
    public $pubArr = ['key0' => 'element0'];
    public $pubVar = 'public var';

}

class JsonEncoderCommandDropBlank implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'blank' => ['drop:blank'],
        'boring' => [],
    ];

    public $blank = '';
    public $boring = 'dull';
}

class JsonEncoderCommandDropEmpty implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'array1' => ['drop:empty'],
        'array2' => ['drop:empty'],
        'boring' => [],
    ];

    public $array1 = ['stuff'];
    public $array2 = [];
    public $boring = 'dull';
}

class JsonEncoderCommandDropFalse implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'boring' => [],
        'untrue' => ['drop:false'],
    ];

    public $boring = 'dull';
    public $untrue = false;
}

class JsonEncoderCommandDropNull implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'boring' => [],
        'novalue' => ['drop:null'],
    ];

    public $boring = 'dull';
    public $novalue = null;
}

class JsonEncoderCommandDropTrue implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'boring' => [],
        'istrue' => ['drop:true'],
    ];

    public $boring = 'dull';
    public $istrue = true;
}

class JsonEncoderCommandMap implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'boring' => ['map:exciting'],
    ];

    public $boring = 'dull';
}

class JsonEncoderCommandMethod implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'boring' => ['method:changeIt'],
    ];

    public $boring = 'dull';

    private function changeIt($value) {
        return strtoupper($value) . '!';
    }
}

class JsonEncoderCommandScalar implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'arrayToScalar1' => ['scalarize'],
        'arrayToScalar2' => ['scalarize'],
        'boring' => [],
    ];

    public $arrayToScalar1 = ['scalar'];
    public $arrayToScalar2 = ['still', 'an', 'array'];
    public $boring = 'dull';
}

class JsonEncoderTest extends \PHPUnit\Framework\TestCase {
    use JsonComparison;

	public function testJsonEncoderInstantiation() {
        $obj = new JsonEncoderMain();
		$this -> assertInstanceOf('JsonEncoderMain', $obj);
	}

	public function testSimpleValid() {
        $obj = new JsonEncoderMain();
        $actual = json_encode($obj);
        file_put_contents(dirname(__FILE__) . '/json-encoder-actual.json', $actual);
        $this -> assertStringEqualsFile(dirname(__FILE__) . '/json-encoder-expect.json', $actual);
	}

    public function testDropBlank() {
        $obj = new JsonEncoderCommandDropBlank();
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"boring":"dull"}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

    public function testDropEmpty() {
        $obj = new JsonEncoderCommandDropEmpty();
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"boring":"dull","array1":["stuff"]}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

    public function testDropFalse() {
        $obj = new JsonEncoderCommandDropFalse();
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"boring":"dull"}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

    public function testDropNull() {
        $obj = new JsonEncoderCommandDropNull();
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"boring":"dull"}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

    public function testDropTrue() {
        $obj = new JsonEncoderCommandDropTrue();
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"boring":"dull"}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

    public function testMap() {
        $obj = new JsonEncoderCommandMap();
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"exciting":"dull"}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

    public function testMethod() {
        $obj = new JsonEncoderCommandMethod();
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"boring":"DULL!"}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

    public function testScalarize() {
        $obj = new JsonEncoderCommandScalar;
        $actual = json_decode(json_encode($obj));
        $expect = json_decode(
            '{"boring":"dull","arrayToScalar1":"scalar","arrayToScalar2":["still","an","array"]}'
        );
        $this -> assertTrue($this -> jsonCompare($actual, $expect));
    }

}
