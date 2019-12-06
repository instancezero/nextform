<?php

use Abivia\NextForm\Manager;
use Abivia\NextForm\Form\Binding\FieldBinding;
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

	public function testInstantiation()
    {
		$this->assertInstanceOf('\Abivia\NextForm\Render\SimpleHtml', $this->testObj);
	}

    public function testEpilog()
    {
        $block = $this->testObj->epilog();
        $this->assertEquals("<br/>\n", $block->body);

        $this->testObj->setContext('inCell', true);
        $block = $this->testObj->epilog();
        $this->assertEquals('&nbsp;', $block->body);
    }

    public function testRenderTriggers()
    {
        $block = $this->testObj->renderTriggers(new FieldBinding());
        $this->assertInstanceof('\Abivia\NextForm\Render\Block', $block);
    }

    public function testOptions()
    {
        // Yes this is just a stub.
        $this->testObj->setOptions([]);
        $this->assertTrue(true);
    }

    /**
     * Exercise the protected checkShowState() function by introducing
     * a nonstandard scope
     */
    public function testCheckShowState()
    {
        $this->testObj->showDoCellspacing('new*state', 'a', ['1']);
        $attrs = $this->testObj->showGet('new*state', 'cellspacing');
        $style = $attrs->get('style');
        $this->assertEquals('1rem', $style['padding-left']);
    }

    public function testShowDoCellspacing()
    {
        $this->testObj->showDoCellspacing('form', 'a', ['1']);
        $attrs = $this->testObj->showGet('form', 'cellspacing');
        $style = $attrs->get('style');
        $this->assertEquals('1rem', $style['padding-left']);

        $this->testObj->showDoCellspacing('form', 'b', ['sm-1', 'sh-md-2', 'b4-md-5']);
        $attrs = $this->testObj->showGet('form', 'cellspacing');
        $style = $attrs->get('style');
        $this->assertEquals('1.6rem', $style['padding-left']);
    }

    public function testShowDoLayout()
    {
        $this->testObj->showDoLayout('form', 'horizontal', ['h']);
        $attrs = $this->testObj->showGet('form', 'cellspacing');
        $style = $attrs->get('style');
        $this->assertEquals('1.2rem', $style['padding-left']);

        $this->testObj->showDoLayout('form', 'vertical', ['v']);
        $attrs = $this->testObj->showGet('form', 'cellspacing');
        $style = $attrs->get('style');
        $this->assertEquals('1.2rem', $style['padding-left']);
    }

    public function testShowDoLayoutAnyHorizontal()
    {
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h']);
        $attrs = $this->testObj->showGet('form', 'headingAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('25%', $style['width']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('75%', $style['width']);

    }

    public function testShowDoLayoutAnyHorizontalCss1()
    {
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h', '100px']);
        $attrs = $this->testObj->showGet('form', 'headingAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('100px', $style['width']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $this->assertEquals(null, $attrs);

    }

    public function testShowDoLayoutAnyHorizontalClass1()
    {
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h', '.foo']);
        $attrs = $this->testObj->showGet('form', 'headingAttributes');
        $class = $attrs->get('class');
        $this->assertEquals('foo', $class[0]);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $this->assertEquals(null, $attrs);

    }

    public function testShowDoLayoutAnyHorizontalCss2()
    {
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h', '100px', '300px']);
        $attrs = $this->testObj->showGet('form', 'headingAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('100px', $style['width']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('300px', $style['width']);

    }

    public function testShowDoLayoutAnyHorizontalClass2()
    {
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h', '.foo', '.bar']);
        $attrs = $this->testObj->showGet('form', 'headingAttributes');
        $class = $attrs->get('class');
        $this->assertEquals('foo', $class[0]);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $class = $attrs->get('class');
        $this->assertEquals('bar', $class[0]);

    }

    public function testShowDoLayoutAnyHorizontalRatio2()
    {
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h', '1', '2.5']);
        $attrs = $this->testObj->showGet('form', 'headingAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('28.571%', $style['width']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('71.429%', $style['width']);

        $this->expectException('\RuntimeException');
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h', '1', '0']);
    }

    public function testShowDoLayoutAnyHorizontalRatio3()
    {
        $this->testObj->showDoLayoutAnyHorizontal('form', ['h', '1', '2', '5']);
        $attrs = $this->testObj->showGet('form', 'headingAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('20%', $style['width']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('40%', $style['width']);

    }

    public function testShowDoLayoutAnyVertical()
    {
        $this->testObj->showDoLayoutAnyVertical('form', ['v']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $this->assertEquals(null, $attrs);

    }

    public function testShowDoLayoutAnyVerticalCss1()
    {
        $this->testObj->showDoLayoutAnyVertical('form', ['v', '500px']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('500px', $style['width']);

    }

    public function testShowDoLayoutAnyVerticalClass1()
    {
        $this->testObj->showDoLayoutAnyVertical('form', ['v', '.foo']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $class = $attrs->get('class');
        $this->assertEquals('foo', $class[0]);

    }

    public function testShowDoLayoutAnyVerticalRatio2()
    {
        $this->testObj->showDoLayoutAnyVertical('form', ['v', '6', '10']);
        $attrs = $this->testObj->showGet('form', 'inputWrapperAttributes');
        $style = $attrs->get('style');
        $this->assertEquals('60%', $style['width']);

        $this->expectException('\RuntimeException');
        $this->testObj->showDoLayoutAnyVertical('form', ['v', '1', '0']);
    }

	public function testStart()
    {
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
