<?php

use Abivia\NextForm\NextForm;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\CellElement;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml
 */
class FormRenderSimpleHtmlHorizontalTest extends SimpleHtmlRenderFrame {
    use HtmlTestLogger;

    protected $emptyLabel;
    protected $testObj;

    protected function setUp() : void {
        NextForm::boot();
        $this->testObj = new SimpleHtml();
        $this->testObj->setShow('layout:hor:20%:40%');
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

	public function testCell() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Cell();

        $expect['basic'] = Block::fromString(
            '<div style="display:inline-block; vertical-align:top; width:40%">' . "\n",
            '</div>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

	public function testCellContext() {
        $this->logMethod(__METHOD__);
        $element = new CellElement();
        $binding = Binding::fromElement($element);
        $this->assertFalse($this->testObj->queryContext('inCell'));
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
