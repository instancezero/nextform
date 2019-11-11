<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\Bootstrap4\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\CommonHtml\FieldElement\File as BaseFile;

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
        $attrs->set('class', 'form-control-file');

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
