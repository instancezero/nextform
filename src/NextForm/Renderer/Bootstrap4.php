<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\ContainerBinding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Binding\SimpleBinding;

/**
 * Renderer for Bootstrap4
 */
class Bootstrap4 extends CommonHtml implements RendererInterface
{

    static protected $buttonSizeClasses = ['large' => ' btn-lg', 'regular' => '', 'small' => ' btn-sm'];

    public function __construct($options = [])
    {
        parent::__construct($options);
        $this->initialize();
        $this->setOptions($options);
    }

    /**
     * Generate a simple input element for a single-valued checkbox.
     * @param FieldBinding $binding
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkInput(FieldBinding $binding, Attributes $attrs)
    {
        // This is a single-valued element
        $attrs->set('id', $binding->getId());
        $attrs->setIfNotNull('value', $binding->getValue());
        if (
            $binding->getValue() === $binding->getElement()->getDefault()
            && $binding->getValue()  !== null
        ) {
            $attrs->setFlag('checked');
        }
        return Block::fromString($this->writeTag('input', $attrs) . "\n");
    }

    /**
     * Generate check/radio HTML inputs from an element's data list.
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs Parent element attributes.
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkList(FieldBinding $binding, Attributes $attrs)
    {
        $baseId = $binding->getId();
        $type = $binding->getDataProperty()->getPresentation()->getType();
        $select = $binding->getValue();
        if ($select === null) {
            $select = $binding->getElement()->getDefault();
        }
        $appearance = $this->showGet('check', 'appearance');
        $checkLayout = $this->showGet('check', 'layout');
        $groupClass = 'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '');
        $labelAttrs = new Attributes();
        $labelAttrs->set('class', 'form-check-label');
        $block = new Block();
        foreach ($binding->getList(true) as $optId => $radio) {
            $optAttrs = $attrs->copy();
            $id = $baseId . '_opt' . $optId;
            $optAttrs->set('id', $id);
            $value = $radio->getValue();
            $optAttrs->setFlag('disabled', !$radio->getEnabled());
            $optAttrs->set('value', $value);
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
            $optAttrs->setFlag('checked', $checked);
            $optAttrs->setIfNotNull('data-nf-name', $radio->getName());
            $optAttrs->setIfNotEmpty('*data-nf-group', $radio->getGroups());
            $optAttrs->setIfNotNull('*data-nf-sidecar', $radio->sidecar);

            $block->merge(
                $this->writeElement(
                    'div', ['attributes' => new Attributes('class', $groupClass)]
                )
            );
            $optAttrs->set('class', 'form-check-input');
            if ($appearance === 'no-label') {
                $optAttrs->set('aria-label', $radio->getLabel());
            }
            $block->body .= $this->writeTag('input', $optAttrs) . "\n";
            if ($appearance !== 'no-label') {
                $labelAttrs->set('!for', $id);
                $block->body .= $this->writeLabel(
                    '', $radio->getLabel(), 'label',
                    $labelAttrs, ['break' => true]
                )
                ;
            }
            $block->close();
        }
        return $block;
    }

    /**
     * Generate check/radio HTML inputs as buttons from an element's data list.
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs Parent element attributes.
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkListButtons(FieldBinding $binding, Attributes $attrs)
    {
        $baseId = $binding->getId();
        $type = $binding->getDataProperty()->getPresentation()->getType();
        $select = $binding->getValue();
        if ($select === null) {
            $select = $binding->getElement()->getDefault();
        }
        // We know the appearance is going to be button or toggle
        //$appearance = $this->showGet('check', 'appearance');
        //$checkLayout = $this->showGet('check', 'layout');
        $labelAttrs = new Attributes();
        $block = new Block();
        foreach ($binding->getList(true) as $optId => $radio) {
            $optAttrs = $attrs->copy();
            $id = $baseId . '_opt' . $optId;
            $optAttrs->set('id', $id);
            $value = $radio->getValue();
            $optAttrs->set('value', $value);
            $optAttrs->setIfNotNull('data-nf-name', $radio->getName());
            $optAttrs->setIfNotEmpty('*data-nf-group', $radio->getGroups());
            $optAttrs->setIfNotNull('*data-nf-sidecar', $radio->sidecar);
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
                $optAttrs->setFlag('checked');
            }
            $show = $radio->getShow();
            if ($show) {
                $this->pushContext();
                $this->setShow($show, 'radio');
            }
            $buttonClass = $this->getButtonClass('radio');
            $labelAttrs->set('class', $buttonClass . ($checked ? ' active' : ''));
            $block->merge($this->writeElement('label', ['attributes' => $labelAttrs]));
            $block->body .= $this->writeTag('input', $optAttrs) . "\n";
            $block->body .= $radio->getLabel();
            $block->close();
            if ($show) {
                $this->popContext();
            }
        }
        return $block;
    }

    /**
     * Generate a single check box/radio input.
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
     * @param \Abivia\NextForm\Renderer\Attributes $groupAttrs
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkSingle(
        FieldBinding $binding,
        Attributes $attrs,
        Attributes $groupAttrs
    ) {
        $baseId = $binding->getId();
        $labels = $binding->getLabels(true);
        $appearance = $this->showGet('check', 'appearance');
        $block = $this->writeElement('div', ['attributes' => $groupAttrs]);
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $baseId . '_formhelp');
        }
        $attrs->set('class', 'form-check-input');
        if ($appearance === 'no-label') {
            $attrs->setIfNotNull('aria-label', $labels->inner);
            $block->merge($this->checkInput($binding, $attrs));
        } else {
            $block->merge($this->checkInput($binding, $attrs));
            $labelAttrs = new Attributes();
            $labelAttrs->set('!for', $baseId);
            $labelAttrs->set('class', 'form-check-label');
            $block->body .= $this->writeLabel(
                'inner', $labels->inner,
                'label', $labelAttrs, ['break' => true]
            );
        }
        $block->close();
        return $block;
    }

    /**
     * Render a single-valued checkbox as a button
     * @param FieldBinding $binding The element we're generating for.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
     * @param \Abivia\NextForm\Renderer\Attributes $groupAttrs
     * @return \Abivia\NextForm\Renderer\Block $block The output block.
     */
    protected function checkSingleButton(
        FieldBinding $binding,
        Attributes $attrs,
        Attributes $groupAttrs
    ) {
        $baseId = $binding->getId();
        $attrs->set('id', $baseId);
        $labels = $binding->getLabels(true);
        $block = $this->writeElement('div', ['attributes' => $groupAttrs]);
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $baseId . '_formhelp');
        }
        $labelAttrs = new Attributes();
        $buttonClass = $this->getButtonClass('radio');
        $checked = $binding->getValue() === $binding->getElement()->getDefault()
            && $binding->getValue() !== null;
        $labelAttrs->set('class', $buttonClass . ($checked ? ' active' : ''));
        $block->merge($this->writeElement('label', ['attributes' => $labelAttrs]));
        $block->body .= $this->writeTag('input', $attrs) . "\n";
        $block->body .= $labels->inner;
        $block->close();

        return $block;
    }

    /**
     * Use current show settings to build the button class
     * @return string
     */
    public function getButtonClass($scope = 'button') : string
    {
        $buttonClass = 'btn btn'
            . ($this->showGet($scope, 'fill') === 'outline' ? '-outline' : '')
            . '-' . $this->showGet($scope, 'purpose')
            . self::$buttonSizeClasses[$this->showGet($scope, 'size')];
        return $buttonClass;
    }

    /**
     * Set up to start generating output.
     */
    protected function initialize()
    {
        parent::initialize();
        // Reset the context
        $this->context = [
            'inCell' => false
        ];
        // Initialize custom settings
        $this->setShow('layout:vertical');
        $this->setShow('purpose:primary');
    }

    /**
     * Write a "standard" input element; if there are before/after labels, generate a group.
     * @param Labels $labels
     * @param Attributes $attrs
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function inputGroup(Labels $labels, Attributes $attrs)
    {
        // Generate the actual input element, with labels if provided.
        if ($labels->has('before') || $labels->has('after')) {
            // We have before/after elements to attach, we need to create an input group
            $input = $this->writeElement(
                'div', ['attributes' => new Attributes('class', 'input-group'), 'show' => 'inputWrapperAttributes']
            );

            if ($labels->has('before')) {
                // Write a prepend group for the before label
                $group = $this->writeElement(
                    'div', ['attributes' => new Attributes('class', ['input-group-prepend'])]
                );
                // Write the before label in the prepend group
                $group->body .= $this->writeLabel(
                    'inputBefore', $labels->before, 'span',
                    new Attributes('class', ['input-group-text'])
                ) . "\n";
                $group->close();
                $input->merge($group);
            }

            // Generate the input element
            $input->body .= $this->writeTag('input', $attrs) . "\n";

            if ($labels->has('after')) {
                // Write an append group for the after label
                $group = $this->writeElement(
                    'div', ['attributes' => new Attributes('class', ['input-group-append'])]
                );
                // Write the after label in the append group
                $group->body .= $this->writeLabel(
                    'inputAfter', $labels->after, 'span',
                    new Attributes('class', ['input-group-text'])
                ) . "\n";
                $group->close();
                $input->merge($group);
            }

            // If there's help text we need to generate a break.
            if ($labels->has('help')) {
                $input->body .= '<span class="w-100"></span>' . "\n";
            }
        } else {
            // Generate an input wrapper if we need to
            $input = $this->writeElement(
                'div', ['show' => 'inputWrapperAttributes']
            );

            // Generate the input element
            $input->body .= $this->writeTag('input', $attrs) . "\n";
        }
        return $input;
    }

    protected function renderFieldCheckbox(FieldBinding $binding, $options = []) {
        //  appearance = default|button|toggle (can't be multiple)|no-label
        //  layout = inline|vertical
        //  form.layout = horizontal|vertical|inline

        // Generate hidden elements and return.
        if ($options['access'] === 'hide') {
            $block = $this->elementHiddenList($binding);
            return $block;
        }

        // Push and update the show context
        $show = $binding->getElement()->getShow();
        if ($show !== '') {
            $this->pushContext();
            $this->setShow($show, 'check');
        }

        if (empty($binding->getList(true))) {
            $block = $this->renderFieldCheckboxSingle($binding, $options);
        } else {
            $block = $this->renderFieldCheckboxMultiple($binding, $options);
        }

        // Restore show context and return.
        if ($show !== '') {
            $this->popContext();
        }

        return $block;
    }

    protected function renderFieldCheckboxMultiple(FieldBinding $binding, $options = [])
    {
        $appearance = $this->showGet('check', 'appearance');
        $layout = $this->showGet('form', 'layout');
        $attrs = new Attributes();
        $block = new Block();
        $baseId = $binding->getId();
        $labels = $binding->getLabels(true);
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();

        // Set up basic attributes for the input element
        $attrs->set('type', $type);
        $attrs->set('name', $binding->getFormName());
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);

        if ($options['access'] == 'view') {
            $attrs->setFlag('readonly');
        }

        // If this is showing as a row of buttons change the group attributes
        $groupAttrs = new Attributes();
        if ($appearance === 'toggle') {
            $asButtons = true;
            $groupAttrs->set('class', 'btn-group btn-group-toggle');
            $groupAttrs->set('data-toggle', 'buttons');

            // For buttons, write before/after labels on the same line
            $labelElement = 'span';
        } else {
            $checkLayout = $this->showGet('check', 'layout');
            // Non-buttons can be stacked (default) or inline
            $asButtons = false;
            $groupAttrs->set(
                'class',
                'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '')
            );
            $labelElement = 'div';
        }

        // Customize the header to align baselines in horizontal layouts
        $headerAttrs = new Attributes();
        if ($layout === 'vertical') {
            $rowBlock = $this->writeElement(
                'fieldset', [
                    'attributes' => $this->groupAttributes($binding),
                    'show' => 'formGroupAttributes'
                ]
            );
            $headerElement = 'div';
        } else {
            // Horizontal layouts has a fieldset with just the form group class
            $rowAttrs = new Attributes('class', 'form-group');
            $rowAttrs->merge($this->groupAttributes($binding));
            $rowBlock = $this->writeElement(
                'fieldset', ['attributes' => $rowAttrs]
            );
            // Horizontal layouts have another div for the row
            $rowBlock->merge($this->writeElement(
                'div', ['attributes' => new Attributes('class', 'row')])
            );
            $headerElement = 'legend';
            if (!$asButtons && $options['access'] == 'write') {
                $headerAttrs->set('class', 'pt-0');
            }
        }

        // Write the heading. We added a pt-0 for horizontal layouts
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, $headerElement, $headerAttrs, ['break' => true]
        );

        if ($layout === 'horizontal') {
            // Create the second column for a horizontal layout
            $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));
        }

        // Generate everything associated with the inputs, including before/after texts
        $input = new Block();
        $input->body .= $this->writeLabel(
            'before' . $labelElement, $labels->before, $labelElement
        );
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $baseId . '_formhelp');
        }
        if ($asButtons) {
            $input->merge($this->writeElement('div', ['attributes' => $groupAttrs]));
            $input->merge($this->checkListButtons($binding, clone $attrs));
        } else {
            $input->merge($this->checkList($binding, clone $attrs));
        }
        $input->close();

        // Write any after-label
        $input->body .= $this->writeLabel(
            'after', $labels->after, $labelElement, [], ['break' => true]
        );

        $block->merge($input);

        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $attrs->get('aria-describedby'));
            $helpAttrs->set('class', 'form-text text-muted');
            $block->body .= $this->writeLabel(
                'help', $labels->help, 'small',
                $helpAttrs, ['break' => true]
            );
        }
        $block->close();
        $rowBlock->merge($block);
        $rowBlock->close();
        return $rowBlock;
    }

    protected function renderFieldCheckboxSingle(FieldBinding $binding, $options = [])
    {
        $appearance = $this->showGet('check', 'appearance');
        $checkLayout = $this->showGet('check', 'layout');
        $attrs = new Attributes();
        $block = new Block();
        $labels = $binding->getLabels(true);
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();

        // Set up basic attributes for the input element
        $attrs->set('type', $type);
        $attrs->set('name', $binding->getFormName());
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);

        // Generate hidden elements and return.
        if ($options['access'] === 'hide') {
            $attrs->set('type', 'hidden');
            $block->merge($this->checkInput($binding, $attrs));
            return $block;
        }
        if ($options['access'] == 'view') {
            $attrs->setFlag('readonly');
        }

        // If this is showing as a row of buttons change the group attributes
        $groupAttrs = new Attributes();
        if ($appearance === 'toggle') {
            $asButtons = true;
            $groupAttrs->set('class', 'btn-group btn-group-toggle');
            $groupAttrs->set('data-toggle', 'buttons');
        } else {
            // Non-buttons can be stacked (default) or inline
            $asButtons = false;
            $groupAttrs->set(
                'class',
                'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '')
            );
        }

        // Customize the header to align baselines in horizontal layouts
        $headerAttrs = new Attributes();
        $rowBlock = $this->writeElement(
            'div', [
                'attributes' => $this->groupAttributes($binding),
                'show' => 'formGroupAttributes'
            ]
        );
        if ($this->showGet('form', 'layout') !== 'vertical') {
            if (!$asButtons && $options['access'] == 'write') {
                $headerAttrs->set('class', 'pt-0');
            }
        }

        // Write the heading. We added a pt-0 for horizontal non-button layouts
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'div', $headerAttrs, ['break' => true]
        );
        if ($this->showGet('form', 'layout') === 'horizontal') {
            // Create the second column for a horizontal layout
            $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));
        }

        // Generate everything associated with the inputs, including before/after texts
        $input = new Block();
        $input->body .= $this->writeLabel(
            'beforespan', $labels->before, 'span'
        );
        if ($asButtons) {
            $input->merge($this->checkSingleButton($binding, $attrs, $groupAttrs));
        } else {
            $input->merge($this->checkSingle($binding, $attrs, $groupAttrs));
        }
        $input->body .= $this->writeLabel(
            'after', $labels->after, 'span', null, ['break' => true]
        );
        $input->close();
        $block->merge($input);

        // Write any help text
        if ($labels->has('help')) {
            $helpAttrs = new Attributes();
            $helpAttrs->set('id', $attrs->get('aria-describedby'));
            $helpAttrs->set('class', 'form-text text-muted');
            $block->body .= $this->writeLabel(
                'help', $labels->help, 'small',
                $helpAttrs, ['break' => true]
            );
        }
        $rowBlock->merge($block);
        $rowBlock->close();

        return $rowBlock;
    }

    protected function renderFieldFile(FieldBinding $binding, $options = [])
    {
        $attrs = new Attributes();
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();
        $block = new Block();
        $attrs->set('id', $binding->getId());
        if ($options['access'] == 'view') {
            $type = 'text';
        }
        $attrs->set('name', $binding->getFormName());
        $attrs->set('class', 'form-control-file');
        $value = $binding->getValue();
        if ($options['access'] === 'hide') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $block->merge($this->elementHidden($binding, $value));
            return $block;
        }

        // Push and update the show context
        $show = $binding->getElement()->getShow();
        if ($show !== '') {
            $this->pushContext();
            $this->setShow($show, 'file');
        }

        // We can see or change the data
        //
        $attrs->setIfNotNull('value', is_array($value) ? implode(',', $value) : $value);
        $labels = $binding->getLabels(true);

        // Start the form group
        $block = $this->writeElement(
            'div', [
                'attributes' => $this->groupAttributes($binding),
                'show' => 'formGroupAttributes'
            ]
        );
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'label',
            new Attributes('!for', $binding->getId()), ['break' => true]
        );
        $attrs->setIfNotNull('placeholder', $labels->inner);
        $attrs->set('type', $type);

        // Start the input group
        $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));
        $block->body .= $this->writeLabel('beforespan', $labels->before, 'span');
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);
        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs->addValidation($type, $data->getValidation());

            $attrs->set('name', $binding->getFormName());
        } else {
            // View Access
            $attrs->set('type', 'text');
            $attrs->setFlag('readonly');
        }
        // Generate the input element
        $block->body .= $this->writeTag('input', $attrs)
            . $this->writeLabel('after', $labels->after, 'span') . "\n";
        $block->close();

        // Restore show context and return.
        if ($show !== '') {
            $this->popContext();
        }

        return $block;
    }

    protected function renderFieldSelect(FieldBinding $binding, $options = [])
    {
        $value = $binding->getValue();
        if ($options['access'] === 'hide') {

            // Hidden: generate one or more hidden input elements
            $block = $this->elementHidden($binding, $value);
            return $block;
        }

        // Push and update the show context
        $element = $binding->getElement();
        $show = $element->getShow();
        if ($show) {
            $this->pushContext();
            $this->setShow($show, 'select');
        }

        // This element is displayed
        $block = new Block();

        // Create a form group.
        $block = $this->writeElement(
            'div', [
                'attributes' => $this->groupAttributes($binding),
                'show' => 'formGroupAttributes'
            ]
        );

        $labels = $binding->getLabels(true);
        $data = $binding->getDataProperty();

        // Link the label if we're not in view mode
        $fieldName = $binding->getFormName();
        $headAttr = new Attributes();
        if ($options['access'] != 'view') {
            $headAttr->set('for', $fieldName);
        }

        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'label', $headAttr, ['break' => true]
        );

        // Horizontal layouts generate a div for the input column
        $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));

        // Text preceeding the select
        $block->body .= $this->writeLabel(
            'before', $labels->before, 'div', null, ['break' => true]
        );
        if ($options['access'] == 'view') {
            // In view mode we just generate a list of currently selected values as text
            $block->merge($this->renderFieldSelectView($binding));
        } else {
            // Generate an actual select!
            $attrs = new Attributes();
            $attrs->set('name', $fieldName);

            $attrs->set('id', $binding->getId());
            $attrs->setIfNotNull('size', $data->getPresentation()->getRows());
            $attrs->addValidation('select', $data->getValidation());
            if ($this->showGet('select', 'appearance') === 'custom') {
                $attrs->set('class', 'custom-select');
            } else {
                $attrs->set('class', 'form-control');
            }

            $select = $this->writeElement('select', ['attributes' => $attrs]);

            // Add the options
            // If there's no value set, see if there's a default
            if ($value === null) {
                $value = $element->getDefault();
            }
            if (!is_array($value)) {
                $value = [$value];
            }
            $select->merge(
                $this->renderFieldSelectOptions($binding->getList(true), $value)
            );
            $select->close();
            $block->merge($select);
        }
        $block->body .= $this->writeLabel(
            'after', $labels->after, 'div', null, ['break' => true]
        );
        $block->close();

        // Restore show context and return.
        if ($show) {
            $this->popContext();
        }

        return $block;
    }

    protected function renderFieldSelectView($binding)
    {
        $baseId = $binding->getId();
        $data = $binding->getDataProperty();
        $multiple = $data->getValidation()->get('multiple');

        $attrs = new Attributes();
        $attrs->set('name', $binding->getFormName());

        $list = $binding->getFlatList(true);
        // render as hidden with text
        $attrs->set('type', 'hidden');

        $value = $binding->getValue();
        $block = new Block();
        if ($multiple) {
            // step through each possible value, output matches
            if (!is_array($value)) {
                $value = [$value];
            }
            $optId = 0;
            foreach ($list as $option) {
                $slot = array_search($option->getValue(), $value);
                if ($slot !== false) {
                    $id = $baseId . '_opt' . $optId;
                    $attrs->set('id', $id);
                    $attrs->set('value', $value[$slot]);
                    $block->body .= $this->writeTag('input', $attrs) . "\n";
                    $block->body .= $this->writeTag('span', [], $option->getLabel())
                        . "<br/>\n";
                    ++$optId;
                }
            }
        } else {
            $attrs->set('id', $baseId);
            $attrs->set('value', $value);
            $block->body .= $this->writeTag('input', $attrs) . "\n";
            foreach ($list as $option) {
                if ($value == $option->getValue()) {
                    $block->body .= $this->writeTag('span')
                        . $option->getLabel() . '</span>'
                        . "\n";
                }
            }
        }
        return $block;
    }

    protected function renderFieldTextarea(FieldBinding $binding, $options = [])
    {

        // Get the type. We also use the data and presentation below.
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();
        $value = $binding->getValue();
        if ($options['access'] === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            $block = $this->elementHidden($binding, $value);
            return $block;
        }

        // Push and update the show context
        $show = $binding->getElement()->getShow();
        if ($show !== '') {
            $this->pushContext();
            $this->setShow($show, 'textarea');
        }

        // We can see or change the data. Create a form group.
        $block = $this->writeElement(
            'div', [
                'attributes' => $this->groupAttributes($binding),
                'show' => 'formGroupAttributes'
            ]
        );

        // Assemble the textarea attributes
        $attrs = new Attributes();
        $attrs->set('id', $binding->getId());
        $attrs->set('name', $binding->getFormName());
        if ($options['access'] == 'view') {
            $attrs->setFlag('readonly');
        }

        // Get any labels associated with this element
        $labels = $binding->getLabels(true);

        // Write the heading
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'label',
            new Attributes('!for', $attrs->get('id')), ['break' => true]
        );

        // Placeholder label and any size specifiers
        $attrs->setIfNotNull('placeholder', $labels->inner);
        $attrs->setIfNotNull('cols', $presentation->getCols());
        $attrs->setIfNotNull('rows', $presentation->getRows());

        // Link to help if available
        if ($labels->has('help')) {
            $attrs->set('aria-describedby', $attrs->get('id') . '_help');
        }

        // Sidecar data
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);

        // Write access: Add in any validation
        if ($options['access'] === 'write') {
            $attrs->addValidation($type, $data->getValidation());
        }

        // Generate the input wrapper
        $input = $this->writeElement('div', ['show' => 'inputWrapperAttributes']);

        $input->body .= $this->writeLabel(
            'before', $labels->before, 'div', null, ['break' => true]
        );
        if ($value === null) {
            $value = '';
        }
        // Generate the textarea element
        $input->body .= $this->writeTag('textarea', $attrs, $value)
            . $this->writeLabel(
                'after', $labels->after, 'div', null, ['break' => true]
            )
            . "\n";

        // It's a wrap.
        $block->merge($input);
        $block->close();

        // Restore show context and return.
        if ($show !== '') {
            $this->popContext();
        }

        return $block;
    }

    protected function renderSectionElement(ContainerBinding $binding, $options = [])
    {
        $labels = $binding->getLabels(true);
        $block = $this->writeElement(
            'fieldset', [
                'attributes' => $this->groupAttributes($binding),
                'show' => 'formGroupAttributes'
            ]
        );
        if ($labels !== null) {
            $block->body .= $this->writeLabel(
                '', $labels->heading, 'legend', null, ['break' => true]
            );
        }

        return $block;
    }

    protected function renderStaticElement(SimpleBinding $binding, $options = [])
    {
        // There's no way to hide this element so if all we have is hidden access, skip it.
        if ($options['access'] === 'hide') {
            return new Block();
        }

        // Push and update the show context
        $element = $binding->getElement();
        $show = $element->getShow();
        if ($show !== '') {
            $this->pushContext();
            $this->setShow($show, 'html');
        }

        // We can see or change the data. Create a form group.
        $block = $this->writeElement(
            'div', [
                'attributes' => $this->groupAttributes($binding),
                'show' => 'formGroupAttributes'
            ]
        );

        // Write a heading if there is one
        $labels = $binding->getLabels(true);
        $block->body .= $this->writeLabel(
            'headingAttributes',
            $labels ? $labels->heading : null,
            'div', null, ['break' => true]
        );
        $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));

        $attrs = new Attributes('id', $binding->getId());
        $block->merge($this->writeElement('div', ['attributes' => $attrs]));
        // Escape the value if it's not listed as HTML
        $value = $binding->getValue() . "\n";
        $block->body .= $element->getHtml() ? $value : htmlspecialchars($value);
        $block->close();

        // Restore show context and return.
        if ($show !== '') {
            $this->popContext();
        }

        return $block;
    }

    protected function renderTriggers(FieldBinding $binding) : Block
    {
        $result = new Block;
        $triggers = $binding->getElement()->getTriggers();
        if (empty($triggers)) {
            return $result;
        }
        $formId = $binding->getManager()->getId();
        $script = "$('#" . $formId . " [name^=\"" . $binding->getFormName(true)
            . "\"]').change(function () {\n";
        foreach ($triggers as $trigger) {
            if ($trigger->getEvent() !== 'change') {
                continue;
            }
            $value = $trigger->getValue();
            $closing = "  }\n";
            if (is_array($value)) {
                $script .= "  if (" . json_encode($value) . ".includes(this.value)) {\n";
            } elseif ($value === null) {
                // Null implies no conditions.
                $closing = '';
            } else {
                $script .= "  if (this.value === " . json_encode($value) . ") {\n";
            }
            foreach ($trigger->getActions() as $action) {
                $script .= $this->renderAction($formId, $binding, $action);
            }
            $script .= $closing;
        }
        $script .= "});\n";
        $result->script = $script;

        return $result;
    }

    protected function renderAction($formId, $binding, $action)
    {
        $script = '';
        switch ($action->getSubject()) {
            case 'checked':
                $value = $action->getValue();
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif ($value === 'checked') {
                    $value = 'true';
                } elseif ($value === 'unchecked') {
                    $value = 'false';
                }
                foreach ($action->getTarget() as $target) {
                    if (preg_match('/{(.*)}/', $target, $match)) {
                        $target = $match[1];
                        $script .= "    " . $formId . ".checkGroup("
                            . "'" . $target . "', " . $value . ");\n";
                    } elseif ($target[0] === '#') {
                        $script .= "    " . $formId . ".check("
                            . "$('" . $target . "'), " . $value . ");\n";
                    } elseif ($target[0] === '&') {
                        $script .= "    " . $formId . ".check("
                            . "$('#" . $formId . " [data-nf-name^=\"" . $target . "\"]'),"
                            . " " . $value . ");\n";
                    } else {
                        $script .= "    " . $formId . ".check("
                            . "$('" . $target . "'),"
                            . " " . $value . ");\n";
                    }
                }
                break;

            case 'display':
                $value = $action->getValue();
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif ($value === 'checked') {
                    $value = 'this.checked';
                } elseif ($value === 'unchecked') {
                    $value = '!this.checked';
                }
                foreach ($action->getTarget() as $target) {
                    if (preg_match('/{(.*)}/', $target, $match)) {
                        $target = $match[1];
                        $script .= "    " . $formId .
                            ".displayGroup('" . $target . "', " . $value . ");\n";
                    } elseif ($target[0] === '#') {
                        $script .= "    $('" . $target . "').toggle(" . $value . ");\n";
                    } elseif ($target[0] === '&') {
                        $script .= "    $('#" . $formId . " [data-nf-name^=\"" . $target . "\"]')"
                            . ".toggle(" . $value . ");\n";
                    } else {
                        $script .= "    " . $formId
                            . ".displayContainer('" . $target . "',"
                            . " " . $value . ");\n";
                    }
                }
                break;

            case 'enable':
                $value = $action->getValue() ? 'false' : 'true';
                foreach ($action->getTarget() as $target) {
                    if (preg_match('/{(.*)}/', $target, $match)) {
                        $target = $match[1];
                        $script .= "    " . $formId .
                            ".disableGroup('" . $target . "', "
                            . $value . ");\n";
                    } elseif ($target[0] === '#') {
                        $script .= "    $('" . $target . "').prop('disabled', "
                            . $value . ");\n";
                    } elseif ($target[0] === '&') {
                        $script .= "    $('#" . $formId . " [data-nf-name^=\"" . $target . "\"]')"
                            . ".prop('disabled', " . $value . ");\n";
                    } else {
                        $script .= "    " . $formId
                            . ".disableContainer('" . $target . "',"
                            . " " . $value . ");\n";
                    }
                }
                break;

            case 'script':
                $script .= "  " . $action->getValue() . "\n";
                break;
        }
        return $script;
    }

    /**
     * Process cell spacing options, called from show().
     *
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    public function showDoCellspacing($scope, $choice, $values = [])
    {
        // Expecting choice to be "a" or "b".
        // For "a", one or more space delimited single digits from 0 to 5,
        // optionally prefixed with rr-
        //
        // For "b" one or more space-delimited sets of [rr-]xx-n where ss is a
        // renderer selector (bs for Bootstrap), xx is a size specifier,
        // and n is 0 to 5.
        //
        // Specifiers other than bs are ignored the result is a list of
        // classes to be used when spacing between the second and subsequent
        // elements in a cell.

        $classList = [];
        if ($choice == 'a') {
            foreach ($values as $value) {
                \preg_match(
                    '/(?<prefix>[a-z][a-z0-9]-)?(?<size>sm|md|lg|xl)\-(?<weight>[0-5])/',
                    $value, $match
                );
                if ($match['prefix'] !== '' && $match['prefix'] !== 'bs-') {
                    continue;
                }
                $classList[] = 'ml-' . $match['size'] . '-' . $match['weight'];
            }
        } else {
            foreach ($values as $value) {
                \preg_match(
                    '/(?<prefix>[a-z][a-z0-9]-)?(?<weight>[0-5])/',
                    $value, $match
                );
                if ($match['prefix'] !== '' && $match['prefix'] !== 'bs-') {
                    continue;
                }
                $classList[] = 'ml-' . $match['weight'];
            }
        }
        if (!empty($classList)) {
            $this->showState[$scope]['cellspacing']
                = new Attributes('class', $classList);
        }
    }

    /**
     * Process layout options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    public function showDoLayout($scope, $choice, $values = [])
    {
        //
        // Structure of the layout elements
        // formGroupAttributes - An Attributes object associated with the element acting as a form group
        // headingAttributes - Set in horizontal layouts to set heading widths
        // inputWrapperAttributes - Set in horizontal layouts for giving an input element width
        //
        if (!isset($this->showState[$scope])) {
            $this->showState[$scope] = [];
        }
        $this->showState[$scope]['layout'] = $choice;
        if ($scope === 'form') {

            // Reset key settings
            unset($this->showState['form']['inputWrapperAttributes']);
            unset($this->showState['form']['headingAttributes']);

            // When before text is in a span, it has a right margin
            $this->showState['form']['beforespan'] = new Attributes('class', ['mr-1']);

            // A cell element will appear as a row
            $this->showState['form']['cellElementAttributes'] = new Attributes('class', ['form-row']);

            // Group wrapper encloses the complete output for a field, including labels
            $this->showState['form']['formGroupAttributes'] = new Attributes('class', ['form-group']);
            if ($choice === 'horizontal') {
                $this->showDoLayoutAnyHorizontal($scope, $values);
            } elseif ($choice === 'vertical') {
                $this->showDoLayoutAnyVertical($scope, $values);
            }
        }
    }

    /**
     * Process horizontal layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    public function showDoLayoutAnyHorizontal($scope, $values)
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
        $apply = &$this->showState[$scope];
        $default = true;
        $apply['formGroupAttributes']->itemAppend('class', 'row');
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
                $apply['cellElementAttributes']->itemAppend('class', $col2);
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
                $apply['cellElementAttributes']->itemAppend('class', $col2);
                $default = false;
            }
        }
        if ($default) {
            $apply['headingAttributes'] = new Attributes('class', ['col-sm-2', 'col-form-label']);
            $apply['inputWrapperAttributes'] = new Attributes('class', ['col-sm-10']);
            $apply['cellElementAttributes']->itemAppend('class', 'col-sm-10');
        }
    }

    /**
     * Process vertical layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    public function showDoLayoutAnyVertical($scope, $values)
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
        $apply = &$this->showState[$scope];
        if (count($values) >= 2) {
            if ($values[1][0] == '.') {
                // class specification
                $col1 = substr($values[1], 1);
                $apply['formGroupAttributes']->itemAppend('class', $col1);
                $apply['cellElementAttributes']->itemAppend('class', $col1);
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
                $apply['formGroupAttributes']->itemAppend('class', $col1);
                $apply['cellElementAttributes']->itemAppend('class', $col1);
                $default = false;
            }
        }
        if ($default) {
            $apply['formGroupAttributes']->itemAppend('class', 'col-sm-12');
            $apply['cellElementAttributes']->itemAppend('class', 'col-sm-12');
        }

    }

    /**
     * Process purpose options, called from showValidate()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $value Array of colon-delimited settings including the initial keyword.
     */
    public function showDoPurpose($scope, $choice, $value = [])
    {
        if (
            strpos(
                '|primary|secondary|success|danger|warning|info|light|dark|link',
                '|' . $choice
            ) === false
        ) {
            throw new RuntimeException($choice . ' is an invalid value for purpose.');
        }
        if (!isset($this->showState[$scope])) {
            $this->showState[$scope] = [];
        }
        $this->showState[$scope]['purpose'] = $choice;
    }

    /**
     * Start rendering a form.
     *
     * @param type $options The 'attributes' option isn't really optional.
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function start($options = []) : Block {
        $pageData = parent::start($options);
        $pageData->styleFiles[] = '<link rel="stylesheet"'
            . ' href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"'
            . ' integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"'
            . ' crossorigin="anonymous">';
        $pageData->scriptFiles[] = '<script'
            . ' src="https://code.jquery.com/jquery-3.3.1.slim.min.js"'
            . ' integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"'
            . ' crossorigin="anonymous"></script>';
        $pageData->scriptFiles[] = '<script'
            . ' src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"'
            . ' integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"'
            . ' crossorigin="anonymous"></script>';
        $pageData->scriptFiles[] = '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"'
            . ' integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"'
            . ' crossorigin="anonymous"></script>';
        if (isset($options['scriptpath'])) {
            $pageData->scriptFiles[] = '<script src="' . $options['scriptpath'] . '></script>';
        } else {
            $pageData->script .= file_get_contents(__DIR__ . '/js/nf-bootstrap4.js')
                . "\n";
        }
        $id = $options['attributes']->get('id');
        $pageData->script .= 'var ' . $id . " = new NextForm($('#" . $id . "'));\n";
        return $pageData;
    }

}
