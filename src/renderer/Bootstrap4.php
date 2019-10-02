<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Element\ButtonElement;
use Abivia\NextForm\Element\CellElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Element\SectionElement;
use Abivia\NextForm\Element\StaticElement;

/**
 * Renderer for Bootstrap4
 */
class Bootstrap4 extends CommonHtml implements RendererInterface
{

    static protected $buttonSizeClasses = ['large' => ' btn-lg', 'regular' => '', 'small' => ' btn-sm'];

    public function __construct($options = [])
    {
        parent::__construct($options);
        $this -> initialize();
        $this -> setOptions($options);
    }

    /**
     * Generate a simple input element.
     * @param FieldElement $element
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkInput(FieldElement $element, Attributes $attrs)
    {
        // This is a single-valued element
        $attrs -> set('id', $element -> getId());
        $attrs -> setIfNotNull('value', $element -> getValue());
        if (
            $element -> getValue() === $element -> getDefault()
            && $element -> getValue()  !== null
        ) {
            $attrs -> setFlag('checked');
        }
        return Block::fromString($this -> writeTag('input', $attrs) . "\n");
    }

    /**
     * Generate check/radio HTML inputs from an element's data list.
     * @param FieldElement $element The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs Parent element attributes.
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkList(FieldElement $element, Attributes $attrs)
    {
        $baseId = $element -> getId();
        $type = $element -> getDataProperty() -> getPresentation() -> getType();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        $appearance = $this -> showGet('check', 'appearance');
        $checkLayout = $this -> showGet('check', 'layout');
        $groupClass = 'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '');
        $labelAttrs = new Attributes;
        $labelAttrs -> set('class', 'form-check-label');
        $block = new Block;
        foreach ($element -> getList(true) as $optId => $radio) {
            $optAttrs = $attrs -> copy();
            $id = $baseId . '-opt' . $optId;
            $optAttrs -> set('id', $id);
            $value = $radio -> getValue();
            $optAttrs -> setFlag('disabled', !$radio -> getEnabled());
            $optAttrs -> set('value', $value);
            if (
                $type == 'checkbox'
                && is_array($select) && in_array($value, $select)
            ) {
                $checked = true;
            } elseif ($value === $select) {
                $checked = true;
            } else {
                $checked = false;
            }
            $optAttrs -> setFlag('checked', $checked);
            $optAttrs -> setIfNotNull('*data-sidecar', $radio -> sidecar);

            $block -> merge(
                $this -> writeElement(
                    'div', ['attrs' => new Attributes('class', $groupClass)]
                )
            );
            $optAttrs -> set('class', 'form-check-input');
            if ($appearance === 'no-label') {
                $optAttrs -> set('aria-label', $radio -> getLabel());
            }
            $block -> body .= $this -> writeTag('input', $optAttrs) . "\n";
            if ($appearance !== 'no-label') {
                $labelAttrs -> set('!for', $id);
                $block -> body .= $this -> writeLabel(
                    '', $radio -> getLabel(), 'label',
                    $labelAttrs, ['break' => true]
                )
                ;
            }
            $block -> close();
        }
        return $block;
    }

    /**
     * Generate check/radio HTML inputs as buttons from an element's data list.
     * @param FieldElement $element The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs Parent element attributes.
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkListButtons(FieldElement $element, Attributes $attrs)
    {
        $baseId = $element -> getId();
        $type = $element -> getDataProperty() -> getPresentation() -> getType();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        // We know the appearance is going to be button or toggle
        //$appearance = $this -> showGet('check', 'appearance');
        //$checkLayout = $this -> showGet('check', 'layout');
        $labelAttrs = new Attributes;
        $block = new Block;
        foreach ($element -> getList(true) as $optId => $radio) {
            $optAttrs = $attrs -> copy();
            $id = $baseId . '-opt' . $optId;
            $optAttrs -> set('id', $id);
            $value = $radio -> getValue();
            $optAttrs -> set('value', $value);
            $optAttrs -> setIfNotNull('*data-sidecar', $radio -> sidecar);
            if (
                $type == 'checkbox'
                && is_array($select) && in_array($value, $select)
            ) {
                $checked = true;
            } elseif ($value === $select) {
                $checked = true;
            } else {
                $checked = false;
            }
            if ($checked) {
                $optAttrs -> setFlag('checked');
            }
            $show = $radio -> getShow();
            if ($show) {
                $this -> pushContext();
                $this -> setShow($show, 'radio');
            }
            $buttonClass = $this -> getButtonClass('radio');
            $labelAttrs -> set('class', $buttonClass . ($checked ? ' active' : ''));
            $block -> merge($this -> writeElement('label', ['attrs' => $labelAttrs]));
            $block -> body .= $this -> writeTag('input', $optAttrs) . "\n";
            $block -> body .= $radio -> getLabel();
            $block -> close();
            if ($show) {
                $this -> popContext();
            }
        }
        return $block;
    }

    /**
     * Generate a single check box/radio input.
     * @param FieldElement $element The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
     * @param \Abivia\NextForm\Renderer\Attributes $groupAttrs
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkSingle(
        FieldElement $element,
        Attributes $attrs,
        Attributes $groupAttrs
    ) {
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $appearance = $this -> showGet('check', 'appearance');
        $block = $this -> writeElement('div', ['attrs' => $groupAttrs]);
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $baseId . '-formhelp');
        }
        $attrs -> set('class', 'form-check-input');
        if ($appearance === 'no-label') {
            $attrs -> setIfNotNull('aria-label', $labels -> inner);
            $block -> merge($this -> checkInput($element, $attrs));
        } else {
            $block -> merge($this -> checkInput($element, $attrs));
            $labelAttrs = new Attributes;
            $labelAttrs -> set('!for', $baseId);
            $labelAttrs -> set('class', 'form-check-label');
            $block -> body .= $this -> writeLabel(
                'inner', $labels -> inner,
                'label', $labelAttrs, ['break' => true]
            );
        }
        $block -> close();
        return $block;
    }

    /**
     * Render a single-valued checkbox as a button
     * @param FieldElement $element The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
     * @param \Abivia\NextForm\Renderer\Attributes $groupAttrs
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkSingleButton(
        FieldElement $element,
        Attributes $attrs,
        Attributes $groupAttrs
    ) {
        $baseId = $element -> getId();
        $attrs -> set('id', $baseId);
        $labels = $element -> getLabels(true);
        $block = $this -> writeElement('div', ['attrs' => $groupAttrs]);
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $baseId . '-formhelp');
        }
        $labelAttrs = new Attributes;
        $buttonClass = $this -> getButtonClass('radio');
        $checked = $element -> getValue() === $element -> getDefault()
            && $element -> getValue() !== null;
        $labelAttrs -> set('class', $buttonClass . ($checked ? ' active' : ''));
        $block -> merge($this -> writeElement('label', ['attrs' => $labelAttrs]));
        $block -> body .= $this -> writeTag('input', $attrs) . "\n";
        $block -> body .= $labels -> inner;
        $block -> close();
        return $block;
    }

    /**
     * Use current show settings to build the button class
     * @return string
     */
    protected function getButtonClass($scope = 'button') : string
    {
        $buttonClass = 'btn btn'
            . ($this -> showGet($scope, 'fill') === 'outline' ? '-outline' : '')
            . '-' . $this -> showGet($scope, 'purpose')
            . self::$buttonSizeClasses[$this -> showGet($scope, 'size')];
        return $buttonClass;
    }

    /**
     * Set up to start generating output.
     */
    protected function initialize()
    {
        parent::initialize();
        // Reset the context
        $this -> context = [
            'inCell' => false
        ];
        // Initialize custom settings
        $this -> setShow('layout:vertical');
        $this -> setShow('purpose:primary');
    }

    /**
     * Write a "standard" input element; if there are before/after labels, generate a group.
     * @param Labels $labels
     * @param Attributes $attrs
     * @return \Abivia\NextForm\Renderer\Block
     */
    protected function inputGroup(Labels $labels, Attributes $attrs)
    {
        // Generate the actual input element, with labels if provided.
        if ($labels -> has('before') || $labels -> has('after')) {
            // We have before/after elements to attach, we need to create an input group
            $input = $this -> writeElement(
                'div', ['attrs' => new Attributes('class', 'input-group'), 'show' => 'inputWrapperAttributes']
            );

            if ($labels -> has('before')) {
                // Write a prepend group for the before label
                $group = $this -> writeElement(
                    'div', ['attrs' => new Attributes('class', ['input-group-prepend'])]
                );
                // Write the before label in the prepend group
                $group -> body .= $this -> writeLabel(
                    'inputBefore', $labels -> before, 'span',
                    new Attributes('class', ['input-group-text'])
                ) . "\n";
                $group -> close();
                $input -> merge($group);
            }

            // Generate the input element
            $input -> body .= $this -> writeTag('input', $attrs) . "\n";

            if ($labels -> has('after')) {
                // Write an append group for the after label
                $group = $this -> writeElement(
                    'div', ['attrs' => new Attributes('class', ['input-group-append'])]
                );
                // Write the after label in the append group
                $group -> body .= $this -> writeLabel(
                    'inputAfter', $labels -> after, 'span',
                    new Attributes('class', ['input-group-text'])
                ) . "\n";
                $group -> close();
                $input -> merge($group);
            }

            // If there's help text we need to generate a break.
            if ($labels -> has('help')) {
                $input -> body .= '<span class="w-100"></span>' . "\n";
            }
        } else {
            // Generate an input wrapper if we need to
            $input = $this -> writeElement(
                'div', ['show' => 'inputWrapperAttributes']
            );

            // Generate the input element
            $input -> body .= $this -> writeTag('input', $attrs) . "\n";
        }
        return $input;
    }

    /**
     * Write a button element.
     * @param ButtonElement $element
     * @param type $options
     * @return \Abivia\NextForm\Renderer\Block
     */
    protected function renderButtonElement(ButtonElement $element, $options = [])
    {
        $labels = $element -> getLabels(true);
        if ($options['access'] === 'hide') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $block = $this -> elementHidden($element, $labels -> inner);
            return $block;
        }
        $show = $element -> getShow();
        if ($show) {
            $this -> pushContext();
            $this -> setShow($show, 'button');
        }

        // Build attributes for the input
        $attrs = new Attributes;
        $attrs -> set('id', $element -> getId());
        if ($options['access'] == 'view' || !$element -> getEnabled()) {
            $attrs -> setFlag('disabled');
        }
        $attrs -> set('name', $element -> getFormName());
        $attrs -> setIfNotNull('value', $labels -> inner);

        $attrs -> set('class', $this -> getButtonClass());

        // We can see or change the data. Create a form group.
        $block = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );

        // Write the header.
        $block -> body .= $this -> writeLabel(
                'headingAttributes', $labels -> heading, 'label',
                new Attributes('!for', $element -> getId()), ['break' => true]
            );

        $attrs -> set('type', $element -> getFunction());
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $attrs -> get('id') . '-formhelp');
        }

        // Generate the input wrapper, if required for a horizontal layout.
        $input = $this -> writeElement('div', ['show' => 'inputWrapperAttributes']);

        // Add in the input element and before/after labels
        $input -> body .= $this -> writeLabel('beforespan', $labels -> before, 'span')
            . $this -> writeTag('input', $attrs)
            . $this -> writeLabel('after', $labels -> after, 'span', [])
            . "\n";
        if ($labels -> has('help')) {
            $helpAttrs = new Attributes;
            $helpAttrs -> set('id', $attrs -> get('aria-describedby'));
            $helpAttrs -> set('class', 'form-text text-muted');
            $input -> body .= $this -> writeLabel(
                'help', $labels -> help, 'small',
                $helpAttrs, ['break' => true]
            );
        }
        $block -> merge($input);
        $block -> close();

        // Restore show context and done.
        if ($show) {
            $this -> popContext();
        }
        return $block;
    }

    protected function renderCellElement(CellElement $element, $options = [])
    {
        $block = $this -> writeElement('div', ['show' => 'cellElementAttributes', 'force' => true]);
        $block -> onCloseDone = [$this, 'popContext'];
        $this -> pushContext();
        $this -> context['inCell'] = true;
        $this -> showDoLayout('form', 'inline');
        return $block;
    }

    protected function renderFieldCheckbox(FieldElement $element, $options = []) {
        //  appearance = default|button|toggle (can't be multiple)|no-label
        //  layout = inline|vertical
        //  form.layout = horizontal|vertical|inline

        // Generate hidden elements and return.
        if ($options['access'] === 'hide') {
            $block = $this -> elementHiddenList($element);
            return $block;
        }

        // Push and update the show context
        $show = $element -> getShow();
        if ($show !== '') {
            $this -> pushContext();
            $this -> setShow($show, 'check');
        }

        if (empty($element -> getList(true))) {
            $block = $this -> renderFieldCheckboxSingle($element, $options);
        } else {
            $block = $this -> renderFieldCheckboxMultiple($element, $options);
        }

        // Restore show context and return.
        if ($show !== '') {
            $this -> popContext();
        }

        return $block;
    }

    protected function renderFieldCheckboxMultiple(FieldElement $element, $options = [])
    {
        $appearance = $this -> showGet('check', 'appearance');
        $layout = $this -> showGet('form', 'layout');
        $attrs = new Attributes;
        $block = new Block();
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();

        // Set up basic attributes for the input element
        $attrs -> set('type', $type);
        $attrs -> set('name', $element -> getFormName()
            . ($type == 'checkbox' ? '[]' : ''));
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);

        if ($options['access'] == 'view') {
            $attrs -> setFlag('readonly');
        }

        // If this is showing as a row of buttons change the group attributes
        $groupAttrs = new Attributes;
        if ($appearance === 'toggle') {
            $asButtons = true;
            $groupAttrs -> set('class', 'btn-group btn-group-toggle');
            $groupAttrs -> set('data-toggle', 'buttons');

            // For buttons, write before/after labels on the same line
            $labelElement = 'span';
        } else {
            $checkLayout = $this -> showGet('check', 'layout');
            // Non-buttons can be stacked (default) or inline
            $asButtons = false;
            $groupAttrs -> set(
                'class',
                'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '')
            );
            $labelElement = 'div';
        }

        // Customize the header to align baselines in horizontal layouts
        $headerAttrs = new Attributes;
        if ($layout === 'vertical') {
            $rowBlock = $this -> writeElement(
                'fieldset', [
                    'attrs' => $this -> groupAttributes($element),
                    'show' => 'formGroupAttributes'
                ]
            );
            $headerElement = 'div';
        } else {
            // Horizontal layouts has a fieldset with just the form group class
            $rowAttrs = new Attributes('class', 'form-group');
            $rowAttrs -> merge($this -> groupAttributes($element));
            $rowBlock = $this -> writeElement(
                'fieldset', ['attrs' => $rowAttrs]
            );
            // Horizontal layouts have another div for the row
            $rowBlock  -> merge($this -> writeElement(
                'div', ['attrs' => new Attributes('class', 'row')])
            );
            $headerElement = 'legend';
            if (!$asButtons && $options['access'] == 'write') {
                $headerAttrs -> set('class', 'pt-0');
            }
        }

        // Write the heading. We added a pt-0 for horizontal layouts
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, $headerElement, $headerAttrs, ['break' => true]
        );

        if ($layout === 'horizontal') {
            // Create the second column for a horizontal layout
            $block -> merge($this -> writeElement('div', ['show' => 'inputWrapperAttributes']));
        }

        // Generate everything associated with the inputs, including before/after texts
        $input = new Block;
        $input -> body .= $this -> writeLabel(
            'before' . $labelElement, $labels -> before, $labelElement
        );
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $baseId . '-formhelp');
        }
        if ($asButtons) {
            $input -> merge($this -> writeElement('div', ['attrs' => $groupAttrs]));
            $input -> merge($this -> checkListButtons($element, clone $attrs));
        } else {
            $input -> merge($this -> checkList($element, clone $attrs));
        }
        $input -> close();

        // Write any after-label
        $input -> body .= $this -> writeLabel(
            'after', $labels -> after, $labelElement, [], ['break' => true]
        );

        $block -> merge($input);

        if ($labels -> has('help')) {
            $helpAttrs = new Attributes;
            $helpAttrs -> set('id', $attrs -> get('aria-describedby'));
            $helpAttrs -> set('class', 'form-text text-muted');
            $block -> body .= $this -> writeLabel(
                'help', $labels -> help, 'small',
                $helpAttrs, ['break' => true]
            );
        }
        $block -> close();
        $rowBlock -> merge($block);
        $rowBlock -> close();
        return $rowBlock;
    }

    protected function renderFieldCheckboxSingle(FieldElement $element, $options = [])
    {
        $appearance = $this -> showGet('check', 'appearance');
        $checkLayout = $this -> showGet('check', 'layout');
        $attrs = new Attributes;
        $block = new Block();
        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();

        // Set up basic attributes for the input element
        $attrs -> set('type', $type);
        $attrs -> set('name', $element -> getFormName());
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);

        // Generate hidden elements and return.
        if ($options['access'] === 'hide') {
            $attrs -> set('type', 'hidden');
            $block -> merge($this -> checkInput($element, $attrs));
            return $block;
        }
        if ($options['access'] == 'view') {
            $attrs -> setFlag('readonly');
        }

        // If this is showing as a row of buttons change the group attributes
        $groupAttrs = new Attributes;
        if ($appearance === 'toggle') {
            $asButtons = true;
            $groupAttrs -> set('class', 'btn-group btn-group-toggle');
            $groupAttrs -> set('data-toggle', 'buttons');
        } else {
            // Non-buttons can be stacked (default) or inline
            $asButtons = false;
            $groupAttrs -> set(
                'class',
                'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '')
            );
        }

        // Customize the header to align baselines in horizontal layouts
        $headerAttrs = new Attributes;
        $rowBlock = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );
        if ($this -> showGet('form', 'layout') !== 'vertical') {
            if (!$asButtons && $options['access'] == 'write') {
                $headerAttrs -> set('class', 'pt-0');
            }
        }

        // Write the heading. We added a pt-0 for horizontal non-button layouts
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'div', $headerAttrs, ['break' => true]
        );
        if ($this -> showGet('form', 'layout') === 'horizontal') {
            // Create the second column for a horizontal layout
            $block -> merge($this -> writeElement('div', ['show' => 'inputWrapperAttributes']));
        }

        // Generate everything associated with the inputs, including before/after texts
        $input = new Block;
        $input -> body .= $this -> writeLabel(
            'beforespan', $labels -> before, 'span'
        );
        if ($asButtons) {
            $input -> merge($this -> checkSingleButton($element, $attrs, $groupAttrs));
        } else {
            $input -> merge($this -> checkSingle($element, $attrs, $groupAttrs));
        }
        $input -> body .= $this -> writeLabel(
            'after', $labels -> after, 'span', null, ['break' => true]
        );
        $input -> close();
        $block -> merge($input);

        // Write any help text
        if ($labels -> has('help')) {
            $helpAttrs = new Attributes;
            $helpAttrs -> set('id', $attrs -> get('aria-describedby'));
            $helpAttrs -> set('class', 'form-text text-muted');
            $block -> body .= $this -> writeLabel(
                'help', $labels -> help, 'small',
                $helpAttrs, ['break' => true]
            );
        }
        $rowBlock -> merge($block);
        $rowBlock -> close();

        return $rowBlock;
    }

    protected function renderFieldCommon(FieldElement $element, $options = [])
    {
        $confirm = $options['confirm'];
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        if ($options['access'] === 'hide' || $type === 'hidden') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            $block = new Block();
            if (!$confirm) {
                $block = $this -> elementHidden($element, $element -> getValue());
            }
            return $block;
        }

        // Push and update the show context
        $show = $element -> getShow();
        if ($show !== '') {
            $this -> pushContext();
            $this -> setShow($show, $type);
        }

        // We can see or change the data. Create a form group.
        $block = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );

        // Get attributes for the input element
        $attrs = new Attributes;
        $attrs -> set('id', $element -> getId() . ($confirm ? '-confirmation' : ''));
        if ($options['access'] == 'view') {
            $attrs -> setFlag('readonly');
        }
        $attrs -> set('name', $element -> getFormName() . ($confirm ? '-confirmation' : ''));
        $attrs -> set('class', 'form-control');
        $value = $element -> getValue();
        $attrs -> setIfNotNull('value', $value);

        // Get any labels associated with this element
        $labels = $element -> getLabels(true);

        // Write the heading
        // If we're generating a confirmation and there's a confirm heading, use that
        // otherwise just use the usual heading
        $fieldHeading = $confirm && $labels -> confirm != '' ? $labels -> confirm : $labels -> heading;
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $fieldHeading, 'label',
            new Attributes('!for', $attrs -> get('id')), ['break' => true]
        );

        // If there's an inner label, use it as a placeholder
        $attrs -> setIfNotNull('placeholder', $labels -> inner);
        if ($type === 'range' && $options['access'] === 'view') {
            $type = 'text';
        }
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $attrs -> get('id') . '-help');
        }
        if (in_array($type, ['button', 'reset', 'submit'])) {
            $attrs -> set('class', $this -> getButtonClass());
        }
        $attrs -> set('type', $type);
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);

        // Render the data list if there is one
        $block -> merge($this -> dataList($attrs, $element, $type, $options));

        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs -> addValidation($type, $data -> getValidation());
        }

        // Generate the actual input element, with labels if provided.
        $input = $this -> inputGroup($labels, $attrs);

        // Generate help text, if any
        if ($labels -> has('help')) {
            $helpAttrs = new Attributes;
            $helpAttrs -> set('id', $attrs -> get('aria-describedby'));
            $helpAttrs -> set('class', 'form-text text-muted');
            $input -> body .= $this -> writeTag('small', $helpAttrs, $labels -> help) . "\n";
        }
        //$input -> close();
        $block -> merge($input);
        $block -> close();

        // Restore show context and return.
        if ($show !== '') {
            $this -> popContext();
        }

        return $block;
    }

    protected function renderFieldFile(FieldElement $element, $options = [])
    {
        $attrs = new Attributes;
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $block = new Block();
        $attrs -> set('id', $element -> getId());
        if ($options['access'] == 'view') {
            $type = 'text';
        }
        $attrs -> set('name', $element -> getFormName());
        $attrs -> set('class', 'form-control-file');
        $value = $element -> getValue();
        if ($options['access'] === 'hide') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $block -> merge($this -> elementHidden($element, $value));
            return $block;
        }

        // Push and update the show context
        $show = $element -> getShow();
        if ($show !== '') {
            $this -> pushContext();
            $this -> setShow($show, 'file');
        }

        // We can see or change the data
        //
        $attrs -> setIfNotNull('value', is_array($value) ? implode(',', $value) : $value);
        $labels = $element -> getLabels(true);

        // Start the form group
        $block = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'label',
            new Attributes('!for', $element -> getId()), ['break' => true]
        );
        $attrs -> setIfNotNull('placeholder', $labels -> inner);
        $attrs -> set('type', $type);

        // Start the input group
        $block -> merge($this -> writeElement('div', ['show' => 'inputWrapperAttributes']));
        $block -> body .= $this -> writeLabel('beforespan', $labels -> before, 'span');
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);
        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs -> addValidation($type, $data -> getValidation());

            // If we allow multiple files, make the name an array
            if ($attrs -> has('=multiple')) {
                $attrs -> set('name', $element -> getFormName() . '[]');
            }
        } else {
            // View Access
            $attrs -> set('type', 'text');
            $attrs -> setFlag('readonly');
        }
        // Generate the input element
        $block -> body .= $this -> writeTag('input', $attrs)
            . $this -> writeLabel('after', $labels -> after, 'span') . "\n";
        $block -> close();

        // Restore show context and return.
        if ($show !== '') {
            $this -> popContext();
        }

        return $block;
    }

    protected function renderFieldSelect(FieldElement $element, $options = [])
    {
        $value = $element -> getValue();
        if ($options['access'] === 'hide') {

            // Hidden: generate one or more hidden input elements
            $block = $this -> elementHidden($element, $value);
            return $block;
        }

        // Push and update the show context
        $show = $element -> getShow();
        if ($show) {
            $this -> pushContext();
            $this -> setShow($show, 'select');
        }

        // This element is visible
        $block = new Block();

        // Create a form group.
        $block = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );

        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();

        // Link the label if we're not in view mode
        $multiple = $data -> getValidation() -> get('multiple');
        $fieldName = $element -> getFormName() . ($multiple ? '[]' : '');
        $headAttr = new Attributes;
        if ($options['access'] != 'view') {
            $headAttr -> set('for', $fieldName);
        }

        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'label', $headAttr, ['break' => true]
        );

        // Horizontal layouts generate a div for the input column
        $block -> merge($this -> writeElement('div', ['show' => 'inputWrapperAttributes']));

        // Text preceeding the select
        $block -> body .= $this -> writeLabel(
            'before', $labels -> before, 'div', null, ['break' => true]
        );
        if ($options['access'] == 'view') {
            // In view mode we just generate a list of currently selected values as text
            $block -> merge($this -> renderFieldSelectView($element));
        } else {
            // Generate an actual select!
            $attrs = new Attributes;
            $attrs -> set('name', $fieldName);

            $attrs -> set('id', $element -> getId());
            $attrs -> setIfNotNull('size', $data -> getPresentation() -> getRows());
            $attrs -> addValidation('select', $data -> getValidation());
            if ($this -> showGet('select', 'appearance') === 'custom') {
                $attrs -> set('class', 'custom-select');
            } else {
                $attrs -> set('class', 'form-control');
            }

            $select = $this -> writeElement('select', ['attrs' => $attrs]);

            // Add the options
            // If there's no value set, see if there's a default
            if ($value === null) {
                $value = $element -> getDefault();
            }
            if (!is_array($value)) {
                $value = [$value];
            }
            $select -> merge(
                $this -> renderFieldSelectOptions($element -> getList(true), $value)
            );
            $select -> close();
            $block -> merge($select);
        }
        $block -> body .= $this -> writeLabel(
            'after', $labels -> after, 'div', null, ['break' => true]
        );
        $block -> close();

        // Restore show context and return.
        if ($show) {
            $this -> popContext();
        }

        return $block;
    }

    protected function renderFieldSelectOption($option, $value)
    {
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

    protected function renderFieldSelectView($element)
    {
        $baseId = $element -> getId();
        $data = $element -> getDataProperty();
        $multiple = $data -> getValidation() -> get('multiple');

        $attrs = new Attributes;
        $attrs -> set('name', $element -> getFormName() . ($multiple ? '[]' : ''));

        $list = $element -> getFlatList(true);
        // render as hidden with text
        $attrs -> set('type', 'hidden');

        $value = $element -> getValue();
        $block = new Block;
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
        return $block;
    }

    protected function renderFieldTextarea(FieldElement $element, $options = [])
    {

        // Get the type. We also use the data and presentation below.
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $value = $element -> getValue();
        if ($options['access'] === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            $block = $this -> elementHidden($element, $value);
            return $block;
        }

        // Push and update the show context
        $show = $element -> getShow();
        if ($show !== '') {
            $this -> pushContext();
            $this -> setShow($show, 'textarea');
        }

        // We can see or change the data. Create a form group.
        $block = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );

        // Assemble the textarea attributes
        $attrs = new Attributes;
        $attrs -> set('id', $element -> getId());
        $attrs -> set('name', $element -> getFormName());
        if ($options['access'] == 'view') {
            $attrs -> setFlag('readonly');
        }

        // Get any labels associated with this element
        $labels = $element -> getLabels(true);

        // Write the heading
        $block -> body .= $this -> writeLabel(
            'headingAttributes', $labels -> heading, 'label',
            new Attributes('!for', $attrs -> get('id')), ['break' => true]
        );

        // Placeholder label and any size specifiers
        $attrs -> setIfNotNull('placeholder', $labels -> inner);
        $attrs -> setIfNotNull('cols', $presentation -> getCols());
        $attrs -> setIfNotNull('rows', $presentation -> getRows());

        // Link to help if available
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $attrs -> get('id') . '-help');
        }

        // Sidecar data
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);

        // Write access: Add in any validation
        if ($options['access'] === 'write') {
            $attrs -> addValidation($type, $data -> getValidation());
        }

        // Generate the input wrapper
        $input = $this -> writeElement('div', ['show' => 'inputWrapperAttributes']);

        $input -> body .= $this -> writeLabel(
            'before', $labels -> before, 'div', null, ['break' => true]
        );
        if ($value === null) {
            $value = '';
        }
        // Generate the textarea element
        $input -> body .= $this -> writeTag('textarea', $attrs, $value)
            . $this -> writeLabel(
                'after', $labels -> after, 'div', null, ['break' => true]
            )
            . "\n";

        // It's a wrap.
        $block -> merge($input);
        $block -> close();

        // Restore show context and return.
        if ($show !== '') {
            $this -> popContext();
        }

        return $block;
    }

    protected function renderSectionElement(SectionElement $element, $options = [])
    {
        $labels = $element -> getLabels(true);
        $block = $this -> writeElement(
            'fieldset', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );
        if ($labels !== null) {
            $block -> body .= $this -> writeLabel(
                '', $labels -> heading, 'legend', null, ['break' => true]
            );
        }

        return $block;
    }

    protected function renderStaticElement(StaticElement $element, $options = [])
    {
        // There's no way to hide this element so if all we have is hidden access, skip it.
        if ($options['access'] === 'hide') {
            return new Block;
        }

        // Push and update the show context
        $show = $element -> getShow();
        if ($show !== '') {
            $this -> pushContext();
            $this -> setShow($show, 'html');
        }

        // We can see or change the data. Create a form group.
        $block = $this -> writeElement(
            'div', [
                'attrs' => $this -> groupAttributes($element),
                'show' => 'formGroupAttributes'
            ]
        );

        // Write a heading if there is one
        $labels = $element -> getLabels(true);
        $block -> body .= $this -> writeLabel(
            'headingAttributes',
            $labels ? $labels -> heading : null,
            'div', null, ['break' => true]
        );
        $block -> merge($this -> writeElement('div', ['show' => 'inputWrapperAttributes']));

        $attrs = new Attributes('id', $element -> getId());
        $block -> merge($this -> writeElement('div', ['attrs' => $attrs]));
        // Escape the value if it's not listed as HTML
        $value = $element -> getValue() . "\n";
        $block -> body .= $element -> getHtml() ? $value : htmlspecialchars($value);
        $block -> close();

        // Restore show context and return.
        if ($show !== '') {
            $this -> popContext();
        }

        return $block;
    }

    /**
     * Process layout options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoLayout($scope, $choice, $values = [])
    {
        //
        // Structure of the layout elements
        // formGroupAttributes - An Attributes object associated with the element acting as a form group
        // headingAttributes - Set in horizontal layouts to set heading widths
        // inputWrapperAttributes - Set in horizontal layouts for giving an input element width
        //
        if (!isset($this -> showState[$scope])) {
            $this -> showState[$scope] = [];
        }
        $this -> showState[$scope]['layout'] = $choice;
        if ($scope === 'form') {

            // Reset key settings
            unset($this -> showState['form']['inputWrapperAttributes']);
            unset($this -> showState['form']['headingAttributes']);

            // When before text is in a span, it has a right margin
            $this -> showState['form']['beforespan'] = new Attributes('class', ['mr-1']);

            // A cell element will appear as a row
            $this -> showState['form']['cellElementAttributes'] = new Attributes('class', ['form-row']);

            // Group wrapper encloses the complete output for a field, including labels
            $this -> showState['form']['formGroupAttributes'] = new Attributes('class', ['form-group']);
            if ($choice === 'horizontal') {
                $this -> showDoLayoutAnyHorizontal($scope, $values);
            } elseif ($choice === 'vertical') {
                $this -> showDoLayoutAnyVertical($scope, $values);
            }
        }
    }

    /**
     * Process horizontal layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    protected function showDoLayoutAnyHorizontal($scope, $values)
    {
        // possible values for arguments:
        // h            - We get to decide
        // h:nxx        - Ignored, we decide
        // h:nxx/mxx    - Ignored, we decide
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Ignored, we decide
        // h:.c1:.c2    - Class for headers / input elements
        //
        // Adjusts:
        // cellElementAttributes - Add the input column class
        // formGroupAttributes - add the row class
        //
        // Creates an attribute set for:
        // headingAttributes -- to be used for input element headings
        //
        $apply = &$this -> showState[$scope];
        $default = true;
        $apply['formGroupAttributes'] -> itemAppend('class', 'row');
        if (count($values) >= 3) {
            if ($values[1][0] == '.') {
                // Dual class specification
                $apply['headingAttributes'] = new Attributes(
                    'class', [substr($values[1], 1), 'col-form-label']
                );
                $col2 = substr($values[2], 1);
                $apply['inputWrapperAttributes'] = new Attributes(
                    'class', $col2
                );
                $apply['cellElementAttributes'] -> itemAppend('class', $col2);
                $default = false;
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
                $factor = 12.0 / $sum;
                $total = round($factor * ($part1 + $part2));
                // Ensure columns are nonzero
                $width1 = ((int) round($factor * $part1)) ?: 1;
                $col1 = 'col-sm-' . $width1;
                $col2 = 'col-sm-' . (int) ($total - $width1 > 0 ? $total - $width1 : 1);
                $apply['headingAttributes'] = new Attributes(
                    'class',[$col1, 'col-form-label']
                );
                $apply['inputWrapperAttributes'] = new Attributes(
                    'class', [$col2]
                );
                $apply['cellElementAttributes'] -> itemAppend('class', $col2);
                $default = false;
            }
        }
        if ($default) {
            $apply['headingAttributes'] = new Attributes('class', ['col-sm-2', 'col-form-label']);
            $apply['inputWrapperAttributes'] = new Attributes('class', ['col-sm-10']);
            $apply['cellElementAttributes'] -> itemAppend('class', 'col-sm-10');
        }
    }

    /**
     * Process vertical layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    protected function showDoLayoutAnyVertical($scope, $values)
    {
        // possible values for arguments:
        // v            - Default
        // v:.class
        // v:n          - Inputs use n columns in the 12 column grid
        // v:m:t        - ratio of inputs over space t, adjusted to the BS grid
        //
        // Adjusts:
        // cellElementAttributes - Add the input column class
        // formGroupAttributes - add the form width
        //
        $default = true;
        $apply = &$this -> showState[$scope];
        if (count($values) >= 2) {
            if ($values[1][0] == '.') {
                // class specification
                $col1 = substr($values[1], 1);
                $apply['formGroupAttributes'] -> itemAppend('class', $col1);
                $apply['cellElementAttributes'] -> itemAppend('class', $col1);
                $default = false;
            } elseif (preg_match('/^[+\-]?[0-9]+(\.[0-9]*)?$/', $values[1])) {
                // ratio
                $part1 = (float) $values[1];
                if (!$part1) {
                    throw new \RuntimeException(
                        'Zero is invalid for a ratio.'
                    );
                }
                $sum = isset($values[2]) ? $values[2] : 12;
                $factor = 12.0 / $sum;
                // Ensure columns are nonzero
                $col1 = 'col-sm-' . ((int) round($factor * $part1)) ?: 1;
                $apply['formGroupAttributes'] -> itemAppend('class', $col1);
                $apply['cellElementAttributes'] -> itemAppend('class', $col1);
                $default = false;
            }
        }
        if ($default) {
            $apply['formGroupAttributes'] -> itemAppend('class', 'col-sm-12');
            $apply['cellElementAttributes'] -> itemAppend('class', 'col-sm-12');
        }

    }

    /**
     * Process purpose options, called from showValidate()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $value Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoPurpose($scope, $choice, $value = [])
    {
        if (
            strpos(
                '|primary|secondary|success|danger|warning|info|light|dark|link',
                '|' . $choice
            ) === false
        ) {
            throw new RuntimeException($choice . ' is an invalid value for purpose.');
        }
        if (!isset($this -> showState[$scope])) {
            $this -> showState[$scope] = [];
        }
        $this -> showState[$scope]['purpose'] = $choice;
    }

    public function start($options = []) : Block {
        $pageData = parent::start($options);
        $pageData -> styleFiles[] = '<link rel="stylesheet"'
            . ' href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"'
            . ' integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"'
            . ' crossorigin="anonymous">';
        $pageData -> scriptFiles[] = '<script'
            . ' src="https://code.jquery.com/jquery-3.3.1.slim.min.js"'
            . ' integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"'
            . ' crossorigin="anonymous"></script>';
        $pageData -> scriptFiles[] = '<script'
            . ' src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"'
            . ' integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"'
            . ' crossorigin="anonymous"></script>';
        $pageData -> scriptFiles[] = '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"'
            . ' integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"'
            . ' crossorigin="anonymous"></script>';
        return $pageData;
    }

}

