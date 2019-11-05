<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\CommonHtml;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Renderer\Block;

class HtmlElement  {

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

    /**
     * Write a HTML element.
     * @param array $options
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function render($options = [])
    {
        $block = new Block();

        // There's no way to hide this element so if all we have is hidden access, skip it.
        if ($options['access'] !== 'hide') {
            $block->body = $this->binding->getElement()->getValue();
        }

        return $block;
    }

}
