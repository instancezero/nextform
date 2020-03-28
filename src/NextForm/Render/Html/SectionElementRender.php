<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Block;

class SectionElementRender extends AbstractElementRender
{

    /**
     * Write a section element.
     * @param array $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {
        // There's no way to hide this element so if all we have is hidden access, skip it.
        $access = $this->engine->getAccess($options);
        if ($access === 'hide' || $access === 'none') {
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
                'legend', $labels, ['heading' => ''], null, ['break' => true]
            );
        }

        return $block;
    }

}
