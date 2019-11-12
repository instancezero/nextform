<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\SimpleHtml\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Html\FieldElement\Select as BaseSelect;

class Select extends BaseSelect {

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

    /**
     * Generate the input element and any wrapping/supporting code.
     */
    protected function inputGroup(
        Labels $labels,
        Attributes $attrs
    ) : Block
    {
        // Start the input group
        $input = new Block();

        return $input;
    }

}
