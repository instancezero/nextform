<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\NextForm;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\ButtonElementRenderBase;

class ButtonElementRender extends ButtonElementRenderBase {

    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);
        $attrs->itemAppend('class', $this->engine->getButtonClass());
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $attrs->get('id') . '_help');
        }

        return $attrs;
    }

    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
    {
        if ($labels->has('help')) {
            $attrs->set(
                'aria-describedby',
                $attrs->get('id') . NextForm::HELP_LABEL
            );
        }

        // Generate the input wrapper, if required for a horizontal layout.
        $input = $this->engine->writeElement('div', ['show' => 'inputWrapperAttributes']);

        // Add in the input element and before/after labels
        $input->body .= $this->engine->writeLabel('beforespan', $labels->before, 'span')
            . $this->engine->writeTag('input', $attrs)
            . $this->engine->writeLabel('after', $labels->after, 'span', [])
            . "\n";

        // Generate supporting messages
        $input->body .= $this->engine->writeInputSupport($labels, $attrs);

        return $input;
    }

}
