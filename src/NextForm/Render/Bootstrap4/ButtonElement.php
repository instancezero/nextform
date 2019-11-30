<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\ButtonElementRenderBase;

class ButtonElement extends ButtonElementRenderBase {

    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);
        $attrs->set('class', $this->engine->getButtonClass());
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $attrs->get('id') . '_help');
        }

        return $attrs;
    }

    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
    {
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $attrs->get('id') . '_formhelp');
        }

        // Generate the input wrapper, if required for a horizontal layout.
        $input = $this->engine->writeElement('div', ['show' => 'inputWrapperAttributes']);

        // Add in the input element and before/after labels
        $input->body .= $this->engine->writeLabel('beforespan', $labels->before, 'span')
            . $this->engine->writeTag('input', $attrs)
            . $this->engine->writeLabel('after', $labels->after, 'span', [])
            . "\n";

        // Generate help text, if any
        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $attrs->get('aria-describedby'));
            $helpAttrs->set('class', 'form-text text-muted');
            $input->body .= $this->engine->writeLabel(
                'help', $labels->help, 'small',
                $helpAttrs, ['break' => true]
            );
        }

        return $input;
    }

}
