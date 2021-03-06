<?php

/**
 *
 */
namespace Abivia\NextForm\Render\SimpleHtml\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Textarea as BaseTextarea;

class Textarea extends BaseTextarea {

    /**
     * Generate the input element and any wrapping/supporting code.
     */
    protected function inputGroup(
        Labels $labels,
        Attributes $attrs,
        $text
    ) : Block
    {
        $input = new Block();
        // Generate the textarea element
        $input->body .= $this->engine->writeTag('textarea', $attrs, $text)
            . $this->engine->writeLabel(
                'div', $labels, 'after', null, ['break' => true]
            )
            . "\n";

        // Generate help text, if any
        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $attrs->get('aria-describedby'));
            $helpAttrs->itemAppend('class', 'form-text text-muted');
            $input->body .= $this->engine->writeTag(
                'small',
                $helpAttrs,
                $labels->get('help')
            ) . "\n";
        }

        return $input;
    }

}
