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

    public function __construct($options = []) {
        parent::__construct($options);
        $this -> setOptions($options);
    }

    protected function initialize() {
        parent::initialize();
        $this -> setShow('purpose:primary');
    }

    protected function renderButtonElement(ButtonElement $element, $options = []) {
        $show = $element -> getShow();
        if ($show) {
            $this -> pushContext();
            $this -> setShow($show, 'button');
        }
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
            $attrs['class'] = $this -> mergeShow('button', ['button-class']);
            // Horizontal layouts need a wrapper
            $block = $this -> writeWrapper($block, 'div', 'group-wrapper');
            $labelAttrs = ['!for' => $element -> getId()];
            $block -> body .= $this -> writeLabel(
                    'heading', $labels -> heading, 'label', $labelAttrs, ['break' => true]
                );
            $input = $this -> writeWrapper(new Block, 'div', 'input-wrapper');
            $attrs['type'] = $element -> getFunction();
            if ($labels -> help !== null) {
                $attrs['aria-describedby'] = $attrs['id'] . '-formhelp';
            }
            $input -> body .= $this -> writeLabel('before', $labels -> before, 'span')
                . $this -> writeTag('input', $attrs)
                . $this -> writeLabel('after', $labels -> after, 'span', [])
                . "\n";
            if ($labels -> help !== null) {
                $attrs['aria-describedby'] = $attrs['id'] . '-formhelp';
                $input -> body .= $this -> writeLabel(
                    'help', $labels -> help, 'small',
                    [
                        'id' => $attrs['aria-describedby'],
                        'class' => 'form-text text-muted',
                    ],
                    ['break' => true]
                );
            }
            $input -> close();
            $block -> merge($input);
            //$block -> body .= "\n";
            $block -> close();
        }
        if ($show) {
            $this -> popContext();
        }
        return $block;
    }

    protected function renderCellElement(CellElement $element, $options = []) {
        $block = new Block();
        $block = $this -> writeWrapper($block, 'div', 'cell-wrapper', ['force' => true]);
        $block -> onCloseDone = [$this, 'popContext'];
        $this -> pushContext();
        $this -> context['inCell'] = true;
        $this -> showDoLayout('form', 'inline');
        return $block;
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
            if (!$confirm) {
                $block -> merge($this -> elementHidden($element, $value));
            }
        } else {
            //
            // We can see or change the data
            //
            $block = $this -> writeWrapper($block, 'div', 'group-wrapper');
            //$block -> body .= '<div class="form-group">' . "\n";
            $attrs['class'] = 'form-control';
            if ($value !== null) {
                $attrs['value'] = $value;
            }
            $labels = $element -> getLabels(true);
            $block -> body .= $this -> writeLabel(
                'heading',
                $confirm && $labels -> confirm != '' ? $labels -> confirm : $labels -> heading,
                'label', ['!for' => $attrs['id']], ['break' => true]
            );
            if ($labels -> inner !== null) {
                $attrs['placeholder'] = $labels -> inner;
            }
            if ($type === 'range' && $options['access'] === 'view') {
                $type = 'text';
            }
            if (($helpLabel = $labels -> help) !== null) {
                $attr['aria-describedby'] = $attr['id'] . '-help';
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
                . $this -> writeLabel('after', $labels -> after, 'span')
                . "\n";
            if ($helpLabel !== null) {
                $helpAttrs = ['id' => $attrs['aria-describedby']];
                $helpAttrs['class'] = 'form-text text-muted';
                $block -> body .= $this -> writeTag('small', $helpAttrs, $helpLabel) . "\n";
            }
            $block -> close();
            //$block -> body .= "</div>\n";
        }
        return $block;
    }

    /**
     * Process layout options, called from showValidate()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $value Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoLayout($scope, $choice, $value = []) {
        $apply = &$this -> custom[$scope];
        $apply['layout'] = $choice;
        $apply['group-wrapper'] = ['class' => ['form-group']];
        $apply['cell-wrapper'] = ['class' => ['form-row']];
        if ($choice === 'vertical') {
            unset($apply['input-wrapper']);
        }
        if ($choice !== 'horizontal') {
            return;
        }
        // possible values for arguments:
        // h            - We get to decide
        // h:nxx        - Ignored, we decide
        // h:nxx/mxx    - Ignored, we decide
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Ignored, we decide
        // h:.c1:.c2    - Class for headers / input elements
        $default = true;
        $apply['group-wrapper']['class'][] = 'row';
        if (count($value) >= 3) {
            if ($value[1][0] == '.') {
                // Dual class specification
                $apply['heading'] = [
                    'class' => [substr($value[1], 1), 'col-form-label'],
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
                $factor = 12.0 / $sum;
                $total = round($factor * ($part1 * $part2));
                // Ensure columns are nonzero
                $col1 = (int) round($factor * $part1) ?: 1;
                $col2 = (int) ($total - $col1 > 0 ? $total - $col1 : 1);
                $apply['heading'] = [
                    'class' => ['col-sm-' . $col1, 'col-form-label'],
                ];
                $apply['input-wrapper'] = [
                    'class' => ['col-sm-' . $col2],
                ];
            }
        }
        if ($default) {
            $apply['heading'] = [
                'class' => ['col-sm-2', 'col-form-label'],
            ];
            $apply['input-wrapper'] = [
                'class' => ['col-sm-10'],
            ];
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
                'primary|secondary|success|danger|warning|info|light|dark|link|',
                $choice . '|'
            ) === false
        ) {
            throw new RuntimeException($choice . ' is an invalid value for purpose.');
        }
        if (!isset($this -> custom[$scope])) {
            $this -> custom[$scope] = [];
        }
        $this -> custom[$scope]['purpose'] = $choice;
        $this -> custom[$scope]['button-class'] = 'btn btn-' . $choice;
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

