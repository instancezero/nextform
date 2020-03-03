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

abstract class AbstractFieldElement
{
    protected $access;

    /**
     * The binding we're rendering.
     *
     * @var FieldBinding
     */
    protected $binding;

    protected $element;

    /**
     * The rendering engine.
     *
     * @var RenderInterface
     */
    protected $engine;

    /**
     *
     * @var FieldElementRenderBase
     */
    protected $field;

    public function __construct(
        FieldElementRenderBase $field,
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
        $valid = $this->binding->getValid();
        if ($valid !== null) {
            $key = $valid ? 'valid' : 'invalid';
            $attrs->itemAppend('class', $this->engine->showGet('form', $key));
        }

        return $attrs;
    }

}
