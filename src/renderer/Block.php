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

    /**
     * List of script files to link
     * @var array
     */
    public $scripts = [];

    /**
     * List of style files to link
     * @var array
     */
    public $styles = [];

    public function close() {
        $this -> body .= $this -> post;
        $this -> post = '';
    }

    public function merge(Block $block) {
        foreach ($block as $prop => $value) {
            switch ($prop) {
                case 'post':
                    // Post code prefixes the existing data
                    $this -> $prop = $value . $this -> $prop;
                    break;

                case 'scripts':
                case 'styles':
                    // Scripts and styles are unique
                    $this -> $prop = array_merge($this -> $prop, $value);
                    break;

                default:
                    // Everything else gets appended.
                    $this -> $prop .= $value;
                    break;
            }
        }
    }

}
