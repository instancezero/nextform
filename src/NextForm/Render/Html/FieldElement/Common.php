<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\Html\FieldElement;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Html\FieldElementBase;

abstract class Common  {
    protected $access;
    protected $binding;
    protected $confirmSuffix;
    protected $element;
    protected $engine;
    protected $field;
    protected $inputType;

    public function __construct(
        FieldElementBase $field,
        RendererInterface $engine,
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
        $attrs->set('id', $this->binding->getId() . $this->confirmSuffix);
        $attrs->set('name', $this->binding->getFormName() . $this->confirmSuffix);
        $attrs->set('type', $this->inputType);
        $attrs->setFlag(
            'readonly',
            $this->element->getReadonly() || $this->access == 'view'
        );
        if (!$this->element->getEnabled()) {
            $attrs->setFlag('disabled');
        }
        $value = $this->binding->getValue();
        if ($value === null) {
            $attrs->setIfNotNull('value', $this->element->getDefault());
        } else {
            $attrs->set('value', $value);
        }
        $attrs->setIfNotNull(
            '*data-nf-sidecar',
            $this->binding->getDataProperty()->getPopulation()->sidecar
        );

        // If there's an inner label, use it as a placeholder
        $attrs->setIfNotNull('placeholder', $labels->inner);

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
        $confirm = $options['confirm'];
        $this->confirmSuffix = $confirm ? '_confirmation' : '';
        $data = $this->binding->getDataProperty();
        $this->inputType = $data->getPresentation()->getType();
        if ($this->access === 'hide' || $this->inputType === 'hidden') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            if ($confirm) {
                // No need to confirm a hidden element.
                $block = new Block();
            } else {
                $block = $this->engine->elementHidden(
                    $this->binding, $this->binding->getValue()
                );
            }
            return $block;
        }

        $this->element = $this->binding->getElement();

        // Push and update the show context
        $show = $this->element->getShow();
        if ($show !== '') {
            $this->engine->pushContext();
            $this->engine->setShow($show, $this->inputType);
        }

        // Convert view-only range elements to text
        if ($this->inputType === 'range' && $this->access === 'view') {
            $this->inputType = 'text';
        }

        // Generate any field grouping.
        $block = $this->renderContainer();

        // Get any labels associated with this element
        $labels = $this->binding->getLabels(true);

        // Get attributes for the input element
        $attrs = $this->inputAttributes($labels);

        // Write the heading
        // If we're generating a confirmation and there's a confirm heading, use that
        // otherwise just use the usual heading
        $fieldHeading = $confirm && $labels->confirm != '' ? $labels->confirm : $labels->heading;
        $block->body .= $this->engine->writeLabel(
            'headingAttributes', $fieldHeading, 'label',
            new Attributes('!for', $attrs->get('id')), ['break' => true]
        );

        // Render the data list if there is one
        $dataList = $this->field->dataList(
            $attrs, $this->binding, $this->inputType, $options
        );

        if ($this->access === 'write') {
            // Write access: Add in any validation
            $attrs->addValidation($this->inputType, $data->getValidation());
        }

        // Generate the actual input element, with labels if provided.
        $input = $this->inputGroup($labels, $attrs);

        $block->merge($input);
        $block->merge($dataList);
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
                    $this->binding,
                    ['id' => $this->binding->getId() . $this->confirmSuffix]
                ),
                'show' => 'formGroupAttributes'
            ]
        );
        return $block;
    }

}
