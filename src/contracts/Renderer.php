<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Element\Element;


/**
 *
 */
interface Renderer {
    public function __construct($options = []);

    public function popContext(Block $block, $options = []);

    public function pushContext($options = []);

    public function render(Element $element, $options = []);

    public function setOptions($options = []);

    public function start($options = []);

}
