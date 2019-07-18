<?php

Namespace Abivia\NextForm\Render;

/**
 *
 */
class Block {
    /**
     * Executable code associated with an Element.
     * @var string
     */
    public $code;

    /**
     * Page header data associated with an Element.
     * @var string
     */
    public $head;

    /**
     * Markup associated with an Element.
     * @var string
     */
    public $body;

    /**
     * Markup that follows any nested elements.
     * @var string
     */
    public $post;

}
