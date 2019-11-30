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
     * Use a renderer to turn this element into part of the form.
     * @param RenderInterface $renderer Any Render object.
     * @param AccessInterface $access Any access control object
     * @param Translator $translator Any translation object.
     * @return Block
     */
    public function generate(
        RenderInterface $renderer,
        AccessInterface $access
    ) : Block {
        $options = false; // $access->hasAccess(...)
        $options = ['access' => 'write'];
        $containerData = $renderer->render($this, $options);
        $containerData->close();
        return $containerData;
    }

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
    public function translate(Translator $translator = null) : Binding
    {
        parent::translate($translator);

        return $this;
    }

}