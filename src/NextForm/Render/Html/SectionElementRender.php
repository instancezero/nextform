<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Block;

class SectionElementRender  {

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
     * Write a section element.
     * @param array $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {
        // There's no way to hide this element so if all we have is hidden access, skip it.
        if ($this->engine->getAccess($options) === 'hide') {
            return new Block();
        }

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
