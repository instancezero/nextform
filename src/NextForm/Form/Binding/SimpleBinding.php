<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Render\Block;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Class for binding elements with a preset value
 */
class SimpleBinding Extends Binding
{

    /**
     * Get the current value for the bound element.
     * @return mixed
     */
    public function getValue() {
        return $this->element->getValue();
    }

    /**
     * Translate the texts in this binding.
     *
     * @param Translator $translator
     * @return $this
     */
    public function translate(?Translator $translator = null) : Binding
    {
        parent::translate($translator);

        return $this;
    }

}