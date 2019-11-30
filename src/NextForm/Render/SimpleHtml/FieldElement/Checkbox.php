<?php

/**
 *
 */
namespace Abivia\NextForm\Render\SimpleHtml\FieldElement;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElement\Checkbox as BaseCheckbox;

class Checkbox extends BaseCheckbox {

    protected function checkList(
        Block $block,
        $list,
        $type,
        Attributes $attrs
    ) {
        $baseId = $this->binding->getId();
        $select = $this->binding->getValue();
        if ($select === null) {
            $select = $this->binding->getElement()->getDefault();
        }
        foreach ($list as $optId => $radio) {
            $optAttrs = $attrs->copy();
            $id = $baseId . '_opt' . $optId;
            $optAttrs->set('id', $id);
            $value = $radio->getValue();
            $optAttrs->set('value', $value);
            $optAttrs->setFlag('disabled', !$radio->getEnabled());
            if (
                $type == 'checkbox'
                && is_array($select) && in_array($value, $select)
            ) {
                $optAttrs->setFlag('checked');
                $checked = true;
            } elseif ($value === $select) {
                $optAttrs->setFlag('checked');
                $checked = true;
            } else {
                $optAttrs->setFlag('checked', false);
                $checked = false;
            }
            $optAttrs->setIfNotNull('data-nf-name', $radio->getName());
            $optAttrs->setIfNotEmpty('*data-nf-group', $radio->getGroups());
            $optAttrs->setIfNotNull('*data-nf-sidecar', $radio->sidecar);
            if ($checked) {
                $optAttrs->setFlag('checked');
            } else {
                $optAttrs->setFlag('checked', false);
            }
            $block->body .= "<div>\n" . $this->engine->writeTag('input', $optAttrs) . "\n"
                . $this->engine->writeLabel(
                    '', $radio->getLabel(), 'label',
                    new Attributes('!for',  $id), ['break' => true]
                )
                . "</div>\n";
        }
    }

    /**
     * Get common attributes for the input element and add BS4 specifics.
     *
     * @param Labels $labels
     * @return Attributes
     */
    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);

        return $attrs;
    }

    /**
     * Generate the input and any associated labels, inside a wrapping div.
     *
     * @param Labels $labels
     * @param Attributes $attrs
     * @return Block
     */
    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
    {
        $baseId = $this->binding->getId();

        // Set attributes for the input
        $attrs->setFlag(
            'readonly',
            $this->binding->getElement()->getReadonly()
            || $this->access == 'view'
        );
        $list = $this->binding->getList(true);
//        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);
//        $attrs->set('name', $binding->getFormName());

        // Start generating output
        $block = $this->engine->writeElement(
            'div', [
                'attributes' => $this->engine->groupAttributes($this->binding)
            ]
        );
        $block->body .= $this->engine->writeLabel(
            'headingAttributes', $labels->heading, 'div', null, ['break' => true]
        );
        $block->merge($this->engine->writeElement('div', ['show' => 'inputWrapperAttributes']));
        $bracketTag = empty($list) ? 'span' : 'div';
        $block->body .= $this->engine->writeLabel(
            'before', $labels->before, $bracketTag, null, ['break' => !empty($list)]
        );
        if (empty($list)) {
            $attrs->set('id', $baseId);
            $value = $this->binding->getValue();
            if ($value !== null) {
                $attrs->set('value', $value);
                if ($value === $this->binding->getElement()->getDefault()) {
                    $attrs->setFlag('checked');
                }
            }
            $block->body .= $this->engine->writeTag('input', $attrs) . "\n";
            $block->body .= $this->engine->writeLabel(
                'inner', $this->binding->getLabels(true)->inner,
                'label', new Attributes('!for', $baseId), ['break' => true]
            );
        } else {
            $this->checkList(
                $block,
                $list,
                $attrs->get('type'),
                clone $attrs
            );
        }
        $block->body .= $this->engine->writeLabel(
            'after', $labels->after, $bracketTag, null, ['break' => !empty($list)]
        );
        $block->close();
        return $block;
    }

    protected function multiple() {

    }

    protected function single() {

    }

}
