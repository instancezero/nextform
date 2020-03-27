<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Render\Block;

class CaptchaElementRender extends AbstractElementRender
{

    /**
     * Write a Captcha element.
     * @param type $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {

        // No write permission, the field is unrenderable.
        if ($this->engine->getAccess($options) !== 'write') {
            return new Block();
        }

        // Pass the render request to the Captcha class via the engine
        $block = $this->engine->captcha($this->binding);

        return $block;
    }

}
