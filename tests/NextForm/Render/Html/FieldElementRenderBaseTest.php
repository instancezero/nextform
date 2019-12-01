<?php

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Html\FieldElementRenderBase;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html;

include_once __DIR__ . '/../HtmlRenderFrame.php';

class MockHtmlCheckbox {
    public function render($options) {
        return Block::fromString('[' . __CLASS__ . ']');
    }
}

class MockHtmlDefault {
    public function render($options) {
        return Block::fromString('[' . __CLASS__ . ']');
    }
}

/**
 * @covers \Abivia\NextForm\Render\Html\FieldElementRenderBase
 */
class NextFormRenderHtmlFieldElementRenderBaseTest extends HtmlRenderFrame
{
    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Html());
    }

    public function testDataList()
    {
        // Get the standard test cases
        $cases = RenderCaseGenerator::html_FieldTextDataList();

        // Create a renderer from the basic case
        $obj = new FieldElementRenderBase(new Html(), $cases['basic'][0]);

        // Set the required attributes
        $attrs = new \Abivia\NextForm\Render\Attributes('id', 'list_test_1');

        $block = $obj->dataList($attrs, 'text', []);
        $expect = Block::fromString(
            '<datalist id="list_test_1_list">' . "\n"
            . '<option value="textlist 1"/>' . "\n"
            . '<option value="textlist 2"'
            . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
            . '<option value="textlist 3"'
            . ' data-nf-name="tl3"/>' . "\n"
            . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
            . '</datalist>' . "\n"
        );
		$this->assertEquals($expect, $block);
    }

	public function testRenderCheckbox()
    {
        // Get the standard test cases
        $cases = RenderCaseGenerator::html_FieldCheckbox();

        // Create a renderer from the basic case
        $obj = new FieldElementRenderBase(new Html(), $cases['basic'][0]);

        // Make it use our mock for the render operation
        $obj->setFieldHandler('checkbox', 'MockHtmlCheckbox');

        $block = $obj->render();
		$this->assertEquals('[MockHtmlCheckbox]', $block->body);
    }

	public function testRenderDefault()
    {
        // Get the standard test cases
        $cases = RenderCaseGenerator::html_FieldTel();

        // Create a renderer from the basic case
        $obj = new FieldElementRenderBase(new Html(), $cases['basic'][0]);

        // Make it use our mock for the render operation
        $obj->setFieldHandler('tel', 'MockHtmlDefault');

        $block = $obj->render();
		$this->assertEquals('[MockHtmlDefault]', $block->body);
    }

	public function testRenderDefaultConfirm()
    {
        // Get the standard test cases
        $cases = RenderCaseGenerator::html_FieldEmail();

        // Create a renderer from the "confirm" case
        $obj = new FieldElementRenderBase(new Html(), $cases['confirm'][0]);

        // Make it use our mock for the render operation
        $obj->setFieldHandler('email', 'MockHtmlDefault');

        $block = $obj->render();
		$this->assertEquals('[MockHtmlDefault][MockHtmlDefault]', $block->body);
    }

}
