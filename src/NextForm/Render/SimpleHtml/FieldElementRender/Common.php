<?php

/**
 *
 */
namespace Abivia\NextForm\Render\SimpleHtml\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Common as BaseCommon;

class Common extends BaseCommon {

    /**
     * Generate the input and any associated labels, inside a wrapping div.
     *
     * @param Labels $labels
     * @param Attributes $attrs
     * @return Block
     */
    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
    {
        $input = $this->engine->writeElement(
            'div', ['show' => 'inputWrapperAttributes']
        );
        $input->body .= $this->engine->writeLabel('span', $labels, 'before');
        // Generate the input element
        $input->body .= $this->engine->writeTag('input', $attrs)
            . $this->engine->writeLabel('span', $labels, 'after') . "\n";
        return $input;
    }

}
