<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;

abstract class ButtonElementRenderBase
{
    protected $access;
    protected $binding;
    protected $element;
    protected $engine;
    protected $inputType;

    public function __construct(RenderInterface $engine, Binding $binding)
    {
        $this->engine = $engine;
        $this->binding = $binding;
    }

    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = new Attributes();
        $attrs->set('id', $this->binding->getId());
        $attrs->set('name', $this->binding->getFormName());
        $attrs->set('type', $this->inputType);
        $attrs->setFlag(
            'readonly',
            $this->element->getReadonly() || $this->access == 'view'
        );
        if ($this->access === 'view' || !$this->element->getEnabled()) {
            $attrs->setFlag('disabled');
        }
        $attrs->setIfNotNull('value', $labels->inner);
        $attrs->set('type', $this->element->getFunction());

        return $attrs;
    }

    abstract protected function inputGroup(
        Labels $labels,
        Attributes $attrs
    ) : Block;

    /**
     * Write a button element.
     * @param type $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {
        $this->access = $this->engine->getAccess($options);

        // Get any labels associated with this element
        $labels = $this->binding->getLabels(true);

        // No write/view permissions, the field is hidden.
        if ($this->access === 'hide') {
            $block = $this->engine->elementHidden($this->binding, $labels->inner);
            return $block;
        }

        $this->element = $this->binding->getElement();

        // Push and update the show context
        $show = $this->element->getShow();
        if ($show !== '') {
            $this->engine->pushContext();
            $this->engine->setShow($show, 'button');
        }

        // Generate any field grouping.
        $block = $this->renderContainer();

        // Get attributes for the input element
        $attrs = $this->inputAttributes($labels);

        // Write the header.
        $block->body .= $this->engine->writeLabel(
            'headingAttributes',
            $labels->heading,
            'label',
            new Attributes('!for', $this->binding->getId()),
            ['break' => true]
        );

        // Generate the actual input element, with labels if provided.
        $input = $this->inputGroup($labels, $attrs);

        $block->merge($input);
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
    protected function renderContainer()
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
