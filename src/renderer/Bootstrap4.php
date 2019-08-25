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
        $block = new Block();
        $attrs -> set('id', $element -> getId());
        if ($options['access'] == 'view') {
            $attrs -> setFlag('disabled');
        }
        $attrs -> set('name', $element -> getFormName());
        $attrs -> setIfNotNull('value', $labels -> inner);
        $attrs -> set('class', $this -> mergeShow('button', ['button-class']));
        // Horizontal layouts need a wrapper
        $block = $this -> writeWrapper($block, 'div', 'group-wrapper');
        $block -> body .= $this -> writeLabel(
                'heading', $labels -> heading, 'label',
                new Attributes('!for', $element -> getId()), ['break' => true]
            );
        $input = $this -> writeWrapper(new Block, 'div', 'input-wrapper');
        $attrs -> set('type', $element -> getFunction());
        if ($labels -> has('help')) {
            $attrs -> set('aria-describedby', $attrs -> get('id') . '-formhelp');
        }
        $input -> body .= $this -> writeLabel('before', $labels -> before, 'span')
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
        $input -> close();
        $block -> merge($input);
        $block -> close();
        if ($show) {
            $this -> popContext();
        }
        return $block;
    }

    protected function renderCellElement(CellElement $element, $options = []) {
        $block = new Block();
        $block = $this -> writeWrapper($block, 'div', 'cell-wrapper', null, ['force' => true]);
        $block -> onCloseDone = [$this, 'popContext'];
        $this -> pushContext();
        $this -> context['inCell'] = true;
        $this -> showDoLayout('form', 'inline');
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
        $block = $this -> writeWrapper($block, 'div', 'group-wrapper');

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
        $attrs -> set('type', $type);
        $labelAttrs = new Attributes;
        $wrapperAttrs = new Attributes;
        $hasGroup = ($labels -> has('before') || $labels -> has('after'));
        if ($hasGroup) {
            $labelAttrs -> set('class', 'input-group-text');
            $wrapperAttrs -> set('class', 'input-group');
        }
        // Generate an input group if
        $input = $this -> writeWrapper(new Block, 'div', 'input-wrapper', $wrapperAttrs);
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

