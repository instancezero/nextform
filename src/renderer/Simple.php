<?php
namespace Abivia\NextForm\Render;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;

/**
 * A skeletal renderer that generates a very basic form.
 */
class Simple implements Renderer {

    public function __construct($options = []) {

    }

    public function render(Element $element, Translator $translate, $options = []) {
        $method = 'render' . get_class($element);
        if (method_exists($this, $method)) {
            $result = $this -> $method($element, $translate, $options);
        } else {
            $result = new Block();
            $result -> body = 'Seems we don\'t have a ' . $method . ' method yet!';
        }
        return $result;
    }

    public function renderCellElement(Element $element, Translator $translate, $options = []) {
        $labels = $element -> getLabels();
    }

    public function setOptions($options = []) {

    }

    public function start($options = []) {
        $pageData = new Block();
        $pageData -> body = '<form method="post">';
        $pageData -> post = '</form>';
        return $pageData;
    }

}

