<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Renderer\Block;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Class for binding elements with a preset value
 */
class SimpleBinding Extends Binding
{

    /**
     * Use a renderer to turn this element into part of the form.
     * @param RendererInterface $renderer Any Renderer object.
     * @param AccessInterface $access Any access control object
     * @param Translator $translate Any translation object.
     * @return Block
     */
    public function generate(
        RendererInterface $renderer,
        AccessInterface $access,
        Translator $translate
    ) : Block {
        $this->translate($translate);
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

}