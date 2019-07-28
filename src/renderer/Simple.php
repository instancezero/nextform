<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\FieldElement;

/**
 * A skeletal renderer that generates a very basic form.
 */
class Simple implements Renderer {

    protected $context = [
        ['inCell' => false]
    ];
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

    public function popContext(Block $block, $options = []) {
        if (count($this -> context) > 1) {
            array_shift($this -> context);
        }
        return $block -> close();
    }

    public function pushContext($options = []) {
        array_unshift($this -> context, $this -> context[0]);
    }

    public function render(Element $element, $options = []) {
        $method = $this -> getRenderMethod($element);
        if (method_exists($this, $method)) {
            if (!isset($options['access'])) {
                $options['access'] = 'write';
            }
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
        $type = $presentation -> getType();
        $block = new Block();
        $labels = $element -> getLabels(true);
        if ($labels -> heading) {
            $block -> body = '<label for="' . $element -> getId() . '">'
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
        $attrs['id'] = 'id="' . $element -> getId() . '"';
        if ($options['access'] == 'view') {
            $attrs['readonly'] = 'readonly';
            if ($type == 'radio') {
                // Render a read-only radio as text
                $type = 'text';
            }
        }
        $attrs['name'] = 'name="' . $element -> getFormName() . '"';
        if ($options['access'] != 'read') {
            // We can see or change the data
            if ($labels -> placeholder !== null) {
                $attrs['placeholder'] = 'placeholder="' . htmlentities($labels -> placeholder) . '"';
            }
            $attrs['type'] = 'type="' . $type . '"';
            if ($labels -> before !== null) {
                $block -> body .= '<span>'. $labels -> before . '</span>';
            }
            if ($type == 'radio') {
                $block = $this -> renderFieldRadio($block, $element, $options);
            } elseif ($type == 'select') {
                $block = $this -> renderFieldSelect($block, $element, $options);
            } else {
                $block -> body .= '<input ' . implode(' ', $attrs) . "/>";
            }
            if ($labels -> after !== null) {
                $block -> body .= '<span>'. $labels -> after . '</span>';
            }
            $block -> body .= ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        } elseif ($options['access'] == 'read') {
            // No write/view permissions, the field is hidden
            $attrs[] = 'type="hidden"';
            $block -> body .= '<input ' . implode(' ', $attrs) . "/>\n";
        }
        return $block;
    }

    protected function renderFieldRadio($block, $element, $options = []) {
        $baseId = $element -> getId();
        $attrs = [];
        $attrs['name'] = 'name="' . $element -> getFormName() . '"';
        $attrs['type'] = 'type="radio"';
        $list = $element -> getList();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        foreach ($list as $optId => $radio) {
            $id = $baseId . '-opt' . $optId;
            $attrs['id'] = 'id="' . $id . '"';
            $attrs['value'] = 'value="' . htmlentities($radio -> value) . '"';
            if ($radio -> value === $select) {
                $attrs['checked'] = 'checked';
            } else {
                unset($attrs['checked']);
            }
            if (isset($radio -> sidecar)) {
                $attrs['data-sidecar'] = 'data-sidecar="' . htmlspecialchars(json_encode($radio -> sidecar)) . '"';
            }
            $block -> body .= "<div>\n  <input " . implode(' ', $attrs) . "/>\n"
                . '  <label for="' . $id . '">' . $radio -> label . "</label>\n"
                . "</div>\n";
        }
        return $block;
    }

    protected function renderFieldSelect($block, $element, $options = []) {
        //
        // Note: this is just a copy of Radio, needs rework
        //
        $baseId = $element -> getId();
        $attrs = [];
        $attrs['name'] = 'name="' . $element -> getFormName() . '"';
        $attrs['type'] = 'type="radio"';
        $list = $element -> getList();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        foreach ($list as $optId => $radio) {
            $id = $baseId . '-opt' . $optId;
            $attrs['id'] = 'id="' . $id . '"';
            $attrs['value'] = 'value="' . htmlentities($radio -> value) . '"';
            if ($radio -> value === $select) {
                $attrs['checked'] = 'checked';
            } else {
                unset($attrs['checked']);
            }
            if (isset($radio -> sidecar)) {
                $attrs['data-sidecar'] = 'data-sidecar="' . htmlspecialchars(json_encode($radio -> sidecar)) . '"';
            }
            $block -> body .= "<div>\n  <input " . implode(' ', $attrs) . "/>\n"
                . '  <label for="' . $id . '">' . $radio -> label . "</label>\n"
                . "</div>\n";
        }
        return $block;
    }

    protected function renderSectionElement(Element $element, $options = []) {
        $labels = $element -> getLabels(true);
        $block = new Block();
        $block -> body = '<fieldset>' . "\n";
        if ($labels -> heading !== null) {
            $block -> body .= '<legend>' . $labels -> heading . '</legend>' . "\n";
        }
        $block -> post = '</fieldset>' . "\n";
        return $block;
    }

    public function renderCellElement(Element $element, $options = []) {
        $labels = $element -> getLabels();
        $block = new Block();
        $block -> body = '<div>' . "\n";
        $block -> post = '</div>' . "\n";
        $this -> context[0]['inCell'] = true;
        return $block;
    }

    public function setOptions($options = []) {

    }

    public function start($options = []) {
        if (isset($options['method'])) {
            $attr = ['method' => 'method="' . htmlspecialchars($options['method']) . '"'];
        } else {
            $attr = ['method' => 'method="post"'];
        }
        if (isset($options['action'])) {
            $attr['action'] = 'action="' . htmlspecialchars($options['action']) . '"';
        }
        if (isset($options['id'])) {
            $attr['id'] = 'id="' . htmlspecialchars($options['id']) . '"';
        }
        if (isset($options['name'])) {
            $attr['name'] = 'name="' . htmlspecialchars($options['name']) . '"';
        }
        $pageData = new Block();
        $pageData -> body = '<form ' . implode(' ', $attr) . '>' . "\n";
        $pageData -> post = '</form>' . "\n";
        return $pageData;
    }

}

