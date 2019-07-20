<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * A skeletal renderer that generates a very basic form.
 */
class Simple implements Renderer {
    static $renderMethodCache = [];

    public function __construct($options = []) {

    }

    protected function getRenderMethod(Element $element) {
        $classPath = get_class($element);
        if (!isset(self::$renderMethodCache[$classPath])) {
            $classParts = explode('\\', $classPath);
            self::$renderMethodCache[$classPath] = 'render' . array_pop($classParts);
        }
        return self::$renderMethodCache[$classPath];
    }

    public function render(Element $element, Translator $translate, $options = []) {
        $method = $this -> getRenderMethod($element);
        if (method_exists($this, $method)) {
            $result = $this -> $method($element, $translate, $options);
        } else {
            $result = new Block();
            $result -> body = 'Seems we don\'t have a ' . $method . ' method yet!' . "\n";
        }
        return $result;
    }

    protected function renderFieldElement(Element $element, Translator $translate, $options = []) {
        $result = new Block();
        $result -> body = '<input type="coming-soon" />' . "\n";
        return $result;
    }

    protected function renderSectionElement(Element $element, Translator $translate, $options = []) {
        $result = new Block();
        $result -> body = '<div>' . "\n";
        $result -> post = '</div>' . "\n";
        return $result;
    }

    public function renderCellElement(Element $element, Translator $translate, $options = []) {
        $labels = $element -> getLabels();
    }

    public function setOptions($options = []) {

    }

    public function start($options = []) {
        $pageData = new Block();
        $pageData -> body = '<form method="post">' . "\n";
        $pageData -> post = '</form>' . "\n";
        return $pageData;
    }

}

