<?php

use Abivia\NextForm\Manager;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml
 */
class FormRenderSimpleHtmlTest extends SimpleHtmlRenderFrame {

    protected function setUp() : void {
        Manager::boot();
        $this->testObj = new SimpleHtml();
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
         self::$defaultFormGroupClass = '';
   }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new SimpleHtml());
    }

	public function testInstantiation() {
		$this->assertInstanceOf('\Abivia\NextForm\Render\SimpleHtml', $this->testObj);
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

    /**
     * @doesNotPerformAssertions
     */
	public function testSetOptions() {

        $this->testObj->setOptions();
    }

}
