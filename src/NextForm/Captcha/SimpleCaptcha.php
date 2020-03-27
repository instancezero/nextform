<?php

namespace Abivia\NextForm\Captcha;

use Abivia\NextForm\Contracts\CaptchaInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Render\Block;
/**
 * SimpleCaptcha
 */
class SimpleCaptcha implements CaptchaInterface
{
    private $answer;

    public function render(
        RenderInterface $engine,
        Binding $binding,
        $options = []
    ) : Block {
        if ($this->answer === null) {
            $this->answer = random_int(1, 10);
        }

        // Generate an input element
        $captcha = FieldBinding::build('text');

        // Create a copy of the source labels
        $captchaLabels = clone $binding->getLabels();

        // Customize and render
        $captchaLabels->set('before', "Enter $this->answer here");
        $captcha->setLabels($captchaLabels);
        $block = $engine->render($captcha);
        $block->data[self::class] = ['answer' => $this->answer];

        return $block;
    }

    public function reset()
    {
        $this->answer = null;
    }
}
