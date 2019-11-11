<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\CommonHtml;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;

class StaticElement  {

    /**
     *
     * @var Binding
     */
    protected $binding;

    /**
     *
     * @var RendererInterface
     */
    protected $engine;

    public function __construct(RendererInterface $engine, Binding $binding) {
        $this->engine = $engine;
        $this->binding = $binding;
    }

    protected function epilog()
    {
        return new Block();
    }

    /**
     * Write a HTML element.
     * @param array $options
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function render($options = [])
    {
        $block = new Block();

        // There's no way to hide this element so if all we have is hidden access, skip it.
        if ($options['access'] === 'hide') {
            return $block;
        }

        // Push and update the show context
        $element = $this->binding->getElement();
        $show = $element->getShow();
        if ($show !== '') {
            $this->pushContext();
            $this->setShow($show, 'html');
        }

        // We can see or change the data. Create a form group.
        $block = $this->engine->writeElement(
            'div', [
                'attributes' => $this->engine->groupAttributes($this->binding),
                'show' => 'formGroupAttributes'
            ]
        );

        // Write a heading if there is one
        $labels = $this->binding->getLabels(true);
        $block->body .= $this->engine->writeLabel(
            'headingAttributes',
            $labels ? $labels->heading : null,
            'div', null, ['break' => true]
        );
        $block->merge($this->engine->writeElement('div', ['show' => 'inputWrapperAttributes']));

        $attrs = new Attributes('id', $this->binding->getId());
        $block->merge($this->engine->writeElement('div', ['attributes' => $attrs]));
        // Escape the value if it's not listed as HTML
        $value = $this->binding->getValue() . "\n";
        $block->body .= $element->getHtml() ? $value : htmlspecialchars($value);
        $block->close();
        $block->merge($this->epilog());

        // Restore show context and return.
        if ($show !== '') {
            $this->popContext();
        }

        return $block;
    }

}
