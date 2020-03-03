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

    public function emptyToken() {
        return ['', ''];
    }

    public function fooToken() {
        return ['_nf_token', 'foo'];
    }

    public function georgeToken() {
        return ['george', 'foo'];
    }

	public function testStart() {
        $attrs = new Attributes();
        $attrs->set('id', 'form_1');
        $attrs->set('name', 'form_1');

        NextForm::setCsrfGenerator([$this, 'emptyToken']);
        $data = $this->testObj->start(['attributes' => $attrs]);
        $this->assertEquals(
            '<form id="form_1" name="form_1" class="needs-validation"'
            . ' method="post" novalidate>'
            . "\n",
            $data->body
        );
        $this->assertEquals("</form>\n", $data->post);

        NextForm::setCsrfGenerator([$this, 'fooToken']);
        $data = $this->testObj->start(['attributes' => $attrs]);
        $this->assertEquals(
            '<form id="form_1" name="form_1" class="needs-validation"'
            . ' method="post" novalidate>'
            . "\n"
            . '<input id="_nf_token" name="_nf_token" type="hidden" value="foo">' . "\n",
            $data->body
        );

        NextForm::setCsrfGenerator([$this, 'georgeToken']);
        $data = $this->testObj->start(['attributes' => $attrs]);
        $this->assertEquals(
            '<form id="form_1" name="form_1" class="needs-validation"'
            . ' method="post" novalidate>'
            . "\n"
            . '<input id="george" name="george" type="hidden" value="foo">' . "\n",
            $data->body
        );

        NextForm::setCsrfGenerator([$this, 'emptyToken']);
        $data = $this->testObj->start(['attributes' => $attrs, 'method' => 'put']);
        $this->assertEquals(
            '<form id="form_1" name="form_1" class="needs-validation"'
            . ' method="put" novalidate>'
            . "\n",
            $data->body
        );

        $data = $this->testObj->start(
            ['action' => 'https://localhost/some file.php', 'attributes' => $attrs]
        );
        $this->assertEquals(
            "<form id=\"form_1\" name=\"form_1\" class=\"needs-validation\""
            . " action=\"https://localhost/some file.php\""
            . " method=\"post\" novalidate>\n",
            $data->body
        );

        $attrs->set('name', 'bad<name');
        $data = $this->testObj->start(['attributes' => $attrs]);
        $this->assertEquals(
            "<form id=\"form_1\" name=\"bad&lt;name\" class=\"needs-validation\""
            . " action=\"https://localhost/some file.php\""
            . " method=\"post\" novalidate>\n",
            $data->body
        );

    }

	public function testCell() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Cell();

        $expect['basic'] = Block::fromString(
            '<div class="col-sm-10 form-row">' . "\n",
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
