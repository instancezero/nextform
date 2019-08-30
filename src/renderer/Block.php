<?php

Namespace Abivia\NextForm\Renderer;

/**
 *
 */
class Block {
    /**
     * Executable code associated with an Element.
     * @var string
     */
    public $code = '';

    /**
     * Page header data associated with an Element.
     * @var string
     */
    public $head = '';

    /**
     * Markup associated with an Element.
     * @var string
     */
    public $body = '';

    /**
     * On close completed event handler (pair with onCloseInit if needed).
     * @var callable
     */
    public $onCloseDone;

    /**
     * Markup that follows any nested elements.
     * @var string
     */
    public $post = '';

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

    public function close() : self {
        $this -> body .= $this -> post;
        $this -> post = '';
        if (is_callable($this -> onCloseDone)) {
            call_user_func($this -> onCloseDone, $this);
        }
        return $this;
    }

    static public function fromString($body = '', $post = '') : Block {
        $that = new Block;
        $that -> body = $body;
        $that -> post = $post;
        return $that;
    }

    public function merge(Block $block) : self {
        foreach ($block as $prop => $value) {
            switch ($prop) {
                case 'onCloseDone':
                    // Handlers are dropped;
                    break;

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
        return $this;
    }

}
