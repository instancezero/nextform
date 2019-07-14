<?php

namespace Abivia\NextForm\Contracts;

use \Abivia\NextForm\Form\Element;


/**
 *
 */
interface Renderer {
    public function render(Element $element);
}
