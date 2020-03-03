<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Common as BaseCommon;

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

        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $attrs->get('id') . '_help');
        }
        if (in_array($this->inputType, ['button', 'reset', 'submit'])) {
            $attrs->itemAppend('class', $this->engine->getButtonClass());
        } else {
            $attrs->itemAppend('class', 'form-control');
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

        // Generate supporting messages
        $input->body .= $this->engine->writeInputSupport($labels, $attrs);

        return $input;
    }

}
