<?php

use Abivia\NextForm\Access\NullAccess;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\HtmlElement;
use Abivia\NextForm\Render\Block;

class MockRender implements RenderInterface
{
    /**
     * Pop the rendering context
     */
    public function popContext()
    {

    }

    /**
     * Push the rendering context
     */
    public function pushContext()
    {

    }

    /**
     * Generate form content for an element.
     * @param Binding $binding The element and data context to be rendered.
     * @param array $options
     *  $options = [
     *      'access' => (string) Access level. One of hide|none|view|write. Default is write.
     *  ]
     * @return Block The generated text and any dependencies, scripts, etc.
     */
    public function render(Binding $binding, $options = []) : Block
    {
        return Block::fromString('rendered');
    }

    public function setOption($key, $value)
        : Abivia\NextForm\Contracts\RenderInterface
    {
        return $this;
    }

    /**
     * Set global options.
     * @param array $options Render-specific options.
     */
    public function setOptions($options = []) {

    }

    /**
     * Set parameters related to the appearance of the form.
     * @param string $settings
     */
    public function setShow($settings) {

    }

    public function showGet($scope, $key)
    {
        return null;
    }

    /**
     * Initiate a form.
     * @param array $options Render-specific options.
     */
    public function start($options = []) : Block
    {

    }

    /**
     * Embed RESTful state data/context into the form.
     * @param array $state
     */
    static public function stateData($state) : Block
    {
        return new Block();
    }

    static public function writeList($list = [], $options = []) : string
    {
        return '';
    }

}

/**
 * @covers Abivia\NextForm\Form\Binding\SimpleBinding
 */
class SimpleBindingTest extends \PHPUnit\Framework\TestCase
{
    public $testObj;

    /**
     * Use a HTML element to create a SimpleBinding
     */
    public function setUp() : void
    {
        $element = new HtmlElement();
        $element->setValue('foo');
        $this->testObj = Binding::fromElement($element);
    }

	public function testGenerate()
    {
        $block = $this->testObj->generate(new MockRender(), new NullAccess());
        $this->assertEquals('rendered', $block->body);
	}

	public function testGetValue()
    {
        $this->assertEquals('foo', $this->testObj->getValue());
	}

    public function testLabels()
    {
        $labels = $this->testObj->getLabels();
        $this->assertInstanceOf('\Abivia\NextForm\Data\Labels', $labels);
        $labels = Labels::build();
        $labels->set('heading', 'Some heading');
        $this->testObj->setLabels($labels);
        $this->testObj->setLabel('error', 'This is an error');
        $testLabels = $this->testObj->getLabels();
        $this->assertEquals($labels, $testLabels);
    }

	public function testTranslate()
    {
        $this->assertEquals($this->testObj, $this->testObj->translate());
	}

}
