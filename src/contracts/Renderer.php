<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Element\Element;


/**
 *
 */
interface Renderer {
    public function render(Element $element);
}
