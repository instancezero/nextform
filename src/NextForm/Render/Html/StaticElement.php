<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;

class StaticElement  {

    /**
     *
     * @var Binding
     */
    protected $binding;

    /**
     *
     * @var RenderInterface
     */
    protected $engine;

    public function __construct(RenderInterface $engine, Binding $binding) {
        $this->engine = $engine;
        $this->binding = $binding;
    }

    /**
     * Write a HTML element.
     * @param array $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {
        $block = new Block();

        // There's no way to hide this element so if all we have is hidden access, skip it.
        if ($this->engine->getAccess($options) === 'hide') {
            return $block;
        }

        // Push and update the show context
        $element = $this->binding->getElement();
        $show = $element->getShow();
        if ($show !== '') {
            $this->engine->pushContext();
            $this->engine->setShow($show, 'html');
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
        $block->merge($this->engine->epilog());

        // Restore show context and return.
        if ($show !== '') {
            $this->engine->popContext();
        }

        return $block;
    }

}
