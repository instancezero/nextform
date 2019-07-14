<?php

class JsonEncoderMain implements \JsonSerializable {
    use \Abivia\NextForm\JsonEncoder;
    static $jsonEncodeMethod = [
        'protArr' => 'array',
        'protObj' => 'default',
        'protVar' => 'default',
        'privArr' => 'array',
        'privVar' => 'default',
        'pubArr' => 'array',
        'pubVar' => 'default',
    ];

    protected $protArr = ['key0' => 'element0'];
    protected $protObj = ['protected' => 'protected'];
    protected $protVar = 'protected var';
    private $privArr = ['private' => 'element0'];
    private $privVar = 5;
    public $pubArr = ['key0' => 'element0'];
    public $pubVar = 'public var';

}

class JsonEncoderTest extends \PHPUnit\Framework\TestCase {

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

}
