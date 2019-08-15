<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Element\Element;


/**
 * Interface for a page renderer.
 * @codeCoverageIgnore
 */
interface Renderer {
    public function __construct($options = []);

    public function popContext(Block $block, $options = []);

    public function pushContext($options = []);

    public function render(Element $element, $options = []);

    public function setOptions($options = []);

    public function setVisual($settings);

    public function start($options = []);

}
