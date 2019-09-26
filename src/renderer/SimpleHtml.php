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
class SimpleHtml extends CommonHtml implements Renderer {

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

    protected function checkList(Block $block, FieldElement $element, $list, $type, Attributes $attrs) {
        $baseId = $element -> getId();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        foreach ($list as $optId => $radio) {
            $id = $baseId . '-opt' . $optId;
            $attrs -> set('id', $id);
            $value = $radio -> getValue();
            $attrs -> set('value', $value);
            $attrs -> setFlag('disabled', !$radio -> getEnabled());
            if (
                $type == 'checkbox'
                && is_array($select) && in_array($value, $select)
            ) {
                $attrs -> setFlag('checked');
                $checked = true;
            } elseif ($value === $select) {
                $attrs -> setFlag('checked');
                $checked = true;
            } else {
                $attrs -> setFlag('checked', false);
                $checked = false;
            }
            $attrs -> setIfNotNull('*data-sidecar', $radio -> sidecar);
            if ($checked) {
                $attrs -> setFlag('checked');
            } else {
                $attrs -> setFlag('checked', false);
            }
            $block -> body .= "<div>\n" . $this -> writeTag('input', $attrs) . "\n"
                . $this -> writeLabel(
                    '', $radio -> getLabel(), 'label',
                    new Attributes('!for',  $id), ['break' => true]
                )
                . "</div>\n";
        }
    }

    protected function initialize() {
        // Reset the context
        $this -> context = [
            'inCell' => false
        ];
        // Initialize custom settings
        $this -> setShow('layout:vertical');
    }

    protected function renderButtonElement(ButtonElement $element, $options = []) {
        $attrs = new Attributes;
        $attrs -> set('id', $element -> getId());
        if ($options['access'] == 'view' || !$element -> getEnabled()) {
            $attrs -> setFlag('disabled');
        }
        $attrs -> set('name', $element -> getFormName());
        $labels = $element -> getLabels(true);
        $attrs -> setIfNotNull('value', $labels -> inner);

        $block = new Block();
        if ($options['access'] === 'hide') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $attrs -> set('type', 'hidden');
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            return $block;
        }

        // We can see or change the data
        $block -> merge(
            $this -> writeElement('div', ['attrs' => $this -> groupAttributes($element)])
        );
        $block -> body .= $this -> writeLabel(
                'headingAttributes', $labels -> heading, 'label',
                new Attributes('!for', $element -> getId()), ['break' => true]
            );
        $block = $this -> writeWrapper($block, 'div', ['show' => 'input-wrapper']);
        $attrs -> set('type', $element -> getFunction());
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $attrs -> get('id') . '-formhelp');
        }
        $block -> body .= $this -> writeLabel('before', $labels -> before, 'span')
            . $this -> writeTag('input', $attrs)
            . $this -> writeLabel('after', $labels -> after, 'span') . "\n";
        if ($labels -> has('help')) {
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
            $block -> body .= $this -> writeLabel(
                'help', $labels -> help, 'small',
                new Attributes('id', $attrs -> get('aria-describedby')),
                ['break' => true]
            );
        }
        $block -> close();
        $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        return $block;
    }

    protected function renderCellElement(CellElement $element, $options = []) {
        $block = new Block();
        $block = $this -> writeWrapper($block, 'div', ['force' => true, 'show' => 'input-wrapper']);
        $block -> onCloseDone = [$this, 'popContext'];
        $this -> pushContext();
        $this -> context['inCell'] = true;
        $this -> showDoLayout('form', 'inline');
        return $block;
    }

    protected function renderFieldCommon(FieldElement $element, $options = []) {
        $attrs = new Attributes;
        $confirm = $options['confirm'];
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $block = new Block();
        $attrs -> set('id', $element -> getId() . ($confirm ? '-confirmation' : ''));
        $attrs -> setFlag('readonly', $element -> getReadonly() || $options['access'] == 'view');
        $attrs -> set('name', $element -> getFormName() . ($confirm ? '-confirmation' : ''));
        $value = $element -> getValue();
        if ($options['access'] === 'hide' || $type === 'hidden') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            if (!$confirm) {
                $block -> merge($this -> elementHidden($element, $value));
            }
        } else {

            // We can see or change the data
            $block -> merge(
                $this -> writeElement(
                    'div', [
                        'attrs' => $this -> groupAttributes(
                            $element, ['id' => $attrs -> get('id')]
                        )
                    ]
                )
            );

            if ($value !== null) {
                $attrs -> set('value', $value);
            }
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                    'headingAttributes',
                    $confirm && $labels -> confirm != '' ? $labels -> confirm : $labels -> heading,
                    'label', new Attributes('!for', $attrs -> get('id')), ['break' => true]
                );
            $attrs -> setIfNotNull('placeholder', $labels -> inner);
            if ($type === 'range' && $options['access'] === 'view') {
                $type = 'text';
            }
            $attrs -> set('type', $type);
            $block = $this -> writeWrapper($block, 'div', ['show' => 'input-wrapper']);
            $block -> body .= $this -> writeLabel('before', $labels -> before, 'span');
            // Render the data list if there is one
            $block -> merge($this -> dataList($attrs, $element, $type, $options));
            if ($options['access'] === 'write') {
                // Write access: Add in any validation
                $attrs -> addValidation($type, $data -> getValidation());
            }
            // Generate the input element
            $block -> body .= $this -> writeTag('input', $attrs)
                . $this -> writeLabel('after', $labels -> after, 'span') . "\n";
            $block -> close();
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : "<br/>\n");
        }
        return $block;
    }

    /**
     * Render Field elements for checkbox and radio types.
     * @param FieldElement $element
     * @param array $options
     * @return Block
     */
    protected function renderFieldCheckbox(FieldElement $element, $options = []) {
        if ($options['access'] === 'hide') {
            // Generate hidden elements and return
            return $this -> elementHiddenList($element);
        }

        // Get things we need to generate attributes
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();

        // Set attributes for the input
        $attrs = new Attributes('type', $type);
        $attrs -> setFlag('readonly', $element -> getReadonly() || $options['access'] == 'view');
        $list = $element -> getList(true);
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);
        $attrs -> set('name', $element -> getFormName()
            . ($type == 'checkbox' && !empty($list) ? '[]' : ''));

        // Start generating output
        $block = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element)
            ]
        );
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'div', null, ['break' => true]
        );
        $block -> merge($this -> writeElement('div', ['show' => 'input-wrapper']));
        $bracketTag = empty($list) ? 'span' : 'div';
        $block -> body .= $this -> writeLabel(
            'before', $labels -> before, $bracketTag, null, ['break' => !empty($list)]
        );
        if (empty($list)) {
            $attrs -> set('id', $baseId);
            $value = $element -> getValue();
            if ($value !== null) {
                $attrs -> set('value', $value);
                if ($value === $element -> getDefault()) {
                    $attrs -> setFlag('checked');
                }
            }
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            $block -> body .= $this -> writeLabel(
                'inner', $element -> getLabels(true) -> inner,
                'label', new Attributes('!for', $baseId), ['break' => true]
            );
        } else {
            $this -> checkList($block, $element, $list, $type, clone $attrs);
        }
        $block -> body .= $this -> writeLabel(
            'after', $labels -> after, $bracketTag, null, ['break' => !empty($list)]
        );
        $block -> close();
        $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        return $block;
    }

    protected function renderFieldFile(FieldElement $element, $options = []) {
        $value = $element -> getValue();
        if ($options['access'] === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            return $this -> elementHidden($element, $value);
        }

        // We can see or change the data
        $attrs = new Attributes;
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $attrs -> set('id', $element -> getId());
        if ($options['access'] == 'view') {
            $type = 'text';
        }
        $attrs -> set('name', $element -> getFormName());
        $attrs -> setIfNotNull('value', is_array($value) ? implode(',', $value) : $value);
        $labels = $element -> getLabels(true);

        $block = $this -> writeElement(
            'div', ['attrs' => $this -> groupAttributes($element)]
        );
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'label',
            new Attributes('!for', $element -> getId()), ['break' => true]
        );
        $attrs -> setIfNotNull('placeholder', $labels -> inner);
        $attrs -> set('type', $type);
        $block = $this -> writeWrapper($block, 'div', ['show' => 'input-wrapper']);
        $block -> body .= $this -> writeLabel('before', $labels -> before, 'span');
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);
        // Render the data list if there is one
        $block -> merge($this -> dataList($attrs, $element, $type, $options));
        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs -> addValidation($type, $data -> getValidation());
            if ($type === 'file' && $attrs -> has('=multiple')) {
                $attrs -> set('name', $element -> getFormName() . '[]');
            }
            $attrs -> setFlag('readonly', $element -> getReadonly());
        } else {
            // View Access
            $attrs -> set('type', 'text');
            $attrs -> setFlag('readonly');
        }
        // Generate the input element
        $block -> body .= $this -> writeTag('input', $attrs) . "\n"
            . $this -> writeLabel('after', $labels -> after, 'span');
        $block -> close();
        $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        return $block;
    }

    protected function renderFieldImage(FieldElement $element, $options = []) {
        $attrs = new Attributes;
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $block = new Block();
        return; /// UNIMPLEMENTED
        $attrs['id'] = $element -> getId();
        $attrs -> setFlag('readonly', $element -> getReadonly() || $options['access'] == 'view');
        $attrs['name'] = $element -> getFormName();
        $value = $element -> getValue();
        if ($options['access'] === 'hide' || $type === 'hidden') {
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
                'headingAttributes', $labels -> heading, 'label',
                ['!for' => $element -> getId()], ['break' => true]
            );
            $labels -> insertInnerTo($attrs, 'placeholder');
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
        $value = $element -> getValue();
        if ($options['access'] === 'hide') {

            // Hide: generate one or more hidden input elements
            return $this -> elementHidden($element, $value);
        }
        // This element is visible
        $attrs = new Attributes;
        $block = new Block();
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();
        $multiple = $data -> getValidation() -> get('multiple');

        $attrs -> set('name', $element -> getFormName() . ($multiple ? '[]' : ''));

        $block = $this -> writeElement(
            'div', ['attrs' => $this -> groupAttributes($element)]
        );
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'div', null, ['break' => true]
        );
        $block = $this -> writeWrapper($block, 'div', ['show' => 'input-wrapper']);
        $block -> body .= $this -> writeLabel(
            'before', $labels -> before, 'div', null, ['break' => true]
        );
        if ($options['access'] == 'view') {
            $list = $element -> getFlatList(true);
            // render as hidden with text
            $attrs -> set('type', 'hidden');
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
                        $attrs -> set('id', $id);
                        $attrs -> set('value', $value[$slot]);
                        $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                        $block -> body .= $this -> writeTag('span', [], $option -> getLabel())
                            . "<br/>\n";
                        ++$optId;
                    }
                }
            } else {
                $attrs -> set('id', $baseId);
                $attrs -> set('value', $value);
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
            $attrs -> set('id', $baseId);
            if (($rows = $data -> getPresentation() -> getRows()) !== null) {
                $attrs -> set('size', $rows);
            }
            $attrs -> addValidation('select', $data -> getValidation());
            $block -> body .= $this -> writeTag('select', $attrs) . "\n";
            $block -> merge(
                $this -> renderFieldSelectOptions($element -> getList(true), $value)
            );
            $block -> body .= '</select>' . "\n";
        }
        $this -> writeLabel('after', $labels -> after, 'div', null, ['break' => true]);
        $block -> close();
        $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";

        return $block;
    }

    protected function renderFieldSelectOption($option, $value) {
        $block = new Block;
        $attrs = new Attributes;
        $attrs -> set('value', $option -> getValue());
        $attrs -> setIfNotNull('*data-sidecar', $option -> getSidecar());
        if (in_array($attrs -> get('value'), $value)) {
            $attrs -> setFlag('selected');
        }
        $block -> body .= $this -> writeTag('option', $attrs, $option -> getLabel()) . "\n";
        return $block;
    }

    protected function renderFieldSelectOptions($list, $value) {
        $block = new Block;
        foreach ($list as $option) {
            if ($option -> isNested()) {
                $attrs = new Attributes;
                $attrs -> set('label', $option -> getLabel());
                $attrs -> setIfNotNull('*data-sidecar', $option -> getSidecar());
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
        $value = $element -> getValue();
        if ($options['access'] === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            return $this -> elementHidden($element, $value);
        }

        // We can see or change the data
        $attrs = new Attributes;
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $attrs -> set('id', $element -> getId());
        $attrs -> setFlag('readonly', $element -> getReadonly() || $options['access'] == 'view');
        $attrs -> set('name', $element -> getFormName());

        $block = $this -> writeElement(
            'div', ['attrs' => $this -> groupAttributes($element)]
        );
        $labels = $element -> getLabels(true);
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'label',
            new Attributes('!for', $attrs -> get('id')), ['break' => true]
        );
        $attrs -> setIfNotNull('placeholder', $labels -> inner);
        $attrs -> setIfNotNull('cols', $presentation -> getCols());
        $attrs -> setIfNotNull('rows', $presentation -> getRows());
        $block -> body .= $this -> writeLabel(
            'before', $labels -> before, 'div', null, ['break' => true]
        );
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);
        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs -> addValidation($type, $data -> getValidation());
        }
        if ($value === null) {
            $value = '';
        }
        // Generate the textarea element
        $block -> body .= $this -> writeTag('textarea', $attrs, $value)
            . $this -> writeLabel(
                'after', $labels -> after, 'div', null, ['break' => true]
            )
            . "\n";

        $block -> close();
        return $block;
    }

    protected function renderSectionElement(SectionElement $element, $options = []) {
        $block = new Block();
        $labels = $element -> getLabels(true);
        $block -> body = '<fieldset>' . "\n";
        if ($labels !== null) {
            $block -> body .= $this -> writeLabel(
                '', $labels -> heading, 'legend', null, ['break' => true]
            );
        }
        $block -> post = '</fieldset>' . "\n";
        return $block;
    }

    protected function renderStaticElement(StaticElement $element, $options = []) {
        $block = new Block();

        // There's no way to hide this element so if access is hidden, skip it.
        if ($options['access'] === 'hide') {
            return $block;
        }

        $block = $this -> writeElement(
            'div', ['attrs' => $this -> groupAttributes($element)]
        );

        // Write a heading if there is one
        $labels = $element -> getLabels(true);
        $block -> body .= $this -> writeLabel(
            'headingAttributes',
            $labels ? $labels -> heading : null,
            'div', null, ['break' => true]
        );
        $block -> merge($this -> writeElement('div', ['show' => 'input-wrapper']));

        $attrs = new Attributes('id', $element -> getId());
        $block -> merge($this -> writeElement('div', ['attrs' => $attrs]));
        // Escape the value if it's not listed as HTML
        $value = $element -> getValue() . "\n";
        $block -> body .= $element -> getHtml() ? $value : htmlspecialchars($value);
        $block -> close();
        $block -> body .= "<br/>\n";

        return $block;
    }

    public function setOptions($options = []) {

    }

    /**
     * Process layout options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoLayout($scope, $choice, $values = []) {
        if (!isset($this -> showState[$scope])) {
            $this -> showState[$scope] = [];
        }
        // Clear out anything that might have been set by previous commands.
        unset($this -> showState[$scope]['headingAttributes']);
        unset($this -> showState[$scope]['input-wrapper']);
        $this -> showState[$scope]['layout'] = $choice;
        if ($choice === 'horizontal') {
            $this -> showDoLayoutAnyHorizontal($scope, $values);
        } elseif ($choice === 'vertical') {
            $this -> showDoLayoutAnyVertical($scope, $values);
        }
    }

    /**
     * Process horizontal layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    protected function showDoLayoutAnyHorizontal($scope, $values) {
        // possible values for arguments:
        // h            - We get to decide
        // h:nxx        - First column width in CSS units
        // h:nxx/mxx    - CSS units for headers / input elements
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Class for headers
        // h:.c1:.c2    - Class for headers / input elements
        $apply = &$this -> showState[$scope];
        switch (count($values)) {
            case 1:
                // No specification, use our default
                $apply['headingAttributes'] = new Attributes(
                    'style',
                    [
                        'display' => 'inline-block',
                        'vertical-align' => 'top',
                        'width' => '25%'
                    ]
                );
                $apply['input-wrapper'] = new Attributes(
                    'style',
                    [
                        'display' => 'inline-block',
                        'vertical-align' => 'top',
                        'width' => '75%'
                    ]
                );
                break;
            case 2:
                if ($values[1][0] == '.') {
                    // Single class specification
                    $apply['headingAttributes'] = new Attributes('class', [substr($values[1], 1)]);
                } else {
                    // Single CSS units
                    $apply['headingAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[1]
                        ]
                    );
                }
                break;
            default:
                if ($values[1][0] == '.') {
                    // Dual class specification
                    $apply['headingAttributes'] = new Attributes('class', [substr($values[1], 1)]);
                    $apply['input-wrapper'] = new Attributes('class', [substr($values[2], 1)]);
                } elseif (preg_match('/^[+\-]?[0-9](\.[0-9]*)?$/', $values[1])) {
                    // ratio
                    $part1 = (float) $values[1];
                    $part2 = (float) $values[2];
                    if (!$part1 || !$part2) {
                        throw new \RuntimeException(
                            'Invalid ratio: ' . $values[1] . ':' . $values[2]
                        );
                    }
                    $sum = isset($values[3]) ? $values[3] : ($part1 + $part2);
                    $apply['headingAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part1 / $sum, 3) . '%'
                        ]
                    );
                    $apply['input-wrapper'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part2 / $sum, 3) . '%'
                        ]
                    );
                } else {
                    // Dual CSS units
                    $apply['headingAttributes'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[1]
                        ]
                    );
                    $apply['input-wrapper'] = new Attributes(
                        'style',
                        [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[2]
                        ]
                    );
                }
                break;

        }
    }

    /**
     * Process vertical layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    protected function showDoLayoutAnyVertical($scope, $values) {
        // possible values for arguments:
        // v            - Default, nothing to do
        // v:mxx        - CSS units for input elements
        // v:.c2        - Class for input elements
        // v:m:t        - ratio of inputs over space t.
        $apply = $this -> showState[$scope];
        switch (count($values)) {
            case 1:
                // No specification, nothing to do
                break;
            case 2:
                if ($values[1][0] == '.') {
                    // Single class specification
                    $apply['input-wrapper'] = [
                        'class' => [substr($values[1], 1)],
                    ];
                } else {
                    // Single CSS units
                    $apply['input-wrapper'] = [
                        'style' => [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => $values[1],
                        ],
                    ];
                }
                break;
            default:
                if (preg_match('/^[+\-]?[0-9](\.[0-9]*)?$/', $values[1])) {
                    // ratio
                    $part1 = (float) $values[1];
                    if (!$part1) {
                        throw new \RuntimeException(
                            'Zero is invalid in a ratio.'
                        );
                    }
                    $sum = isset($values[2]) ? $values[2] : $part1;
                    $apply['input-wrapper'] = [
                        'style' => [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part1 / $sum, 3) . '%'
                        ],
                    ];
                }
                break;
        }
    }

}

