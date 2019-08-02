<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\ButtonElement;
use Abivia\NextForm\Element\FieldElement;

/**
 * A skeletal renderer that generates a very basic form.
 */
class Simple implements Renderer {

    protected $context = [
        ['inCell' => false]
    ];
    static $inputAttributes = [
        '*' => [
            'autocomplete' => true, 'autofocus' => true,
            'dirname' => true, 'disabled' => true, 'form' => true,
            'name' => true, 'value' => true,
            // Globals
            'accesskey' => true, 'class' => true, 'contenteditable' => true,
            'dir' => true, 'draggable' => true, 'dropzone' => true,
            'hidden' => true, 'id' => true, 'lang' => true,
            'spellcheck' => true, 'style' => true, 'tabindex' => true, 'title' => true,
            'translate' => true,
        ],
        'button' => [],
        'checkbox' => ['checked' => true, 'required' => true, ],
        'color' => [],
        'date' => ['max' => true, 'min' => true, 'pattern' => true, 'step' => true, ],
        //'datetime' => ['required' => true, 'step' => true, ],
        'datetime-local' => ['required' => true, 'step' => true, ],
        'email' => [
            'list' => true, 'multiple' => true, 'pattern' => true,
            'placeholder' => true, 'required' => true, 'size' => true,
        ],
        'file' => [
            'accept' => true, 'multiple' => true, 'required' => true, 'value' => false
        ],
        'image' => [
            'formaction' => true, 'formenctype' => true, 'formmethod' => true,
            'formtarget' => true, 'height' => true, 'src' => true, 'width' => true,
        ],
        'month' => ['required' => true, 'step' => true, ],
        'number' => [
            'list' => true, 'max' => true, 'maxlength' => true, 'min' => true,
            'required' => true, 'step' => true,
        ],
        'password' => [
            'pattern' => true, 'placeholder' => true, 'required' => true, 'size' => true,
        ],
        'radio' => ['required' => true, ],
        'range' => ['step' => true, ],
        'reset' => [],
        'search' => [
            'list' => true, 'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'submit' => [
            'formaction' => true, 'formenctype' => true, 'formmethod' => true,
            'formtarget' => true,
        ],
        'tel' => [
            'list' => true, 'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'text' => [
            'list' => true, 'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'time' => ['step' => true, ],
        'url' => [
            'list' => true, 'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'week' => ['required' => true, 'step' => true, ],
    ];
    static $renderMethodCache = [];

    public function __construct($options = []) {
        // Build a non-sparse input attribute matrix
        if (isset(self::$inputAttributes['*'])) {
            // Merge all attributes into the common defaults
            $common = self::$inputAttributes['*'];
            unset(self::$inputAttributes['*']);
            foreach (self::$inputAttributes as $attrs) {
                foreach (array_keys($attrs) as $attrName) {
                    if (!isset($common[$attrName])) {
                        $common[$attrName] = false;
                    }
                }
            }
            ksort($common);
            // Overwrite the defaults for each input type
            foreach (self::$inputAttributes as &$attrs) {
                $attrs = array_merge($common, $attrs);
            }
        }
    }

    /**
     * Render a data list, if there is one.
     * @param string $attrs Parent attributes. Passed by reference.
     * @param type $element The element we're rendering.
     * @param type $type The element type
     * @param type $options Options, specifically access rights.
     * @return \Abivia\NextForm\Renderer\Block
     */
    protected function dataList(&$attrs, $element, $type, $options) {
        $block = new Block;
        // Check for a data list, if there is write access.
        $list = $options['access'] === 'write' && self::$inputAttributes[$type]['list']
            ? $element -> getList(true) : [];
        if (!empty($list)) {
            $attrs['list'] = $attrs['id'] . '-list';
            $block -> post = '<datalist id="' . $attrs['list'] . "\">\n";
            foreach ($list as $option) {
                $block -> post .= '  <option value="'
                    . htmlspecialchars($option -> getValue()) . '"';
                    $sidecar = $option -> getSidecar();
                    if ($sidecar !== null) {
                        $block -> post .=  $this -> writeAttribute(
                            'data-sidecar', json_encode($sidecar)
                        );
                    }
                $block -> post .= "/>\n";
            }
            $block -> post .= "</datalist>\n";
        }
        return $block;
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

    protected function renderButtonElement(ButtonElement $element, $options = []) {
        $attrs = [];
        $block = new Block();
        $labels = $element -> getLabels(true);
        if ($labels -> heading) {
            $block -> body = '<label for="' . $element -> getId() . '">'
                . $labels -> heading . '</label>' . "\n";
        }
        $attrs['id'] = $element -> getId();
        if ($options['access'] == 'view') {
            $attrs['=disabled'] = 'disabled';
        }
        $attrs['name'] = $element -> getFormName();
        $attrs['value'] = $labels -> inner;
        if ($options['access'] === 'read') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $attrs['type'] = 'hidden';
            $block -> body .= '<input' . $this -> writeAttributes($attrs) . "/>\n";
        } else {
            //
            // We can see or change the data
            //
            $attrs['type'] = $element -> getButtonType();
            if ($labels -> before !== null) {
                $block -> body .= '<span>' . $labels -> before . '</span>';
            }
            $block -> body .= '<input' . $this -> writeAttributes($attrs) . "/>";
            if ($labels -> after !== null) {
                $block -> body .= '<span>'. $labels -> after . '</span>';
            }
            $block -> body .= ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        }
        return $block;
    }

    protected function renderFieldElement(FieldElement $element, $options = []) {
        $attrs = [];
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
            'checkbox', 'color', 'date', 'datetime', 'datetime-local',
            'email', 'file', 'hidden', 'image', 'month', 'number',
            'password', 'range', 'reset', 'search',
            'submit', 'tel', 'textarea', 'time', 'url', 'week',
            // Our non w3c types...
            'select',
        */
        $attrs['id'] = $element -> getId();
        if ($options['access'] == 'view') {
            $attrs['=readonly'] = 'readonly';
            if ($type == 'radio') {
                // Render a read-only radio as text
                $type = 'text';
            }
        }
        $attrs['name'] = $element -> getFormName();
        if (($value = $element -> getValue()) !== null) {
            $attrs['value'] = $value;
        }
        if ($options['access'] === 'read') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $attrs['type'] = 'hidden';
            $block -> body .= '<input' . $this -> writeAttributes($attrs) . "/>\n";
        } else {
            //
            // We can see or change the data
            //
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            $attrs['type'] = $type;
            if ($labels -> before !== null) {
                $block -> body .= '<span>'. $labels -> before . '</span>';
            }
            if ($type === 'radio') {
                $block = $this -> renderFieldRadio($block, $element, $options);
            } elseif ($type === 'select') {
                $block = $this -> renderFieldSelect($block, $element, $options);
            } else {
                // Render the data list if there is one
                $block -> merge($this -> dataList($attrs, $element, $type, $options));
                $block -> body .= '<input' . $this -> writeAttributes($attrs) . "/>";
            }
            if ($labels -> after !== null) {
                $block -> body .= '<span>'. $labels -> after . '</span>';
            }
            $block -> body .= ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        }
        return $block;
    }

    protected function renderFieldRadio($block, $element, $options = []) {
        $baseId = $element -> getId();
        $attrs = [];
        $attrs['name'] = $element -> getFormName();
        $attrs['type'] = 'radio';
        $list = $element -> getList(true);
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        foreach ($list as $optId => $radio) {
            $id = $baseId . '-opt' . $optId;
            $attrs['id'] = $id;
            $value = $radio -> getValue();
            $attrs['value'] = htmlspecialchars($value);
            if ($value === $select) {
                $attrs['=checked'] = 'checked';
            } else {
                unset($attrs['=checked']);
            }
            $sidecar = $radio -> getSidecar();
            if ($sidecar !== null) {
                $attrs['data-sidecar'] = json_encode($sidecar);
            }
            $block -> body .= "<div>\n  <input" . $this -> writeAttributes($attrs) . "/>\n"
                . '  <label for="' . $id . '">' . $radio -> getLabel() . "</label>\n"
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
        $attrs['name'] = $element -> getFormName();
        $attrs['type'] = 'radio';
        $list = $element -> getList();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        foreach ($list as $optId => $radio) {
            $id = $baseId . '-opt' . $optId;
            $attrs['id'] = $id;
            $attrs['value'] = htmlspecialchars($radio -> value);
            if ($radio -> value === $select) {
                $attrs['=checked'] = 'checked';
            } else {
                unset($attrs['=checked']);
            }
            if (isset($radio -> sidecar)) {
                $attrs['data-sidecar'] = json_encode($radio -> sidecar);
            }
            $block -> body .= "<div>\n  <input" . $this -> writeAttributes($attrs) . "/>\n"
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
        $attrs = ['method' => isset($options['method']) ? $options['method'] : 'post'];
        if (isset($options['action'])) {
            $attrs['action'] = $options['action'];
        }
        if (isset($options['id'])) {
            $attrs['id'] = $options['id'];
        }
        if (isset($options['name'])) {
            $attrs['name'] = $options['name'];
        }
        $pageData = new Block();
        $pageData -> body = '<form' . $this -> writeAttributes($attrs) . '>' . "\n";
        $pageData -> post = '</form>' . "\n";
        return $pageData;
    }

    /**
     * Encode an attribute into escaped HTML
     * @param string $attrName The attribute name.
     * @param string $value The attribute value.
     * @return string
     */
    protected function writeAttribute($attrName, $value) {
        switch ($attrName[0]) {
            case '!': {
                // Attrribute that does not need to be escaped
                $html = ' ' . substr($attrName, 1) . '="' . $value . '"';
            }
            break;

            case '=': {
                // Stand-alone attribute with no value
                $html = ' ' . substr($attrName, 1);
            }
            break;

            default: {
                $html = ' ' . $attrName . '="' . htmlspecialchars($value) . '"';
            }
            break;

        }
        return $html;
    }

    /**
     * Encode attributes into escaped HTML
     * @param array $attrs
     * @return string
     */
    protected function writeAttributes($attrs) {
        $html = '';
        foreach ($attrs as $attrName => $value) {
            $html .= $this -> writeAttribute($attrName, $value);
        }
        return $html;
    }

}

