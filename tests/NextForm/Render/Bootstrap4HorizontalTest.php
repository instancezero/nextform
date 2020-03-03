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
class FormRenderBootstrap4HorizontalTest extends Bootstrap4RenderFrame
{
    use HtmlTestLogger;
    use RenderCaseRunner;

    protected $testObj;

    protected function setUp() : void
    {
        NextForm::boot();
        $this->testObj = new Bootstrap4();
        $this->testObj->setShow('layout:horizontal:2:10');
    }

    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
        self::$defaultFormGroupClass = 'form-group row';
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Bootstrap4());
    }

	public function testInstantiation()
    {
		$this->assertInstanceOf('\Abivia\NextForm\Render\Bootstrap4', $this->testObj);
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
