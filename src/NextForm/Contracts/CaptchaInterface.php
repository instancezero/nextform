<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Block;

/**
 * Support for pluggable captcha fields
 */
interface CaptchaInterface
{
    /**
     * Generate whatever is required to implement a captcha for a form.
     *
     * @param RenderInterface $engine The rendering engine for this request.
     * @param Binding $binding Binding to the form element that is requesting a
     *  Captcha.
     * @param array $options
     * @return Block
     */
    public function render(
        RenderInterface $engine,
        Binding $binding,
        $options = []
    ) : Block;

    /**
     * Break caching for render processes that are the same for multiple
     * forms on a page.
     */
    public function reset();
}
