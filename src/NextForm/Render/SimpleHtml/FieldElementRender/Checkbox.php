<?php

/**
 *
 */
namespace Abivia\NextForm\Render\SimpleHtml\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Checkbox as BaseCheckbox;

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

            $tempLabels = Labels::build();
            $tempLabels->set('inner', $radio->getLabel());
            $block->body .= "<div>\n"
                . $this->engine->writeTag('input', $optAttrs) . "\n"
                . $this->engine->writeLabel(
                    'label',
                    $tempLabels,
                    'inner',
                    new Attributes('!for',  $id),
                    ['break' => true]
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
//        $attrs->set('name', $binding->getNameOnForm());

        // Start generating output
        $block = $this->engine->writeElement(
            'div', [
                'attributes' => $this->engine->groupAttributes($this->binding)
            ]
        );
        $block->body .= $this->engine->writeLabel(
            'div',
            $labels,
            ['heading' => 'headingAttributes'],
            null,
            ['break' => true]
        );
        $block->merge($this->engine->writeElement(
            'div', ['show' => 'inputWrapperAttributes'])
        );
        $bracketTag = empty($list) ? 'span' : 'div';
        $block->body .= $this->engine->writeLabel(
            $bracketTag,
            $labels,
            'before',
            null,
            ['break' => !empty($list)]
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
                'label',
                $this->binding->getLabels(true),
                'inner',
                new Attributes('!for', $baseId),
                ['break' => true]
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
            $bracketTag,
            $labels,
            'after',
            null,
            ['break' => !empty($list)]
        );
        $block->close();
        return $block;
    }

    protected function multiple() {

    }

    protected function single() {

    }

}
