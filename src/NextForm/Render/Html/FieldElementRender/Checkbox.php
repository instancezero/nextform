<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html\FieldElementRender;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Html\FieldElementRenderBase;

abstract class Checkbox extends AbstractFieldElement
{

    /**
     * Current access setting.
     * @var string
     */
    protected $access;

    /**
     * Get common attributes for the input element.
     *
     * @param Labels $labels
     * @return Attributes
     */
    protected function inputAttributes(Labels $labels) : Attributes
    {
        $attrs = parent::inputAttributes($labels);
        $data = $this->binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();
        $attrs->set('type', $type);
        $attrs->set('name', $this->binding->getNameOnForm());
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);
        if ($this->access == 'view') {
            $attrs->setFlag('readonly');
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

    abstract protected function multiple();

    /**
     * Render the element.
     *
     * @param array $options
     * @return Block
     */
    public function render($options = []) : Block
    {
        //  appearance = default|button|toggle (can't be multiple)|no-label
        //  layout = inline|vertical
        //  form.layout = horizontal|vertical|inline

        $this->access = $this->engine->getAccess($options);
        if ($this->access === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            $block = $this->engine->elementHiddenList($this->binding);
            return $block;
        }

        $data = $this->binding->getDataProperty();
        $this->inputType = $data->getPresentation()->getType();
        $this->element = $this->binding->getElement();

        // Push and update the show context
        $show = $this->element->getShow();
        if ($show !== '') {
            $this->engine->pushContext();
            $this->engine->setShow($show, 'check');
        }

        $labels = $this->binding->getLabels(true);
        $attrs = $this->inputAttributes($labels);

        $block = $this->inputGroup($labels, $attrs);

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

    abstract protected function single();

}
