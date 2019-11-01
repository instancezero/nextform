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

    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
    {
        $input = $this->engine->writeElement('div', ['show' => 'input-wrapper']);
        $input->body .= $this->engine->writeLabel('before', $labels->before, 'span');
        // Generate the input element
        $input->body .= $this->engine->writeTag('input', $attrs)
            . $this->engine->writeLabel('after', $labels->after, 'span') . "\n";
        return $input;
    }

    protected function epilog()
    {
        return $this->engine->queryContext('inCell') ? '&nbsp;' : "<br/>\n";
    }

}
