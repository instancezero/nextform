<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Bootstrap4\FieldElementRender;

use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Manager;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRender\Checkbox as BaseCheckbox;

class Checkbox extends BaseCheckbox {
    protected $attrs;

    /**
     * Generate a simple input element for a single-valued checkbox.
     * @param FieldBinding $binding
     * @param \Abivia\NextForm\Render\Attributes $attrs
     * @return \Abivia\NextForm\Render\Block $block The output block.
     */
    protected function checkInput(FieldBinding $binding, Attributes $attrs)
    {
        // This is a single-valued element
        $attrs->set('id', $binding->getId());
        $attrs->setIfNotNull('value', $binding->getValue());
        if (
            $binding->getValue() === $binding->getElement()->getDefault()
            && $binding->getValue()  !== null
        ) {
            $attrs->setFlag('checked');
        }
        return Block::fromString($this->engine->writeTag('input', $attrs) . "\n");
    }

    /**
     * Generate check/radio HTML inputs from an element's data list.
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Render\Attributes $attrs Parent element attributes.
     * @return \Abivia\NextForm\Render\Block $block The output block.
     */
    protected function checkList(FieldBinding $binding, Attributes $attrs)
    {
        $baseId = $binding->getId();
        $type = $binding->getDataProperty()->getPresentation()->getType();
        $select = $binding->getValue();
        if ($select === null) {
            $select = $binding->getElement()->getDefault();
        }
        $appearance = $this->engine->showGet('check', 'appearance');
        $checkLayout = $this->engine->showGet('check', 'layout');
        $groupClass = 'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '');
        $labelAttrs = new Attributes();
        $labelAttrs->set('class', 'form-check-label');
        $block = new Block();
        foreach ($binding->getList(true) as $optId => $radio) {
            $optAttrs = $attrs->copy();
            $id = $baseId . '_opt' . $optId;
            $optAttrs->set('id', $id);
            $value = $radio->getValue();
            $optAttrs->setFlag('disabled', !$radio->getEnabled());
            $optAttrs->set('value', $value);
            if (
                $type == 'checkbox'
                && is_array($select) && in_array($value, $select)
            ) {
                $checked = true;
            } elseif ($value === $select) {
                $checked = true;
            } else {
                $checked = false;
            }
            $optAttrs->setFlag('checked', $checked);
            $optAttrs->setIfNotNull('data-nf-name', $radio->getName());
            $optAttrs->setIfNotEmpty('*data-nf-group', $radio->getGroups());
            $optAttrs->setIfNotNull('*data-nf-sidecar', $radio->sidecar);

            $block->merge(
                $this->engine->writeElement(
                    'div', ['attributes' => new Attributes('class', $groupClass)]
                )
            );
            $optAttrs->set('class', 'form-check-input');
            if ($appearance === 'no-label') {
                $optAttrs->set('aria-label', $radio->getLabel());
            }
            $block->body .= $this->engine->writeTag('input', $optAttrs) . "\n";
            if ($appearance !== 'no-label') {
                $labelAttrs->set('!for', $id);
                $block->body .= $this->engine->writeLabel(
                    '', $radio->getLabel(), 'label',
                    $labelAttrs, ['break' => true]
                )
                ;
            }
            $block->close();
        }
        return $block;
    }

    /**
     * Generate check/radio HTML inputs as buttons from an element's data list.
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Render\Attributes $attrs Parent element attributes.
     * @return \Abivia\NextForm\Render\Block $block The output block.
     */
    protected function checkListButtons(FieldBinding $binding, Attributes $attrs)
    {
        $baseId = $binding->getId();
        $type = $binding->getDataProperty()->getPresentation()->getType();
        $select = $binding->getValue();
        if ($select === null) {
            $select = $binding->getElement()->getDefault();
        }
        // We know the appearance is going to be button or toggle
        //$appearance = $this->showGet('check', 'appearance');
        //$checkLayout = $this->showGet('check', 'layout');
        $labelAttrs = new Attributes();
        $block = new Block();
        foreach ($binding->getList(true) as $optId => $radio) {
            $optAttrs = $attrs->copy();
            $id = $baseId . '_opt' . $optId;
            $optAttrs->set('id', $id);
            $value = $radio->getValue();
            $optAttrs->set('value', $value);
            $optAttrs->setIfNotNull('data-nf-name', $radio->getName());
            $optAttrs->setIfNotEmpty('*data-nf-group', $radio->getGroups());
            $optAttrs->setIfNotNull('*data-nf-sidecar', $radio->sidecar);
            if (
                $type == 'checkbox'
                && is_array($select) && in_array($value, $select)
            ) {
                $checked = true;
            } elseif ($value === $select) {
                $checked = true;
            } else {
                $checked = false;
            }
            if ($checked) {
                $optAttrs->setFlag('checked');
            }
            $show = $radio->getShow();
            if ($show) {
                $this->engine->pushContext();
                $this->engine->setShow($show, 'radio');
            }
            $buttonClass = $this->engine->getButtonClass('radio');
            $labelAttrs->set('class', $buttonClass . ($checked ? ' active' : ''));
            $block->merge($this->engine->writeElement('label', ['attributes' => $labelAttrs]));
            $block->body .= $this->engine->writeTag('input', $optAttrs) . "\n";
            $block->body .= $radio->getLabel();
            $block->close();
            if ($show) {
                $this->engine->popContext();
            }
        }
        return $block;
    }

    /**
     * Generate a single check box/radio input.
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Render\Attributes $attrs
     * @param \Abivia\NextForm\Render\Attributes $groupAttrs
     * @return \Abivia\NextForm\Render\Block $block The output block.
     */
    protected function checkSingle(
        FieldBinding $binding,
        Attributes $attrs,
        Attributes $groupAttrs
    ) {
        $baseId = $binding->getId();
        $labels = $binding->getLabels(true);
        $appearance = $this->engine->showGet('check', 'appearance');
        $block = $this->engine->writeElement('div', ['attributes' => $groupAttrs]);
        if ($labels->has('help')) {
            $attrs->set(
                'aria-describedby',
                $baseId . Manager::HELP_LABEL
            );
        }
        $attrs->set('class', 'form-check-input');
        if ($appearance === 'no-label') {
            $attrs->setIfNotNull('aria-label', $labels->inner);
            $block->merge($this->checkInput($binding, $attrs));
        } else {
            $block->merge($this->checkInput($binding, $attrs));
            $labelAttrs = new Attributes();
            $labelAttrs->set('!for', $baseId);
            $labelAttrs->set('class', 'form-check-label');
            $block->body .= $this->engine->writeLabel(
                'inner', $labels->inner,
                'label', $labelAttrs, ['break' => true]
            );
        }
        $block->close();
        return $block;
    }

    /**
     * Render a single-valued checkbox as a button
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Render\Attributes $attrs
     * @param \Abivia\NextForm\Render\Attributes $groupAttrs
     * @return \Abivia\NextForm\Render\Block $block The output block.
     */
    protected function checkSingleButton(
        FieldBinding $binding,
        Attributes $attrs,
        Attributes $groupAttrs
    ) {
        $baseId = $binding->getId();
        $attrs->set('id', $baseId);
        $labels = $binding->getLabels(true);
        $block = $this->engine->writeElement('div', ['attributes' => $groupAttrs]);
        if ($labels->has('help')) {
            $attrs->set(
                'aria-describedby',
                $baseId . Manager::HELP_LABEL
            );
        }
        $labelAttrs = new Attributes();
        $buttonClass = $this->engine->getButtonClass('radio');
        $checked = $binding->getValue() === $binding->getElement()->getDefault()
            && $binding->getValue() !== null;
        $labelAttrs->set('class', $buttonClass . ($checked ? ' active' : ''));
        $block->merge($this->engine->writeElement('label', ['attributes' => $labelAttrs]));
        $block->body .= $this->engine->writeTag('input', $attrs) . "\n";
        $block->body .= $labels->inner;
        $block->close();

        return $block;
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
     * Generate the input element and any wrapping/supporting code.
     *
     * @param Labels $labels
     * @param Attributes $attrs
     * @return Block
     */
    protected function inputGroup(Labels $labels, Attributes $attrs) : Block
    {
        $this->attrs = $attrs;
        if (empty($this->binding->getList(true))) {
            $input = $this->single();
        } else {
            $input = $this->multiple();
        }

        return $input;
    }

    protected function multiple() {
        $appearance = $this->engine->showGet('check', 'appearance');
        $layout = $this->engine->showGet('form', 'layout');

        $block = new Block();
        $baseId = $this->binding->getId();
        $labels = $this->binding->getLabels(true);



        // If this is showing as a row of buttons change the group attributes
        $groupAttrs = new Attributes();
        if ($appearance === 'toggle') {
            $asButtons = true;
            $groupAttrs->set('class', 'btn-group btn-group-toggle');
            $groupAttrs->set('data-toggle', 'buttons');

            // For buttons, write before/after labels on the same line
            $labelElement = 'span';
        } else {
            $checkLayout = $this->engine->showGet('check', 'layout');
            // Non-buttons can be stacked (default) or inline
            $asButtons = false;
            $groupAttrs->set(
                'class',
                'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '')
            );
            $labelElement = 'div';
        }

        // Customize the header to align baselines in horizontal layouts
        $headerAttrs = new Attributes();
        if ($layout === 'vertical') {
            $rowBlock = $this->engine->writeElement(
                'fieldset', [
                    'attributes' => $this->engine->groupAttributes($this->binding),
                    'show' => 'formGroupAttributes'
                ]
            );
            $headerElement = 'div';
        } else {
            // Horizontal layouts has a fieldset with just the form group class
            $rowAttrs = new Attributes('class', 'form-group');
            $rowAttrs->merge($this->engine->groupAttributes($this->binding));
            $rowBlock = $this->engine->writeElement(
                'fieldset', ['attributes' => $rowAttrs]
            );
            // Horizontal layouts have another div for the row
            $rowBlock->merge($this->engine->writeElement(
                'div', ['attributes' => new Attributes('class', 'row')])
            );
            $headerElement = 'legend';
            if (!$asButtons && $this->access == 'write') {
                $headerAttrs->set('class', 'pt-0');
            }
        }

        // Write the heading. We added a pt-0 for horizontal layouts
        $block->body .= $this->engine->writeLabel(
            'headingAttributes',
            $labels->heading,
            $headerElement,
            $headerAttrs,
            ['break' => true]
        );

        if ($layout === 'horizontal') {
            // Create the second column for a horizontal layout
            $block->merge($this->engine->writeElement(
                'div', ['show' => 'inputWrapperAttributes']
            ));
        }

        // Generate everything associated with the inputs, including before/after texts
        $input = new Block();
        $input->body .= $this->engine->writeLabel(
            'before' . $labelElement, $labels->before, $labelElement
        );
        if ($labels->has('help')) {
            $this->attrs->set(
                'aria-describedby',
                $baseId . Manager::HELP_LABEL
            );
        }
        if ($asButtons) {
            $input->merge($this->engine->writeElement('div', ['attributes' => $groupAttrs]));
            $input->merge($this->checkListButtons($this->binding, clone $this->attrs));
        } else {
            $input->merge($this->checkList($this->binding, clone $this->attrs));
        }
        $input->close();

        // Write any after-label
        $input->body .= $this->engine->writeLabel(
            'after', $labels->after, $labelElement, [], ['break' => true]
        );

        $block->merge($input);

        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $this->attrs->get('aria-describedby'));
            $helpAttrs->set('class', 'form-text text-muted');
            $block->body .= $this->engine->writeLabel(
                'help', $labels->help, 'small',
                $helpAttrs, ['break' => true]
            );
        }
        $block->close();
        $rowBlock->merge($block);
        $rowBlock->close();
        return $rowBlock;
    }

    protected function single() {
        $appearance = $this->engine->showGet('check', 'appearance');
        $checkLayout = $this->engine->showGet('check', 'layout');
        $block = new Block();
        $labels = $this->binding->getLabels(true);


        // Generate hidden elements and return.
        if ($this->access === 'hide') {
            $this->attrs->set('type', 'hidden');
            $block->merge($this->checkInput($this->binding, $this->attrs));
            return $block;
        }
        if ($this->access == 'view') {
            $this->attrs->setFlag('readonly');
        }

        // If this is showing as a row of buttons change the group attributes
        $groupAttrs = new Attributes();
        if ($appearance === 'toggle') {
            $asButtons = true;
            $groupAttrs->set('class', 'btn-group btn-group-toggle');
            $groupAttrs->set('data-toggle', 'buttons');
        } else {
            // Non-buttons can be stacked (default) or inline
            $asButtons = false;
            $groupAttrs->set(
                'class',
                'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '')
            );
        }

        // Customize the header to align baselines in horizontal layouts
        $headerAttrs = new Attributes();
        $rowBlock = $this->engine->writeElement(
            'div', [
                'attributes' => $this->engine->groupAttributes($this->binding),
                'show' => 'formGroupAttributes'
            ]
        );
        if ($this->engine->showGet('form', 'layout') !== 'vertical') {
            if (!$asButtons && $this->access == 'write') {
                $headerAttrs->set('class', 'pt-0');
            }
        }

        // Write the heading. We added a pt-0 for horizontal non-button layouts
        $block->body .= $this->engine->writeLabel(
            'headingAttributes', $labels->heading, 'div', $headerAttrs, ['break' => true]
        );
        if ($this->engine->showGet('form', 'layout') === 'horizontal') {
            // Create the second column for a horizontal layout
            $block->merge($this->engine->writeElement('div', ['show' => 'inputWrapperAttributes']));
        }

        // Generate everything associated with the inputs, including before/after texts
        $input = new Block();
        $input->body .= $this->engine->writeLabel(
            'beforespan', $labels->before, 'span'
        );
        if ($asButtons) {
            $input->merge($this->checkSingleButton($this->binding, $this->attrs, $groupAttrs));
        } else {
            $input->merge($this->checkSingle($this->binding, $this->attrs, $groupAttrs));
        }
        $input->body .= $this->engine->writeLabel(
            'after', $labels->after, 'span', null, ['break' => true]
        );
        $input->close();
        $block->merge($input);

        // Write any help text
        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $this->attrs->get('aria-describedby'));
            $helpAttrs->set('class', 'form-text text-muted');
            $block->body .= $this->engine->writeLabel(
                'help', $labels->help, 'small',
                $helpAttrs, ['break' => true]
            );
        }
        $rowBlock->merge($block);
        $rowBlock->close();

        return $rowBlock;
    }

}
