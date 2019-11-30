<?php

/**
 *
 */
namespace Abivia\NextForm\Render\SimpleHtml\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElement\Textarea as BaseTextarea;

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
                'after', $labels->after, 'div', null, ['break' => true]
            )
            . "\n";

        // Generate help text, if any
        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $attrs->get('aria-describedby'));
            $helpAttrs->set('class', 'form-text text-muted');
            $input->body .= $this->engine->writeTag('small', $helpAttrs, $labels->help) . "\n";
        }

        return $input;
    }

}
