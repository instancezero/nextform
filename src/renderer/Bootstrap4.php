<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Renderer for Bootstrap4
 */
class Bootstrap4 implements Renderer {

    public function __construct($options = []) {
        $this -> setOptions($options);
    }

    public function render(Element $element, $options = []) {

    }

    public function setOptions($options = []) {

    }

    public function start($options = []) {

    }

}

