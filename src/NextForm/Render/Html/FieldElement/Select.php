<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html\FieldElement;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementBase;

abstract class Select  {
    protected $access;
    protected $binding;
    protected $dataProperty;
    protected $element;
    protected $engine;
    protected $field;
    protected $multiple;
    protected $value;

    public function __construct(
        FieldElementBase $field,
        RenderInterface $engine,
        FieldBinding $binding
    ) {
        $this->field = $field;
        $this->engine = $engine;
        $this->binding = $binding;
    }

    /**
     * Get common attributes for the input element.
     *
     * @param Labels $labels
     * @return Attributes
     */
    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = new Attributes();
        $attrs->set('id', $this->binding->getId());
        $attrs->set('name', $this->binding->getFormName());

        if (!$this->element->getEnabled()) {
            $attrs->setFlag('disabled');
        }
        $attrs->setIfNotNull(
            '*data-nf-sidecar',
            $this->binding->getDataProperty()->getPopulation()->sidecar
        );

        if (($rows = $this->dataProperty->getPresentation()->getRows()) !== null) {
            $attrs->set('size', $rows);
        }

        return $attrs;
    }

    /**
     * Generate the input element and any wrapping/supporting code.
     */
    abstract protected function inputGroup(
        Labels $labels,
        Attributes $attrs
    ) : Block;

    /**
     * Render the element.
     *
     * @param array $options
     * @return Block
     */
    public function render($options = []) : Block
    {
        $this->access = $this->engine->getAccess($options);
        $this->dataProperty = $this->binding->getDataProperty();
        $this->multiple = $this->dataProperty->getValidation()->get('multiple');
        $this->element = $this->binding->getElement();

        // If there's no value set, see if there's a default
        $this->value = $this->binding->getValue();
        if ($this->value === null) {
            $this->value = $this->element->getDefault();
        }
        if (!is_array($this->value)) {
            $this->value = [$this->value];
        }
        if ($this->access === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            $block = $this->engine->elementHidden(
                $this->binding, $this->binding->getValue()
            );
            return $block;
        }

        // Push and update the show context
        $show = $this->element->getShow();
        if ($show !== '') {
            $this->engine->pushContext();
            $this->engine->setShow($show, 'select');
        }

        // Generate any field grouping.
        $block = $this->renderContainer();

        // Get any labels associated with this element
        $labels = $this->binding->getLabels(true);

        // Get attributes for the input element
        $attrs = $this->inputAttributes($labels);

        $headAttrs = new Attributes();
        if ($this->access !== 'view') {
            $headAttrs->set('!for', $attrs->get('name'));
        }

        // Write the heading
        $block->body .= $this->engine->writeLabel(
            'headingAttributes', $labels->heading, 'label',
            $headAttrs, ['break' => true]
        );

        $select = $this->inputGroup($labels, $attrs);


        $block->merge($select);
        $block->close();
        $block->merge($this->engine->epilog());

        // Restore show context and return.
        if ($show !== '') {
            $this->engine->popContext();
        }

        return $block;
    }

    /**
     * Generate any field grouping.
     *
     * @return Block
     */
    protected function renderContainer() : Block
    {
        // We can see or change the data. Create a form group.
        $block = $this->engine->writeElement(
            'div', [
                'attributes' => $this->engine->groupAttributes(
                    $this->binding
                ),
                'show' => 'formGroupAttributes'
            ]
        );
        return $block;
    }

    protected function renderOption($option, $value)
    {
        $block = new Block();
        $attrs = new Attributes();
        $attrs->set('value', $option->getValue());
        $attrs->setIfNotNull('data-nf-name', $option->getName());
        $attrs->setIfNotEmpty('*data-nf-group', $option->getGroups());
        $attrs->setIfNotNull('*data-nf-sidecar', $option->getSidecar());
        if (in_array($attrs->get('value'), $value)) {
            $attrs->setFlag('selected');
        }
        $block->body .= $this->engine->writeTag('option', $attrs, $option->getLabel()) . "\n";
        return $block;
    }

    protected function renderOptions($list, $value) {
        $block = new Block();
        foreach ($list as $option) {
            if ($option->isNested()) {
                $attrs = new Attributes();
                $attrs->set('label', $option->getLabel());
                $attrs->setIfNotNull('data-nf-name', $option->getName());
                $attrs->setIfNotEmpty('*data-nf-group', $option->getGroups());
                $attrs->setIfNotNull('*data-nf-sidecar', $option->getSidecar());
                $block->body .= $this->engine->writeTag('optgroup', $attrs) . "\n";
                $block->merge($this->renderOptions($option->getList(), $value));
                $block->body .= '</optgroup>' . "\n";
            } else {
                $block->merge($this->renderOption($option, $value));
            }
        }
        return $block;
    }

    protected function renderView(Attributes $attrs)
    {
        $list = $this->binding->getFlatList(true);
        // render as hidden with text
        $attrs->set('type', 'hidden');

        $baseId = $this->binding->getId();
        $value = $this->binding->getValue();
        $input = new Block();
        if ($this->multiple) {
            // step through each possible value, output matches
            if (!is_array($value)) {
                $value = [$value];
            }
            $optId = 0;
            foreach ($list as $option) {
                $slot = array_search($option->getValue(), $value);
                if ($slot !== false) {
                    $id = $baseId . '_opt' . $optId;
                    $attrs->set('id', $id);
                    $attrs->set('value', $value[$slot]);
                    $input->body .= $this->engine->writeTag('input', $attrs) . "\n";
                    $input->body .= $this->engine->writeTag('span', [], $option->getLabel())
                        . "<br/>\n";
                    ++$optId;
                }
            }
        } else {
            $attrs->set('id', $baseId);
            $attrs->set('value', $value);
            $input->body .= $this->engine->writeTag('input', $attrs) . "\n";
            foreach ($list as $option) {
                if ($value == $option->getValue()) {
                    $input->body .= $this->engine->writeTag('span')
                        . $option->getLabel() . '</span>'
                        . "\n";
                }
            }
        }
        return $input;
    }

}
