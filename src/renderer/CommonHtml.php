<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\ButtonElement;
use Abivia\NextForm\Element\CellElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Element\HtmlElement;
use Abivia\NextForm\Element\SectionElement;
use Abivia\NextForm\Element\StaticElement;

/**
 * Methods used by most HTML render classes
 */
abstract class CommonHtml extends Html implements Renderer {

    /**
     * Maps element types to render methods.
     * @var array
     */
    static $renderMethodCache = [];

    public function __construct($options = []) {
        parent::__construct($options);
        self::$showDefaultScope = 'form';
        $this -> initialize();
    }

    /**
     * Render a data list, if there is one.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs Parent attributes. Passed by reference.
     * @param type $element The element we're rendering.
     * @param type $type The element type
     * @param type $options Options, specifically access rights.
     * @return \Abivia\NextForm\Renderer\Block
     */
    protected function dataList(Attributes $attrs, $element, $type, $options) {
        $block = new Block;
        // Check for a data list, if there is write access.
        $list = $options['access'] === 'write' && Attributes::inputHas($type, 'list')
            ? $element -> getList(true) : [];
        if (!empty($list)) {
            $attrs -> set('list', $attrs -> get('id') . '-list');
            $block -> post = '<datalist id="' . $attrs -> get('list') . "\">\n";
            $optAttrs = new Attributes();
            foreach ($list as $option) {
                $optAttrs -> set('value', $option -> getValue());
                $sidecar = $option -> sidecar;
                if ($sidecar !== null) {
                    $optAttrs -> set('*data-sidecar', $sidecar);
                }
                $block -> post .= $this -> writeTag('option', $optAttrs) . "\n";
            }
            $block -> post .= "</datalist>\n";
        }
        return $block;
    }

    protected function initialize() {
        // Reset the context
        $this -> context = [
            'inCell' => false
        ];
        // Initialize custom settings
        $this -> setShow('layout:vertical');
    }

    protected function renderFieldElement(FieldElement $element, $options = []) {
        /*
            'image'
        */
        $result = new Block;
        $presentation = $element -> getDataProperty() -> getPresentation();
        $type = $presentation -> getType();
        $options['confirm'] = false;
        $repeater = true;
        while ($repeater) {
            switch ($type) {
                case 'checkbox':
                case 'radio':
                    $block = $this -> renderFieldCheckbox($element, $options);
                    break;
                default:
                    $method = 'renderField' . \ucfirst($type);
                    if (method_exists($this, $method)) {
                        $block = $this -> $method($element, $options);
                    } else {
                        $block = $this -> renderFieldCommon($element, $options);
                    }
                    break;
            }
            // Check to see if we need to generate a confirm field, and
            // haven't already done so...
            if (
                in_array($type, self::$inputConfirmable)
                && $presentation -> getConfirm()
                && $options['access'] === 'write' && !$options['confirm']
            ) {
                $options['confirm'] = true;
            } else {
                $repeater = false;
            }
            $result -> merge($block);
        }
        return $result;
    }

    abstract protected function renderFieldCommon(FieldElement $element, $options = []);

    /**
     * Render Field elements for checkbox and radio types.
     * @param FieldElement $element
     * @param array $options
     * @return Block
     */
    abstract protected function renderFieldCheckbox(FieldElement $element, $options = []);

    public function setOptions($options = []) {

    }

}

