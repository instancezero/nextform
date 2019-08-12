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
    static $highlightAttribute = [
        'id', 'name', 'type', 'class', 'style', 'value', 'min', 'max'
    ];
    static $inputAttributes = [
        '*' => [
            'autocomplete' => true, 'autofocus' => true,
            'dirname' => true, 'disabled' => true, 'form' => true,
            'name' => true, 'readonly' => true, 'type' => true, 'value' => true,
            // Globals
            'accesskey' => true, 'class' => true, 'contenteditable' => true,
            'dir' => true, 'draggable' => true, 'dropzone' => true,
            'id' => true, 'lang' => true,
            'spellcheck' => true, 'style' => true, 'tabindex' => true, 'title' => true,
            'translate' => true,
        ],
        'button' => [],
        'checkbox' => ['checked' => true, 'required' => true, ],
        'color' => [],
        'date' => ['max' => true, 'min' => true, 'pattern' => true, 'step' => true, ],
        'datetime-local' => ['max' => true, 'min' => true, 'required' => true, 'step' => true, ],
        'email' => [
            'list' => true, 'multiple' => true, 'pattern' => true,
            'placeholder' => true, 'required' => true, 'size' => true,
        ],
        'file' => [
            'accept' => true, 'capture' => true, 'multiple' => true,
            'readonly' => false, 'required' => true, 'value' => false
        ],
        'hidden' => [
            'dirname' => false, 'readonly' => false, 'required' => false
        ],
        'image' => [
            'alt' => true, 'formaction' => true, 'formenctype' => true,
            'formmethod' => true, 'formtarget' => true, 'height' => true,
            'src' => true, 'width' => true,
        ],
        'month' => ['max' => true, 'min' => true, 'required' => true, 'step' => true, ],
        'number' => [
            'list' => true, 'max' => true, 'maxlength' => true, 'min' => true,
            'required' => true, 'step' => true,
        ],
        'password' => [
            'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true, 'required' => true, 'size' => true,
        ],
        'radio' => ['checked' => true, 'required' => true, ],
        'range' => ['max' => true, 'min' => true, 'step' => true, ],
        'reset' => [],
        'search' => [
            'list' => true, 'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        // Select isn't an input type but it shares attributes
        'select' => [
            'multiple' => true, 'readonly' => false, 'required' => true, 'value' => false
        ],
        'submit' => [
            'formaction' => true, 'formenctype' => true, 'formmethod' => true,
            'formtarget' => true,
        ],
        'tel' => [
            'list' => true, 'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'text' => [
            'list' => true, 'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        // Textarea isn't an input type but it shares attributes
        'textarea' => [
            'cols' => true, 'list' => true, 'maxlength' => true, 'minlength' => true,
            'placeholder' => true, 'required' => true, 'rows' => true,
        ],
        'time' => ['max' => true, 'min' => true, 'step' => true, ],
        'url' => [
            'list' => true, 'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'week' => ['max' => true, 'min' => true, 'required' => true, 'step' => true, ],
    ];
    static $inputConfirmable = [
        'email', 'number', 'password', 'tel', 'text',
    ];
    static $inputDateTime = [
        'date' => 'Y-m-d',
        'datetime-local' => 'Y-m-d\TH:i',
        'month' => 'Y-m',
        'time' => 'H:i',
        'week' => 'Y-\WW',
    ];
    /**
     * Maps element types to render methods.
     * @var array
     */
    static $renderMethodCache = [];
    /**
     * Quick lookup for self-closing elements
     * @var array
     */
    static $selfClose = ['input' => 1, 'option' => 2];
    /**
     * Map validation-related attributes to properties in a Data\Validation object.
     * @var array
     */
    static $validationMap = [
        'accept' => ['accept', []],
        'maxlength' => ['maxLength', null],
        'minlength' => ['minLength', null],
        'max' => ['maxValue', null],
        'min' => ['minValue', null],
        '=multiple' => ['multiple', false],
        'pattern' => ['-pattern', ''],
        '=required' => ['required', false],
        'step' => ['step', null],
    ];

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
     * Add validation elements to an attributes list
     * @param array $attrs The attribute list
     * @param string $type The input type we're generating
     * @param \Abivia\NextForm\Data\Validation $validation
     */
    protected function addValidation(&$attrs, $type, $validation) {
        foreach (self::$validationMap as $attrName => $specs) {
            list($lookup) = $this -> parseAttribute($attrName);
            if (self::$inputAttributes[$type][$lookup]) {
                $setting = $validation -> get($specs[0]);
                if ($setting === $specs[1]) {
                    continue;
                }
                if ($lookup === 'accept') {
                    $attrs[$attrName] = implode(',', $setting);
                } elseif (
                    ($lookup == 'min' || $lookup == 'max')
                    && isset(self::$inputDateTime[$type])
                ){
                    $attrs[$attrName] = date(self::$inputDateTime[$type], strtotime($setting));
                } else {
                    $attrs[$attrName] = $setting;
                }
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
                $optAttrs = ['value' => $option -> getValue()];
                $sidecar = $option -> sidecar;
                if ($sidecar !== null) {
                    $optAttrs['*data-sidecar'] = $sidecar;
                }
                $block -> post .= '  ' . $this -> writeTag('option', $optAttrs) . "\n";
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

    /**
     * Extract a processing command (! no escape; = no value; * JSON encode) from an attribute, if any
     * @param string $attrName The attribute command and name
     * @return array Attribute name in the first element, command (or '') in the second.
     */
    protected function parseAttribute($attrName) {
        if (strpos('!=*', $attrName[0]) !== false) {
            $cmd = $attrName[0];
            $attrName = substr($attrName, 1);
        } else {
            $cmd = '';
        }
        return [$attrName, $cmd];
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
        $block -> body .= $this -> writeLabel(
                $labels -> heading, 'label', ['!for' => $element -> getId()]
            );
        $attrs['id'] = $element -> getId();
        if ($options['access'] == 'view') {
            $attrs['=disabled'] = 'disabled';
        }
        $attrs['name'] = $element -> getFormName();
        if ($labels -> inner !== null) {
            $attrs['value'] = $labels -> inner;
        }
        if ($options['access'] === 'read') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $attrs['type'] = 'hidden';
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
        } else {
            //
            // We can see or change the data
            //
            $attrs['type'] = $element -> getFunction();
            $block -> body .= $this -> writeLabel($labels -> before, 'span')
                . $this -> writeTag('input', $attrs)
                . $this -> writeLabel($labels -> after, 'span')
                . ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        }
        return $block;
    }

    protected function renderFieldElement(FieldElement $element, $options = []) {
        /*
            'image', 'textarea'
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
                case 'file':
                    $block = $this -> renderFieldFile($element, $options);
                    break;
                case 'select':
                    $block = $this -> renderFieldSelect($element, $options);
                    break;
                default:
                    $block = $this -> renderFieldCommon($element, $options);
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

    protected function renderFieldCommon(FieldElement $element, $options = []) {
        $attrs = [];
        $confirm = $options['confirm'];
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $block = new Block();
        $attrs['id'] = $element -> getId() . ($confirm ? '-confirm' : '');
        if ($options['access'] == 'view') {
            $attrs['=readonly'] = 'readonly';
        }
        $attrs['name'] = $element -> getFormName() . ($confirm ? '-confirm' : '');
        $value = $element -> getValue();
        if ($options['access'] === 'read' || $type === 'hidden') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $attrs['type'] = 'hidden';
            if (is_array($value)) {
                foreach ($value as $key => $entry) {
                    $attrs['name'] = $element -> getFormName() . '[' . htmlspecialchars($key) . ']';
                    $attrs['value'] = $entry;
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                }
            } else {
                if ($value !== null) {
                    $attrs['value'] = $value;
                }
                $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            }
        } else {
            //
            // We can see or change the data
            //
            if ($value !== null) {
                $attrs['value'] = $value;
            }
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                $confirm && $labels -> confirm != '' ? $labels -> confirm : $labels -> heading,
                'label', ['!for' => $attrs['id']]
            );
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            if ($type === 'range' && $options['access'] === 'view') {
                $type = 'text';
            }
            $attrs['type'] = $type;
            $block -> body .= $this -> writeLabel($labels -> before, 'span');
            $sidecar = $data -> getPopulation() -> sidecar;
            if ($sidecar !== null) {
                $attrs['*data-sidecar'] = $sidecar;
            }
            // Render the data list if there is one
            $block -> merge($this -> dataList($attrs, $element, $type, $options));
            if ($options['access'] === 'write') {
                // Write access: Add in any validation
                $this -> addValidation($attrs, $type, $data -> getValidation());
            }
            // Generate the input element
            $block -> body .= $this -> writeTag('input', $attrs)
                . $this -> writeLabel($labels -> after, 'span')
                . ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>')
                . "\n";
        }
        return $block;
    }

    protected function renderFieldCheckbox(FieldElement $element, $options = []) {
        $attrs = [];
        $block = new Block();
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $attrs['type'] = $type;
        $visible = true;
        if ($options['access'] == 'view') {
            $attrs['=readonly'] = 'readonly';
        } elseif ($options['access'] === 'read') {
            $attrs['type'] = 'hidden';
            $visible = false;
        }
        if ($visible) {
            $block -> body .= $this -> writeLabel($labels -> before, 'div');
        }
        $attrs['name'] = $element -> getFormName() . ($type == 'checkbox' ? '[]' : '');
        $list = $element -> getList(true);
        if (empty($list)) {
            $attrs['id'] = $baseId;
            if (($value = $element -> getValue()) !== null) {
                $attrs['value'] = $value;
            }
            $sidecar = $data -> getPopulation() -> sidecar;
            if ($sidecar !== null) {
                $attrs['*data-sidecar'] = $sidecar;
            }
            if ($visible) {
                $block -> body .= $this -> writeLabel($labels -> before, 'span');
            }
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            if ($visible) {
                $block -> body .= $this -> writeLabel(
                        $element -> getLabels(true) -> inner, 'label', ['for' => $baseId]
                    )
                    . $this -> writeLabel($labels -> after, 'span');
            }
        } else {
            $select = $element -> getValue();
            if ($select === null) {
                $select = $element -> getDefault();
            }
            foreach ($list as $optId => $radio) {
                $id = $baseId . '-opt' . $optId;
                $attrs['id'] = $id;
                $value = $radio -> getValue();
                $attrs['value'] = htmlspecialchars($value);
                if ($type == 'checkbox' && is_array($select) && in_array($value, $select)) {
                    $attrs['=checked'] = true;
                    $checked = true;
                } elseif ($value === $select) {
                    $attrs['=checked'] = true;
                    $checked = true;
                } else {
                    unset($attrs['=checked']);
                    $checked = false;
                }
                $sidecar = $radio -> sidecar;
                if ($sidecar !== null) {
                    $attrs['*data-sidecar'] = $sidecar;
                }
                if ($visible) {
                    if ($checked) {
                        $attrs['=checked'] = true;
                    } else {
                        unset($attrs['=checked']);
                    }
                    $block -> body .= "<div>\n  " . $this -> writeTag('input', $attrs) . "\n"
                        . '  ' . $this -> writeLabel($radio -> getLabel(), 'label', ['for' => $id])
                        . "</div>\n";
                } elseif ($checked) {
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                }
            }
        }
        if ($visible) {
            $this -> writeLabel($labels -> after, 'div');
            $block -> body .= ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        }
        return $block;
    }

    protected function renderFieldFile(FieldElement $element, $options = []) {
        $attrs = [];
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $block = new Block();
        $attrs['id'] = $element -> getId();
        if ($options['access'] == 'view') {
            $type = 'text';
        }
        $attrs['name'] = $element -> getFormName();
        $value = $element -> getValue();
        if ($options['access'] === 'read') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $attrs['type'] = 'hidden';
            if (is_array($value)) {
                $attrs['name'] .= '[]';
                foreach ($value as $item) {
                    $attrs['value'] = $item;
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                }
            } else {
                if ($value !== null) {
                    $attrs['value'] = $value;
                }
                $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            }
        } else {
            //
            // We can see or change the data
            //
            if ($value !== null) {
                $attrs['value'] = is_array($value) ? implode(',', $value) : $value;
            }
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                    $labels -> heading, 'label', ['!for' => $element -> getId()]
                );
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            $attrs['type'] = $type;
            $block -> body .= $this -> writeLabel($labels -> before, 'span');
            $sidecar = $data -> getPopulation() -> sidecar;
            if ($sidecar !== null) {
                $attrs['*data-sidecar'] = $sidecar;
            }
            // Render the data list if there is one
            $block -> merge($this -> dataList($attrs, $element, $type, $options));
            if ($options['access'] === 'write') {
                // Write access: Add in any validation
                $this -> addValidation($attrs, $type, $data -> getValidation());
                if ($type === 'file' && isset($attrs['=multiple'])) {
                    $attrs['name'] .= '[]';
                }
            } else {
                // View Access
                $attrs['type'] = 'text';
                $attrs['=readonly'] = 'readonly';
            }
            // Generate the input element
            $block -> body .= $this -> writeTag('input', $attrs)
                . $this -> writeLabel($labels -> after, 'span')
                . ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>')
                . "\n";
        }
        return $block;
    }

    protected function renderFieldImage(FieldElement $element, $options = []) {
        $attrs = [];
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $block = new Block();
        return; /// UNIMPLEMENTED
        $attrs['id'] = $element -> getId();
        if ($options['access'] == 'view') {
            $attrs['=readonly'] = 'readonly';
        }
        $attrs['name'] = $element -> getFormName();
        $value = $element -> getValue();
        if ($options['access'] === 'read' || $type === 'hidden') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $attrs['type'] = 'hidden';
            if (is_array($value)) {
                foreach ($value as $key => $entry) {
                    $attrs['name'] = $element -> getFormName() . '[' . htmlspecialchars($key) . ']';
                    $attrs['value'] = $entry;
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                }
            } else {
                if ($value !== null) {
                    $attrs['value'] = $value;
                }
                $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            }
        } else {
            //
            // We can see or change the data
            //
            if ($value !== null) {
                $attrs['value'] = $value;
            }
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                    $labels -> heading, 'label', ['!for' => $element -> getId()]
                );
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            if ($type === 'range' && $options['access'] === 'view') {
                $type = 'text';
            }
            $attrs['type'] = $type;
            $block -> body .= $this -> writeLabel($labels -> before, 'span');
            $sidecar = $data -> getPopulation() -> sidecar;
            if ($sidecar !== null) {
                $attrs['*data-sidecar'] = $sidecar;
            }
            // Render the data list if there is one
            $block -> merge($this -> dataList($attrs, $element, $type, $options));
            if ($options['access'] === 'write') {
                // Write access: Add in any validation
                $this -> addValidation($attrs, $type, $data -> getValidation());
            }
            // Generate the input element
            $block -> body .= $this -> writeTag('input', $attrs)
                . $this -> writeLabel($labels -> after, 'span')
                . ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>')
                . "\n";
        }
        return $block;
    }

    protected function renderFieldSelect(FieldElement $element, $options = []) {
        $attrs = [];
        $block = new Block();
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();
        $multiple = $data -> getValidation() -> get('multiple');

        $attrs['name'] = $element -> getFormName() . ($multiple ? '[]' : '');
        $value = $element -> getValue();
        //
        // Read-only: generate one or more hidden input elements
        //
        if ($options['access'] === 'read') {
            $attrs['type'] = 'hidden';
            if ($multiple) {
                if (!is_array($value)) {
                    $value = [$value];
                }
                // Generate a list of input elements
                foreach ($value as $optId => $item) {
                    $id = $baseId . '-opt' . $optId;
                    $attrs['id'] = $id;
                    $attrs['value'] = $item;
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                }
            } else {
                $attrs['id'] = $baseId;
                if ($value !== null) {
                    $attrs['value'] = $value;
                }
                // Generate the input element
                $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            }
        } else {
            // This element is visible
            $block -> body .= $this -> writeLabel($labels -> before, 'div');
            if ($options['access'] == 'view') {
                $list = $element -> getFlatList(true);
                // render as hidden with text
                $attrs['type'] = 'hidden';
                if ($multiple) {
                    // step through each possible value, output matches
                    if (!is_array($value)) {
                        $value = [$value];
                    }
                    $optId = 0;
                    foreach ($list as $option) {
                        $slot = array_search($option -> getValue(), $value);
                        if ($slot !== false) {
                            $id = $baseId . '-opt' . $optId;
                            $attrs['id'] = $id;
                            $attrs['value'] = $value[$slot];
                            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                            $block -> body .= $this -> writeTag('span', [], $option -> getLabel())
                                . "<br/>\n";
                            ++$optId;
                        }
                    }
                } else {
                    $attrs['id'] = $baseId;
                    $attrs['value'] = $value;
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                    foreach ($list as $option) {
                        if ($value == $option -> getValue()) {
                            $block -> body .= $this -> writeTag('span')
                                . $option -> getLabel() . '</span>'
                                . "\n";
                        }
                    }
                }
            } else {
                // Generate an actual select!
                if ($value === null) {
                    $value = $element -> getDefault();
                }
                if (!is_array($value)) {
                    $value = [$value];
                }
                $attrs['id'] = $baseId;
                $this -> addValidation($attrs, 'select', $data -> getValidation());
                $block -> body .= $this -> writeTag('select', $attrs) . "\n";
                $block -> merge(
                    $this -> renderFieldSelectOptions($element -> getList(true), $value)
                );
                $block -> body .= '</select>' . "\n";
            }
            $this -> writeLabel($labels -> after, 'div');
            $block -> body .= ($this -> context[0]['inCell'] ? '&nbsp;' : '<br/>') . "\n";
            $block -> body .= $this -> writeLabel($labels -> before, 'div');
        }
        return $block;
    }

    protected function renderFieldSelectOption($option, $value) {
        $block = new Block;
        $attrs = [];
        $attrs['value'] = $option -> getValue();
        if (($sidecar = $option -> getSidecar()) !== null) {
            $attrs['*data-sidecar'] = $sidecar;
        }
        if (in_array($attrs['value'], $value)) {
            $attrs['=selected'] = true;
        }
        $block -> body .= '  ' . $this -> writeTag('option', $attrs, $option -> getLabel()) . "\n";
        return $block;
    }

    protected function renderFieldSelectOptions($list, $value) {
        $block = new Block;
        foreach ($list as $option) {
            if ($option -> isNested()) {
                $attrs = ['label' => $option -> getLabel()];
                if (($sidecar = $option -> getSidecar()) !== null) {
                    $attrs['*data-sidecar'] = $sidecar;
                }
                $block -> body .= $this -> writeTag('optgroup', $attrs) . "\n";
                $block -> merge($this -> renderFieldSelectOptions($option -> getList(), $value));
                $block -> body .= '</optgroup>' . "\n";
            } else {
                $block -> merge($this -> renderFieldSelectOption($option, $value));
            }
        }
        return $block;
    }

    protected function renderSectionElement(Element $element, $options = []) {
        $labels = $element -> getLabels(true);
        $block = new Block();
        $block -> body = '<fieldset>' . "\n"
            . $this -> writeLabel($labels -> heading, 'legend');
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
        $pageData -> body = $this -> writeTag('form', $attrs) . "\n";
        $pageData -> post = '</form>' . "\n";
        return $pageData;
    }

    /**
     * Encode an attribute into escaped HTML
     * @param string $atrName The attribute name with optional processing command.
     * @param string $value The attribute value.
     * @return string
     */
    protected function writeAttribute($attrName, $cmd, $value) {
        switch ($cmd) {
            case '!': {
                // Attrribute that does not need to be escaped
                $html = ' ' . $attrName . '="' . $value . '"';
            }
            break;

            case '*': {
                // JSON-endoced attribute
                $html = ' ' . $attrName . '="' . htmlspecialchars(json_encode($value)) . '"';
            }
            break;

            case '=': {
                // Stand-alone attribute with no value
                $html = ' ' . $attrName;
            }
            break;

            default: {
                $html = ' ' . $attrName . '="' . htmlspecialchars($value) . '"';
            }
            break;

        }
        return $html;
    }

    protected function writeLabel($text, $tag, $attrs = []) {
        $html = $text === null ? '' : $this -> writeTag($tag, $attrs)
            . htmlspecialchars($text)
            . '</' . $tag . '>' . ($tag === 'span' ? '' : "\n")
        ;
        return $html;
    }

    /**
     * Write an element and attributes into escaped HTML
     * @param array $attrs
     * @return string
     */
    protected function writeTag($tag, $attrs = [], $text = null) {
        $html = '<' . $tag;
        $parts = [];
        if ($tag === 'input') {
            $mask = self::$inputAttributes[$attrs['type']];
            foreach ($attrs as $attrName => $value) {
                // For input elements, only write the allowed attributes
                list($lookup, $cmd) = $this -> parseAttribute($attrName);
                if (
                    (isset($mask[$lookup]) && $mask[$lookup])
                    || substr($lookup, 0, 5) === 'data-'
                ) {
                    $parts[$lookup] = $this -> writeAttribute($lookup, $cmd, $value);
                }
            }
        } else {
            foreach ($attrs as $attrName => $value) {
                list($lookup, $cmd) = $this -> parseAttribute($attrName);
                $parts[$lookup] = $this -> writeAttribute($lookup, $cmd, $value);
            }
        }
        foreach (self::$highlightAttribute as $attrName) {
            if (isset($parts[$attrName])) {
                $html .= $parts[$attrName];
                unset($parts[$attrName]);
            }
        }
        $html .= implode('', $parts);
        if (isset(self::$selfClose[$tag]) && $text === null) {
            $html .= '/>';
        } elseif ($text !== null) {
            $html .= '>' . htmlentities($text) . '</' . $tag . '>';
        } else {
            $html .= '>';
        }
        return $html;
    }

}

