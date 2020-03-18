<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Textarea as BaseTextarea;

class Textarea extends BaseTextarea {

    /**
     * Get attributes for the input element and add BS4 specifics.
     *
     * @param Labels $labels
     * @return Attributes
     */
    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);
        $attrs->itemAppend('class', 'form-control');

        return $attrs;
    }

    /**
     * Generate the input element and any wrapping/supporting code.
     */
    protected function inputGroup(
        Labels $labels,
        Attributes $attrs,
        $value
    ) : Block
    {
        $input = $this->engine->inputGroupPre($labels);

        // Generate the textarea element
        $input->body .= $this->engine->writeTag('textarea', $attrs, $value)
            . "\n";

        $input->merge($this->engine->inputGroupPost($labels));

        // Generate supporting messages
        $input->body .= $this->engine->writeInputSupport($labels, $attrs);

        return $input;
    }

}
