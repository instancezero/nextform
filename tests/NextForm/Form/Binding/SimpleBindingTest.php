<?php

use Abivia\NextForm\Access\NullAccess;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\SimpleBinding;
use Abivia\NextForm\Form\Element\ButtonElement;
use Abivia\NextForm\Form\Element\CellElement;
use Abivia\NextForm\Form\Element\FieldElement;
use Abivia\NextForm\Form\Element\HtmlElement;
use Abivia\NextForm\Form\Element\SectionElement;
use Abivia\NextForm\Form\Element\StaticElement;
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
    public function stateData($state) : Block
    {
        return new Block();
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

	public function testTranslate()
    {
        $this->assertEquals($this->testObj, $this->testObj->translate());
	}

}
