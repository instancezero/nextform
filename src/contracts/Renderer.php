<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Element\Element;
use Illuminate\Contracts\Translation\Translator as Translator;


/**
 *
 */
interface Renderer {
    public function render(Element $element, Translator $translate, $readOnly);
}
