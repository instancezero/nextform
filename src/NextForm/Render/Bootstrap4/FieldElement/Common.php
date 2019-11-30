<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElement\Common as BaseCommon;

class Common extends BaseCommon {

    /**
     * Get common attributes for the input element and add BS4 specifics.
     *
     * @param Labels $labels
     * @return Attributes
     */
    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);
        $attrs->set('class', 'form-control');
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $attrs->get('id') . '_help');
        }
        if (in_array($this->inputType, ['button', 'reset', 'submit'])) {
            $attrs->set('class', $this->engine->getButtonClass());
        }

        return $attrs;
    }

    /**
     * Generate the input element and any wrapping/supporting code.
     *
     * @param Labels $labels
     * @param Attributes $attrs
     * @return Block
     */
    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
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
