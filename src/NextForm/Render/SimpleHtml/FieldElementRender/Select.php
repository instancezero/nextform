<?php

/**
 *
 */
namespace Abivia\NextForm\Render\SimpleHtml\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Select as BaseSelect;

class Select extends BaseSelect {

    /**
     * Generate the input element and any wrapping/supporting code.
     */
    protected function inputGroup(
        Labels $labels,
        Attributes $attrs
    ) : Block
    {
        $select = $this->engine->writeElement('div', ['show' => 'inputWrapperAttributes']);
        $select->body .= $this->engine->writeLabel(
            'before', $labels->before, 'div', null, ['break' => true]
        );

        if ($this->access === 'view') {
            $select->merge($this->renderView($attrs));
        } else {
            // Write access: Add in any validation
            $attrs->addValidation('select', $this->dataProperty->getValidation());

            $select->merge(
                $this->engine->writeElement('select', ['attributes' => $attrs])
            );

            // Add the options
            $select->merge(
                $this->renderOptions(
                    $this->binding->getList(true), $this->value
                )
            );
        }

        $select->body .= $this->engine->writeLabel(
            'after', $labels->after, 'div', null, ['break' => true]
        );

        // Generate help text, if any
        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $attrs->get('aria-describedby'));
            $helpAttrs->set('class', 'form-text text-muted');
            $select->body .= $this->engine->writeTag('small', $helpAttrs, $labels->help) . "\n";
        }

        return $select;
    }

}
