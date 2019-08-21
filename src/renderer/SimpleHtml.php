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
 * A skeletal renderer that generates a very basic form.
 */
class SimpleHtml extends Html implements Renderer {

    /**
     * Maps element types to render methods.
     * @var array
     */
    static $renderMethodCache = [];

    public function __construct($options = []) {
        parent::__construct($options);
        $this -> initialize();
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

    protected function elementHidden($element, $value) {
        $block = new Block;
        $baseId = $element -> getId();
        $attrs = ['type' => 'hidden'];
        if (is_array($value)) {
            $optId = 0;
            foreach ($value as $key => $entry) {
                $attrs['id'] = $baseId . '-opt' . $optId;
                ++$optId;
                $attrs['name'] = $element -> getFormName() . '[' . htmlspecialchars($key) . ']';
                $attrs['value'] = $entry;
                $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            }
        } else {
            $attrs['id'] = $baseId;
            $attrs['name'] = $element -> getFormName();
            if ($value !== null) {
                $attrs['value'] = $value;
            }
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
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

    protected function initialize() {
        // Reset the context
        $this -> context = [
            'inCell' => false
        ];
        // Initialize custom settings
        $this -> setShow('layout:vertical');
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
        $attrs['id'] = $element -> getId();
        if ($options['access'] == 'view') {
            $attrs['=disabled'] = 'disabled';
        }
        $attrs['name'] = $element -> getFormName();
        $labels = $element -> getLabels(true);
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
            $block -> body .= $this -> writeLabel(
                    'heading', $labels -> heading, 'label', ['!for' => $element -> getId()]
                );
            $block = $this -> writeWrapper($block, 'div', 'input-wrapper');
            $attrs['type'] = $element -> getFunction();
            $block -> body .= $this -> writeLabel('before', $labels -> before, 'span')
                . $this -> writeTag('input', $attrs)
                . $this -> writeLabel('after', $labels -> after, 'span');
            $block -> close();
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        }
        return $block;
    }

    protected function renderCellElement(CellElement $element, $options = []) {
        $block = new Block();
        $block = $this -> writeWrapper($block, 'div', 'input-wrapper', ['force' => true]);
        $block -> onCloseDone = [$this, 'popContext'];
        $this -> pushContext();
        $this -> context['inCell'] = true;
        $this -> showDoLayout('form', 'inline');
        return $block;
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
                case 'file':
                    $block = $this -> renderFieldFile($element, $options);
                    break;
                case 'select':
                    $block = $this -> renderFieldSelect($element, $options);
                    break;
                case 'textarea':
                    $block = $this -> renderFieldTextarea($element, $options);
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
        $attrs['id'] = $element -> getId() . ($confirm ? '-confirmation' : '');
        if ($options['access'] == 'view') {
            $attrs['=readonly'] = 'readonly';
        }
        $attrs['name'] = $element -> getFormName() . ($confirm ? '-confirmation' : '');
        $value = $element -> getValue();
        if ($options['access'] === 'read' || $type === 'hidden') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            if (!$confirm) {
                $block -> merge($this -> elementHidden($element, $value));
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
                'heading',
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
            $block = $this -> writeWrapper($block, 'div', 'input-wrapper');
            $block -> body .= $this -> writeLabel('before', $labels -> before, 'span');
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
                . $this -> writeLabel('after', $labels -> after, 'span');
            $block -> close();
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
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
        $attrs['name'] = $element -> getFormName() . ($type == 'checkbox' ? '[]' : '');
        $list = $element -> getList(true);
        if ($visible) {
            $block -> body .= $this -> writeLabel('heading', $labels -> heading, 'div');
            $block = $this -> writeWrapper($block, 'div', 'input-wrapper');
            $bracketTag = empty($list) ? 'span' : 'div';
            $block -> body .= $this -> writeLabel('before', $labels -> before, $bracketTag);
        }
        if (empty($list)) {
            $attrs['id'] = $baseId;
            if (($value = $element -> getValue()) !== null) {
                $attrs['value'] = $value;
            }
            $sidecar = $data -> getPopulation() -> sidecar;
            if ($sidecar !== null) {
                $attrs['*data-sidecar'] = $sidecar;
            }
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            if ($visible) {
                $block -> body .= $this -> writeLabel(
                        'inner', $element -> getLabels(true) -> inner,
                        'label', ['for' => $baseId]
                    )
                    ;
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
                        . '  '
                        . $this -> writeLabel('', $radio -> getLabel(), 'label', ['for' => $id])
                        . "</div>\n";
                } elseif ($checked) {
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                }
            }
        }
        if ($visible) {
            $block -> body .= $this -> writeLabel('after', $labels -> after, $bracketTag);
            $block -> close();
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
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
            $block -> merge($this -> elementHidden($element, $value));
        } else {
            //
            // We can see or change the data
            //
            if ($value !== null) {
                $attrs['value'] = is_array($value) ? implode(',', $value) : $value;
            }
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                    'heading', $labels -> heading, 'label', ['!for' => $element -> getId()]
                );
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            $attrs['type'] = $type;
            $block = $this -> writeWrapper($block, 'div', 'input-wrapper');
            $block -> body .= $this -> writeLabel('before', $labels -> before, 'span');
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
                . $this -> writeLabel('after', $labels -> after, 'span');
            $block -> close();
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
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
            $block -> merge($this -> elementHidden($element, $value));
        } else {
            //
            // We can see or change the data
            //
            if ($value !== null) {
                $attrs['value'] = $value;
            }
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                    'heading', $labels -> heading, 'label', ['!for' => $element -> getId()]
                );
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            if ($type === 'range' && $options['access'] === 'view') {
                $type = 'text';
            }
            $attrs['type'] = $type;
            $block -> body .= $this -> writeLabel('before', $labels -> before, 'span');
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
                . $this -> writeLabel('after', $labels -> after, 'span')
                . ($this -> context['inCell'] ? '&nbsp;' : '<br/>')
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
        if ($options['access'] === 'read') {
            //
            // Read-only: generate one or more hidden input elements
            //
            $block -> merge($this -> elementHidden($element, $value));
        } else {
            // This element is visible
            $block -> body .= $this -> writeLabel('heading', $labels -> heading, 'div');
            $block = $this -> writeWrapper($block, 'div', 'input-wrapper');
            $block -> body .= $this -> writeLabel('before', $labels -> before, 'div');
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
                if (($rows = $data -> getPresentation() -> getRows()) !== null) {
                    $attrs['size'] = $rows;
                }
                $this -> addValidation($attrs, 'select', $data -> getValidation());
                $block -> body .= $this -> writeTag('select', $attrs) . "\n";
                $block -> merge(
                    $this -> renderFieldSelectOptions($element -> getList(true), $value)
                );
                $block -> body .= '</select>' . "\n";
            }
            $this -> writeLabel('after', $labels -> after, 'div');
            $block -> close();
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
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

    protected function renderFieldTextarea(FieldElement $element, $options = []) {
        $attrs = [];
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $block = new Block();
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
            $block -> merge($this -> elementHidden($element, $value));
        } else {
            //
            // We can see or change the data
            //
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                'heading', $labels -> heading,
                'label', ['!for' => $attrs['id']]
            );
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            if (($aval = $presentation -> getCols()) !== null) {
                $attrs['cols'] = $aval;
            }
            if (($aval = $presentation -> getRows()) !== null) {
                $attrs['rows'] = $aval;
            }
            $block -> body .= $this -> writeLabel('before', $labels -> before, 'div');
            $sidecar = $data -> getPopulation() -> sidecar;
            if ($sidecar !== null) {
                $attrs['*data-sidecar'] = $sidecar;
            }
            if ($options['access'] === 'write') {
                // Write access: Add in any validation
                $this -> addValidation($attrs, $type, $data -> getValidation());
            }
            if ($value === null) {
                $value = '';
            }
            // Generate the textarea element
            $block -> body .= $this -> writeTag('textarea', $attrs, $value)
                . $this -> writeLabel('after', $labels -> after, 'div')
                . ($this -> context['inCell'] ? '&nbsp;' : '<br/>')
                . "\n";
        }
        return $block;
    }

    protected function renderHtmlElement(HtmlElement $element, $options = []) {
        $block = new Block();
        $block -> body .= $this -> writeLabel('heading', null, 'div');
        $block = $this -> writeWrapper($block, 'div', 'input-wrapper', ['append' => "<br/>\n"]);
        $block -> body .= $element -> getValue();
        $block -> close();
        return $block;
    }

    protected function renderSectionElement(SectionElement $element, $options = []) {
        $block = new Block();
        $labels = $element -> getLabels(true);
        $block -> body = '<fieldset>' . "\n";
        if ($labels !== null) {
            $block -> body .= $this -> writeLabel('', $labels -> heading, 'legend');
        }
        $block -> post = '</fieldset>' . "\n";
        return $block;
    }

    protected function renderStaticElement(StaticElement $element, $options = []) {
        $block = new Block();
        $block -> body .= $this -> writeLabel('heading', null, 'div');
        $block = $this -> writeWrapper($block, 'div', 'input-wrapper', ['append' => "<br/>\n"]);
        $block -> body .= htmlspecialchars($element -> getValue());
        $block -> close();
        return $block;
    }

    public function setOptions($options = []) {

    }

    /**
     * Process layout options, called from showValidate()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $value Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoLayout($scope, $choice, $value = []) {
        if (!isset($this -> custom[$scope]['layout'])) {
            return;
        }
        $apply = &$this -> custom[$scope];
        $apply['layout'] = $choice;
        if ($choice === 'vertical') {
            unset($apply['input-wrapper']);
        }
        if ($choice !== 'horizontal') {
            return;
        }
        // possible values for arguments:
        // h            - We get to decide
        // h:nxx        - First column width in CSS units
        // h:nxx/mxx    - CSS units for headers / input elements
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Class for headers
        // h:.c1:.c2    - Class for headers / input elements
        switch (count($value)) {
            case 1:
                // No specification, use our default
                $apply['heading'] = [
                    'style' => [
                        'display' => 'inline-block', 'vertical-align' => 'top',
                        'width' => '25%'
                    ],
                ];
                $apply['input-wrapper'] = [
                    'style' => [
                        'display' => 'inline-block', 'vertical-align' => 'top',
                        'width' => '75%'
                    ],
                ];
                break;
            case 2:
                if ($value[1][0] == '.') {
                    // Single class specification
                    $apply['heading'] = [
                        'class' => [substr($value[1], 1)],
                    ];
                    unset($apply['input-wrapper']);
                } else {
                    // Single CSS units
                    $apply['heading'] = [
                        'style' => [
                            'display' => 'inline-block', 'vertical-align' => 'top',
                            'width' => $value[1]
                        ],
                    ];
                    unset($apply['input-wrapper']);
                }
                break;
            default:
                if ($value[1][0] == '.') {
                    // Dual class specification
                    $apply['heading'] = [
                        'class' => [substr($value[1], 1)],
                    ];
                    $apply['input-wrapper'] = [
                        'class' => [substr($value[2], 1)],
                    ];
                } elseif (preg_match('/^[+\-]?[0-9](\.[0-9]*)?$/', $value[1])) {
                    // ratio
                    $part1 = (float) $value[1];
                    $part2 = (float) $value[2];
                    if (!$part1 || !$part2) {
                        throw new \RuntimeException(
                            'Invalid ratio: ' . $value[1] . ':' . $value[2]
                        );
                    }
                    $sum = isset($value[3]) ? $value[3] : ($part1 + $part2);
                    $apply['heading'] = [
                        'style' => [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part1 / $sum, 3) . '%'
                        ],
                    ];
                    $apply['input-wrapper'] = [
                        'style' => [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part2 / $sum, 3) . '%'
                        ],
                    ];
                } else {
                    // Dual CSS units
                    $apply['heading'] = [
                        'style' => [
                            'display' => 'inline-block', 'vertical-align' => 'top',
                            'width' => $value[1]
                        ],
                    ];
                    $apply['input-wrapper'] = [
                        'style' => [
                            'display' => 'inline-block', 'vertical-align' => 'top',
                            'width' => $value[2]
                        ],
                    ];
                }
                break;

        }
    }

}

