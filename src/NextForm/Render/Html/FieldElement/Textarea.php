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

abstract class Textarea  {
    protected $access;
    protected $binding;
    protected $element;
    protected $engine;
    protected $field;

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
        $attrs->set('id', $this->binding->getId());
        $attrs->set('name', $this->binding->getFormName());
        $attrs->setFlag(
            'readonly',
            $this->element->getReadonly() || $this->access == 'view'
        );
        if (!$this->element->getEnabled()) {
            $attrs->setFlag('disabled');
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
        Attributes $attrs,
        $value
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
        $data = $this->binding->getDataProperty();
        $presentation = $data->getPresentation();
        $value = $this->binding->getValue();
        if ($this->access === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            $block = $this->engine->elementHidden(
                $this->binding, $value
            );
            return $block;
        }

        $this->element = $this->binding->getElement();

        // Push and update the show context
        $show = $this->element->getShow();
        if ($show !== '') {
            $this->engine->pushContext();
            $this->engine->setShow($show, 'textarea');
        }

        // Get any labels associated with this element
        $labels = $this->binding->getLabels(true);

        // Get attributes for the input element
        $attrs = $this->inputAttributes($labels);

        if ($this->access === 'write') {
            // Write access: Add in any validation
            $attrs->addValidation('textarea', $data->getValidation());
        }

        // We can see or change the data
        if ($value === null) {
            $value = '';
        }

        // Generate any field grouping.
        $block = $this->renderContainer();

        // Write the heading
        $block->body .= $this->engine->writeLabel(
            'headingAttributes', $labels->heading, 'label',
            new Attributes('!for', $this->binding->getId()), ['break' => true]
        );

        // Placeholder label and any size specifiers
        $attrs->setIfNotNull('placeholder', $labels->inner);
        $attrs->setIfNotNull('cols', $presentation->getCols());
        $attrs->setIfNotNull('rows', $presentation->getRows());

        // Generate the actual input element, with labels if provided.
        $input = $this->inputGroup($labels, $attrs, $value);

        $block->merge($input);
        $block->close();
        //$block->merge($this->engine->epilog());

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
                'attributes' => $this->engine->groupAttributes($this->binding),
                'show' => 'formGroupAttributes'
            ]
        );
        return $block;
    }

}
