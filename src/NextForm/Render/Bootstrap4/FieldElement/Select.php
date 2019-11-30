<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\Bootstrap4\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Html\FieldElement\Select as BaseSelect;

class Select extends BaseSelect {

    /**
     * Get attributes for the input element and add BS4 specifics.
     *
     * @param Labels $labels
     * @return Attributes
     */
    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);

        if ($this->access === 'write') {
            if ($this->engine->showGet('select', 'appearance') === 'custom') {
                $attrs->set('class', 'custom-select');
            } else {
                $attrs->set('class', 'form-control');
            }
        }

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
        $select = $this->engine->inputGroupPre($labels);

        if ($this->access === 'view') {
            $select->merge($this->renderView($attrs));
        } else {
            // Write access: Add in any validation
            $attrs->addValidation('select', $this->dataProperty->getValidation());

            // Generate the actual input element.
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

        $select->merge($this->engine->inputGroupPost($labels));
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
