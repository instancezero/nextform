<?php
namespace Abivia\NextForm\Render;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;

/**
 * Renderer for Bootstrap4
 */
class Bootstrap4 implements Renderer {

    public function __construct($options = []) {
        $this -> setOptions($options);
    }

    public function render(Element $element, Translator $translate, $options = []) {

    }

    public function setOptions($options = []) {

    }

}

