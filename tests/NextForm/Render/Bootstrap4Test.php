<?php

use Abivia\NextForm\NextForm;
//use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\CellElement;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4
 */
class FormRenderBootstrap4Test extends Bootstrap4RenderFrame {

    protected function setUp() : void
    {
        NextForm::boot();
        $this->testObj = new Bootstrap4();
        $this->testObj->setShow('layout:vertical:10');
    }

    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
        self::$defaultFormGroupClass = 'form-group col-sm-10';
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Bootstrap4());
    }

	public function testInstantiation() {
		$this->assertInstanceOf('\Abivia\NextForm\Render\Bootstrap4', $this->testObj);
	}

	public function testStart() {
        $attrs = new Attributes();
        $attrs->set('id', 'form_1');
        $attrs->set('name', 'form_1');
        $data = $this->testObj->start(['attributes' => $attrs, 'token' => '']);
        $this->assertEquals("<form id=\"form_1\" name=\"form_1\" method=\"post\">\n", $data->body);
        $this->assertEquals("</form>\n", $data->post);

        $data = $this->testObj->start(['attributes' => $attrs, 'token' => 'foo']);
        $this->assertEquals(
            "<form id=\"form_1\" name=\"form_1\" method=\"post\">\n"
            . '<input id="nf_token" name="nf_token" type="hidden" value="foo">' . "\n",
            $data->body
        );

        $data = $this->testObj->start(['attributes' => $attrs, 'token' => 'foo', 'tokenName' => 'george']);
        $this->assertEquals(
            "<form id=\"form_1\" name=\"form_1\" method=\"post\">\n"
            . '<input id="george" name="george" type="hidden" value="foo">' . "\n",
            $data->body
        );

        $data = $this->testObj->start(['attributes' => $attrs, 'method' => 'put', 'token' => '']);
        $this->assertEquals("<form id=\"form_1\" name=\"form_1\" method=\"put\">\n", $data->body);

        $data = $this->testObj->start(
            ['action' => 'https://localhost/some file.php', 'attributes' => $attrs, 'token' => '']
        );
        $this->assertEquals(
            "<form id=\"form_1\" name=\"form_1\" action=\"https://localhost/some file.php\" method=\"post\">\n",
            $data->body
        );

        $attrs->set('name', 'bad<name');
        $data = $this->testObj->start(
            ['attributes' => $attrs, 'token' => '']
        );
        $this->assertEquals("<form id=\"form_1\" name=\"bad&lt;name\" action=\"https://localhost/some file.php\" method=\"post\">\n", $data->body);

    }

	public function testCell() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Cell();

        $expect['basic'] = Block::fromString(
            '<div class="form-row col-sm-10">' . "\n",
            '</div>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

	public function testCellContext() {
        $this->logMethod(__METHOD__);
        $element = new CellElement();
        $this->assertFalse($this->testObj->queryContext('inCell'));
        $binding = Binding::fromElement($element);
        $this->testObj->render($binding);
        $this->assertTrue($this->testObj->queryContext('inCell'));
    }

    /**
     * @doesNotPerformAssertions
     */
	public function testSetOptions() {

        $this->testObj->setOptions();
    }

}
