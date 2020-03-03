<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Block;

class HtmlElementRender extends AbstractElementRender
{

    /**
     * Write a HTML element.
     * @param array $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {
        $block = new Block();

        // There's no way to hide this element so if all we have is hidden access, skip it.
        $access = $this->engine->getAccess($options);
        if ($access === 'hide' || $access === 'none') {
            return $block;
        }

        $block->body = $this->binding->getElement()->getValue();

        return $block;
    }

}
