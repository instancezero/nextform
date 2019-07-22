<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Element\Element;
use Illuminate\Contracts\Translation\Translator as Translator;


/**
 *
 */
interface Renderer {
    public function __construct($options = []);

    public function render(Element $element, $options = []);

    public function setOptions($options = []);

    public function start($options = []);

}
