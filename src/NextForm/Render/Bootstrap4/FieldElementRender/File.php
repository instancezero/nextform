<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\File as BaseFile;

class File extends BaseFile {

    /**
     * Get attributes for the input element and add BS4 specifics.
     *
     * @param Labels $labels
     * @return Attributes
     */
    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);
        $attrs->itemAppend('class', 'form-control-file');

        return $attrs;
    }

    /**
     * Generate the input element and any wrapping/supporting code.
     */
    protected function inputGroup(
        Labels $labels,
        Attributes $attrs
    ) : Block
    {
        $input = $this->engine->inputGroup($labels, $attrs);

        // Generate supporting messages
        $input->body .= $this->engine->writeInputSupport($labels, $attrs);

        return $input;
    }

}
