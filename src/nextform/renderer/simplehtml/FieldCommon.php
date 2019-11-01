<?php

/**
 *
 */
namespace Abivia\Nextform\Renderer\SimpleHtml;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\Nextform\Renderer\CommonHtml\FieldCommon as BaseCommon;

class FieldCommon extends BaseCommon {

    /**
     * Generate the input and any associated labels, inside a wrapping div.
     *
     * @param Labels $labels
     * @param Attributes $attrs
     * @return Block
     */
    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
    {
        $input = $this->engine->writeElement('div', ['show' => 'inputWrapperAttributes']);
        $input->body .= $this->engine->writeLabel('before', $labels->before, 'span');
        // Generate the input element
        $input->body .= $this->engine->writeTag('input', $attrs)
            . $this->engine->writeLabel('after', $labels->after, 'span') . "\n";
        return $input;
    }

    /**
     * Delimit this element from the next as required.
     *
     * @return Block
     */
    protected function epilog()
    {
        // TODO: put this common code in the rendering engine.
        $block = Block::fromString(
            $this->engine->queryContext('inCell') ? '&nbsp;' : "<br/>\n"
        );
        return $block;
    }

}
