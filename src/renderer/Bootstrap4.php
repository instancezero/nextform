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
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Renderer for Bootstrap4
 */
class Bootstrap4 extends SimpleHtml implements Renderer {

    static protected $buttonSizeClasses = ['large' => ' btn-lg', 'regular' => '', 'small' => ' btn-sm'];

    public function __construct($options = []) {
        parent::__construct($options);
        $this -> setOptions($options);
    }

    protected function checkInput(Block $block, FieldElement $element, Attributes $attrs) {
        // This is a single-valued element
        $attrs -> set('id', $element -> getId());
        $attrs -> setIfNotNull('value', $element -> getValue());
        if (
            $element -> getValue() === $element -> getDefault()
            && $element -> getValue()  !== null
        ) {
            $attrs -> setFlag('checked');
        }
        $block -> body .= $this -> writeTag('input', $attrs) . "\n";
    }

    protected function checkList(Block $block, FieldElement $element, $list, $type, $visible, Attributes $attrs) {
        $needEmpty = !$visible;
        $baseId = $element -> getId();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        $appearance = $this -> showGet('check', 'appearance');
        $checkLayout = $this -> showGet('check', 'layout');
        $groupClass = 'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '');
        $labelAttrs = new Attributes;
        $labelAttrs -> set('class', 'form-check-label');
        foreach ($list as $optId => $radio) {
            $optAttrs = $attrs -> copy();
            $id = $baseId . '-opt' . $optId;
            $optAttrs -> set('id', $id);
            $value = $radio -> getValue();
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
            $optAttrs -> setIfNotNull('*data-sidecar', $radio -> sidecar);
            if ($visible) {
                if ($checked) {
                    $optAttrs -> setFlag('checked');
                }
                $block = $this -> writeWrapper(
                    $block, 'div', ['attrs' => new Attributes('class', $groupClass)]
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
            } elseif ($checked) {
                $block -> body .= $this -> writeTag('input', $optAttrs) . "\n";
                $needEmpty = false;
            }
        }
        if ($needEmpty) {
            $block -> body .= $this -> checkInput($block, $element, $attrs);
        }
    }

    protected function checkListButtons(Block $block, FieldElement $element, $list, $type, Attributes $attrs) {
        $baseId = $element -> getId();
        $select = $element -> getValue();
        if ($select === null) {
            $select = $element -> getDefault();
        }
        // We know the appearance is going to be button or toggle
        //$appearance = $this -> showGet('check', 'appearance');
        //$checkLayout = $this -> showGet('check', 'layout');
        $labelAttrs = new Attributes;
        foreach ($list as $optId => $radio) {
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
            $block = $this -> writeWrapper(
                $block, 'label', ['attrs' => $labelAttrs]
            );
            $block -> body .= $this -> writeTag('input', $optAttrs) . "\n";
            $block -> body .= $radio -> getLabel();
            $block -> close();
            if ($show) {
                $this -> popContext();
            }
        }
    }

    protected function checkSingle(Block $block, FieldElement $element, Attributes $attrs, Attributes $groupAttrs) {
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $appearance = $this -> showGet('check', 'appearance');
        $block = $this -> writeWrapper(
            $block, 'div', ['attrs' => $groupAttrs]
        );
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $baseId . '-formhelp');
        }
        $attrs -> set('class', 'form-check-input');
        if ($appearance === 'no-label') {
            $attrs -> setIfNotNull('aria-label', $labels -> inner);
            $this -> checkInput($block, $element, $attrs);
        } else {
            $this -> checkInput($block, $element, $attrs);
            $labelAttrs = new Attributes;
            $labelAttrs -> set('!for', $baseId);
            $labelAttrs -> set('class', 'form-check-label');
            $block -> body .= $this -> writeLabel(
                'inner', $labels -> inner,
                'label', $labelAttrs, ['break' => true]
            );
        }
        $block -> close();
    }

    /**
     * Render a single-valued checkbox as a button
     * @param \Abivia\NextForm\Renderer\Block $block
     * @param FieldElement $element
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
     * @param \Abivia\NextForm\Renderer\Attributes $groupAttrs
     */
    protected function checkSingleButton(
        Block $block, FieldElement $element, Attributes $attrs, Attributes $groupAttrs
    ) {
        $baseId = $element -> getId();
        $attrs -> set('id', $baseId);
        $labels = $element -> getLabels(true);
        $block = $this -> writeWrapper(
            $block, 'div', ['attrs' => $groupAttrs]
        );
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $baseId . '-formhelp');
        }
        $labelAttrs = new Attributes;
        $buttonClass = $this -> getButtonClass('radio');
        $checked = $element -> getValue() === $element -> getDefault()
            && $element -> getValue() !== null;
        $labelAttrs -> set('class', $buttonClass . ($checked ? ' active' : ''));
        $block = $this -> writeWrapper(
            $block, 'label', ['attrs' => $labelAttrs]
        );
        $block -> body .= $this -> writeTag('input', $attrs) . "\n";
        $block -> body .= $labels -> inner;
        $block -> close();
    }

    /**
     * Use current show settings to build the button class
     * @return string
     */
    protected function getButtonClass($scope = 'button') : string {
        $buttonClass = 'btn btn'
            . ($this -> showGet($scope, 'fill') === 'outline' ? '-outline' : '')
            . '-' . $this -> showGet($scope, 'purpose')
            . self::$buttonSizeClasses[$this -> showGet($scope, 'size')];
        return $buttonClass;
    }

    protected function initialize() {
        parent::initialize();
        $this -> setShow('purpose:primary');
    }

    protected function renderButtonElement(ButtonElement $element, $options = []) {
        $labels = $element -> getLabels(true);
        if ($options['access'] === 'read') {
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
        $attrs = new Attributes;
        $attrs -> set('id', $element -> getId());
        if ($options['access'] == 'view') {
            $attrs -> setFlag('disabled');
        }
        $attrs -> set('name', $element -> getFormName());
        $attrs -> setIfNotNull('value', $labels -> inner);

        $attrs -> set('class', $this -> getButtonClass());

        $block = $this -> writeWrapper(new Block, 'div', ['show' => 'input-wrapper']);
        $attrs -> set('type', $element -> getFunction());
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $attrs -> get('id') . '-formhelp');
        }
        $block -> body .= $this -> writeLabel('before', $labels -> before, 'span')
            . $this -> writeTag('input', $attrs)
            . $this -> writeLabel('after', $labels -> after, 'span', [])
            . "\n";
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

        // Write the header. Horizontal layouts need a wrapper
        $header = $this -> writeWrapper(new Block(), 'div', ['show' => 'group-wrapper']);
        $header -> body .= $this -> writeLabel(
                'heading', $labels -> heading, 'label',
                new Attributes('!for', $element -> getId()), ['break' => true]
            );
        $header -> merge($block);
        $block = $header -> close();

        // Restore show context and done.
        if ($show) {
            $this -> popContext();
        }
        return $block;
    }

    protected function renderCellElement(CellElement $element, $options = []) {
        $block = new Block();
        $block = $this -> writeWrapper($block, 'div', ['show' => 'cell-wrapper', 'force' => true]);
        $block -> onCloseDone = [$this, 'popContext'];
        $this -> pushContext();
        $this -> context['inCell'] = true;
        $this -> showDoLayout('form', 'inline');
        return $block;
    }

    protected function renderFieldCheckbox(FieldElement $element, $options = []) {
        //  appearance = default|button|toggle (can't be multiple)|no-label
        //  layout = inline|vertical
        $show = $element -> getShow();
        if ($show) {
            $this -> pushContext();
            $this -> setShow($show, 'check');
        }
        $appearance = $this -> showGet('check', 'appearance');
        $checkLayout = $this -> showGet('check', 'layout');
        $attrs = new Attributes;
        $block = new Block();
        $baseId = $element -> getId();
        $labels = $element -> getLabels(true);
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        $attrs -> set('type', $type);
        $visible = true;
        if ($options['access'] == 'view') {
            $attrs -> setFlag('readonly');
        } elseif ($options['access'] === 'read') {
            $attrs -> set('type', 'hidden');
            $visible = false;
        }
        $attrs -> set('name', $element -> getFormName() . ($type == 'checkbox' ? '[]' : ''));
        $list = $element -> getList(true);
        $groupAttrs = new Attributes;
        if ($appearance === 'toggle') {
            $asButtons = true;
            $groupAttrs -> set('class', 'btn-group btn-group-toggle');
            $groupAttrs -> set('data-toggle', 'buttons');
        } else {
            $asButtons = false;
            $groupAttrs -> set(
                'class',
                'form-check' . ($checkLayout === 'inline' ? ' form-check-inline' : '')
            );
        }
        $sidecar = $data -> getPopulation() -> sidecar;
        $attrs -> setIfNotNull('*data-sidecar', $sidecar);
        if ($visible) {
            $block -> body .= $this -> writeLabel(
                'heading', $labels -> heading, 'div', null, ['break' => true]
            );

            // The "before" and "after" texts are in a div if we have multiple
            // choices, span otherwise.
            $bracketTag = empty($list) ? 'span' : 'div';
            $block -> body .= $this -> writeLabel(
                'before', $labels -> before, $bracketTag, null
            );
            if (empty($list)) {
                if ($asButtons) {
                    $this -> checkSingleButton($block, $element, $attrs, $groupAttrs);
                } else {
                    $this -> checkSingle($block, $element, $attrs, $groupAttrs);
                }
            } else {
                $block = $this -> writeWrapper($block, 'div', ['attrs' => $groupAttrs]);
                $listBlock = new block;
                if ($asButtons) {
                    $this -> checkListButtons($listBlock, $element, $list, $type, clone $attrs);
                } else {
                    $this -> checkList($listBlock, $element, $list, $type, $visible, clone $attrs);
                }
                $block -> merge($listBlock);
                $block -> close();
            }

            // Write any after-label
            $block -> body .= $this -> writeLabel(
                'after', $labels -> after, $bracketTag,
                [], ['break' => !empty($list)]
            );
            $block -> close();
            if ($labels -> has('help')) {
                $helpAttrs = new Attributes;
                $helpAttrs -> set('id', $attrs -> get('aria-describedby'));
                $helpAttrs -> set('class', 'form-text text-muted');
                $block -> body .= $this -> writeLabel(
                    'help', $labels -> help, 'small',
                    $helpAttrs, ['break' => true]
                );
            } elseif (!$asButtons) {
                $block -> body .= "\n";
            }
        } else {
            // Not visible, we're just writing hidden elements, no labels.
            if (empty($list)) {
                $this -> checkInput($block, $element, $attrs);
            } else {
                $this -> checkList($block, $element, $list, $type, $visible, clone $attrs);
            }

        }

        // Restore show context and done.
        if ($show) {
            $this -> popContext();
        }
        return $block;
    }

    protected function renderFieldCommon(FieldElement $element, $options = []) {
        $confirm = $options['confirm'];
        $data = $element -> getDataProperty();
        $presentation = $data -> getPresentation();
        $type = $presentation -> getType();
        if ($options['access'] === 'read' || $type === 'hidden') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            if ($confirm) {
                $block = new Block();
            } else {
                $block = $this -> elementHidden($element, $element -> getValue());
            }
            return $block;
        }
        //
        // We can see or change the data
        //
        $attrs = new Attributes;
        $block = new Block();
        $attrs -> set('id', $element -> getId() . ($confirm ? '-confirm' : ''));
        if ($options['access'] == 'view') {
            $attrs -> setFlag('readonly');
        }
        $attrs -> set('name', $element -> getFormName() . ($confirm ? '-confirm' : ''));
        $value = $element -> getValue();
        $block = $this -> writeWrapper($block, 'div', ['show' => 'group-wrapper']);

        $attrs -> set('class', 'form-control');
        $attrs -> setIfNotNull('value', $value);
        $labels = $element -> getLabels(true);
        $block -> body .= $this -> writeLabel(
            'heading',
            $confirm && $labels -> confirm != '' ? $labels -> confirm : $labels -> heading,
            'label', new Attributes('!for', $attrs -> get('id')), ['break' => true]
        );
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
        $labelAttrs = new Attributes;
        $wrapperAttrs = new Attributes;
        $hasGroup = ($labels -> has('before') || $labels -> has('after'));
        if ($hasGroup) {
            $labelAttrs -> set('class', 'input-group-text');
            $wrapperAttrs -> set('class', 'input-group');
        }
        // Generate an input group if
        $input = $this -> writeWrapper(
            new Block, 'div', ['show' => 'input-wrapper', 'attrs' => $wrapperAttrs]
        );
        $input -> body .= $this -> writeLabel(
            'before', $labels -> before, 'span',
            $labelAttrs, ['div' => 'input-group-prepend']
        );
        $attrs -> setIfNotNull('*data-sidecar', $data -> getPopulation() -> sidecar);
        // Render the data list if there is one
        $input -> merge($this -> dataList($attrs, $element, $type, $options));
        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs -> addValidation( $type, $data -> getValidation());
        }
        // Generate the input element
        $input -> body .= $this -> writeTag('input', $attrs)
            . ($hasGroup ? "\n" : '')
            . $this -> writeLabel(
                'after', $labels -> after, 'span', $labelAttrs,
                ['div' => 'input-group-append']
            )
            . ($hasGroup ? '' : "\n")
            ;
        if ($labels -> has('help')) {
            if ($hasGroup) {
                $input -> body .= '<span class="w-100"></span>' . "\n";
            }
            $helpAttrs = new Attributes;
            $helpAttrs -> set('id', $attrs -> get('aria-describedby'));
            $helpAttrs -> set('class', 'form-text text-muted');
            $input -> body .= $this -> writeTag('small', $helpAttrs, $labels -> help) . "\n";
        }
        $input -> close();
        $block -> merge($input);
        $block -> close();

        return $block;
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
        $this -> showState[$scope]['layout'] = $choice;
        if ($scope === 'form') {
            unset($this -> showState['input-wrapper']);
            $this -> showState[$scope]['cell-wrapper'] = new Attributes('class', ['form-row']);
            $this -> showState[$scope]['group-wrapper'] = new Attributes('class', ['form-group']);
            if ($choice === 'horizontal') {
                $this -> showDoLayoutAnyHorizontal($scope, $values);
            } elseif ($choice === 'vertical') {
            }
        }
    }

    /**
     * Process horizontal layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    protected function showDoLayoutAnyHorizontal($scope, $values) {
        $apply = &$this -> showState[$scope];
        // possible values for arguments:
        // h            - We get to decide
        // h:nxx        - Ignored, we decide
        // h:nxx/mxx    - Ignored, we decide
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Ignored, we decide
        // h:.c1:.c2    - Class for headers / input elements
        $default = true;
        $apply['group-wrapper'] -> itemAppend('class', 'row');
        if (count($values) >= 3) {
            if ($values[1][0] == '.') {
                // Dual class specification
                $apply['heading'] = new Attributes(
                    'class', [substr($values[1], 1), 'col-form-label']
                );
                $apply['input-wrapper'] = new Attributes(
                    'class', substr($values[2], 1)
                );
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
                $col1 = ((int) round($factor * $part1)) ?: 1;
                $col2 = (int) ($total - $col1 > 0 ? $total - $col1 : 1);
                $apply['heading'] = new Attributes(
                    'class',['col-sm-' . $col1, 'col-form-label']
                );
                $apply['input-wrapper'] = new Attributes(
                    'class', ['col-sm-' . $col2]
                );
            }
        }
        if ($default) {
            $apply['heading'] = new Attributes('class', ['col-sm-2', 'col-form-label']);
            $apply['input-wrapper'] = new Attributes('class', ['col-sm-10']);
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
        // v            - Default
        // v:.class
        // v:m:t        - ratio of inputs over space t, adjusted to the BS grid
        $default = true;
        $apply = &$this -> showState[$scope];
        $apply['group-wrapper']['class'][] = 'row';
        if (count($values) >= 2) {
            if ($values[1][0] == '.') {
                // class specification
                $apply['input-wrapper'] = new Attributes('class', [substr($values[1], 1)]);
            } elseif (preg_match('/^[+\-]?[0-9](\.[0-9]*)?$/', $values[1])) {
                // ratio
                $part1 = (float) $values[1];
                if (!$part1) {
                    throw new \RuntimeException(
                        'Zero is invalid for a ratio.'
                    );
                }
                $sum = isset($values[2]) ? $values[2] : $part1;
                $factor = 12.0 / $sum;
                // Ensure columns are nonzero
                $col1 = ((int) round($factor * $part1)) ?: 1;
                $apply['input-wrapper'] = new Attributes('class', ['col-sm-' . $col1]);
            }
        }
        if ($default) {
            $apply['input-wrapper'] = new Attributes('class', ['col-sm-12']);
        }
    }

    /**
     * Process purpose options, called from showValidate()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $value Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoPurpose($scope, $choice, $value = []) {
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

    public function start($options = []) {
        $pageData = parent::start($options);
        $pageData -> head .= '<link rel="stylesheet"'
            . ' href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"'
            . ' integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"'
            . ' crossorigin="anonymous">';
        $pageData -> scripts[] = '<script'
            . ' src="https://code.jquery.com/jquery-3.3.1.slim.min.js"'
            . ' integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"'
            . ' crossorigin="anonymous"></script>';
        $pageData -> scripts[] = '<script'
            . ' src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"'
            . ' integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"'
            . ' crossorigin="anonymous"></script>';
        $pageData -> scripts[] = '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"'
            . ' integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"'
            . ' crossorigin="anonymous"></script>';
        return $pageData;
    }

}

