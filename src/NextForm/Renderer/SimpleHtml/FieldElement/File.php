<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\SimpleHtml\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Html\FieldElement\File as BaseFile;

class File extends BaseFile {

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
        $input = $this->engine->writeElement(
            'div',
            ['show' => 'inputWrapperAttributes']
        );

        $input->body .= $this->engine->writeLabel('before', $labels->before, 'span');
        $input->body .= $this->engine->writeTag('input', $attrs) . "\n";
        $input->body .= $this->engine->writeLabel('after', $labels->after, 'span');

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
