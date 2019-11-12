<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\Html;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Renderer\Block;

class SectionElement  {

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
     * Write a cell element.
     * @param array $options
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function render($options = [])
    {
        $labels = $this->binding->getLabels(true);
        $block = $this->engine->writeElement(
            'fieldset', [
                'attributes' => $this->engine->groupAttributes($this->binding),
                'show' => 'formGroupAttributes'
            ]
        );
        if ($labels !== null) {
            $block->body .= $this->engine->writeLabel(
                '', $labels->heading, 'legend', null, ['break' => true]
            );
        }

        return $block;
    }

}
