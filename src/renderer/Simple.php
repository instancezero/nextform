<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\FieldElement;

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

    public function render(Element $element, $options = []) {
        $method = $this -> getRenderMethod($element);
        if (method_exists($this, $method)) {
            $result = $this -> $method($element, $options);
        } else {
            $result = new Block();
            $result -> body = 'Seems we don\'t have a ' . $method . ' method yet!' . "\n";
        }
        return $result;
    }

    protected function renderFieldElement(FieldElement $element, $options = []) {
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $block = new Block();
        $labels = $element -> getLabels(true);
        if ($labels -> heading) {
            $block -> body = '<label for="' . $element -> getFormName() . '">'
                . $labels -> heading . '</label>' . "\n";
        }
        /*
            'button', 'checkbox', 'color', 'date', 'datetime-local',
            'email', 'file', 'hidden', 'image', 'month', 'number',
            'password', 'radio', 'range', 'reset', 'search',
            'submit', 'tel', 'text', 'textarea', 'time', 'url', 'week',
            // Our non w3c types...
            'select',
        */
        $attrs = [];
        if ($options['access'] == 'view') {
            $attrs['readonly'] = 'readonly';
        }
        $attrs['name'] = 'name="' . $element -> getFormName() . '"';
        if ($options['access'] != 'read') {
            $type = $presentation -> getType();
            $attrs['type'] = 'type="' . $type . '"';
            if ($type == 'radio') {
                $block = $this -> renderFieldRadio($block, $element, $presentation, $options);
            } elseif ($type == 'select') {
                $block = $this -> renderSelect($block, $element, $presentation, $options);
            } else {
                $block -> body .= '<input ' . implode(' ', $attrs) . "/>\n";
            }
        } elseif ($options['access'] == 'read') {
            $attrs[] = 'type="hidden"';
            $block -> body .= '<input ' . implode(' ', $attrs) . "/>\n";
        }
        return $block;
    }

    protected function renderFieldRadio($block, $element, $presentation, $options = []) {
        return $block;
    }

    protected function renderFieldSelect($block, $element, $presentation, $options = []) {
        return $block;
    }

    protected function renderSectionElement(Element $element, $options = []) {
        $block = new Block();
        $block -> body = '<div>' . "\n";
        $block -> post = '</div>' . "\n";
        return $block;
    }

    public function renderCellElement(Element $element, $options = []) {
        $labels = $element -> getLabels();
    }

    public function setOptions($options = []) {

    }

    public function start($options = []) {
        $attr = ['method' => 'method="post"'];
        $attr['action'] = 'action="' . $options['action'] . '"';
        $attr['id'] = 'id="' . $options['id'] . '"';
        $attr['name'] = 'name="' . $options['name'] . '"';
        $pageData = new Block();
        $pageData -> body = '<form ' . implode(' ', $attr) . '>' . "\n";
        $pageData -> post = '</form>' . "\n";
        return $pageData;
    }

}

