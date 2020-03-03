<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Select as BaseSelect;

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
                $attrs->itemAppend('class', 'custom-select');
            } else {
                $attrs->itemAppend('class', 'form-control');
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

        // Generate supporting messages
        $select->body .= $this->engine->writeInputSupport($labels, $attrs);

        return $select;
    }

}
