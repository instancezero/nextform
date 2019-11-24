<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\SimpleHtml\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Html\FieldElement\Select as BaseSelect;

class Select extends BaseSelect {

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
