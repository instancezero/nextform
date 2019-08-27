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
        $attrs -> merge($this -> showFindAll('button', ['button-attrs']));
        $attrs -> merge($this -> showFindAll('button', ['size-attrs']));
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

    protected function renderFieldCheckbox(FieldElement $element, $options = []) {
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
        if ($visible) {
            $block -> body .= $this -> writeLabel(
                'heading', $labels -> heading, 'div', null, ['break' => true]
            );
            $block = $this -> writeWrapper($block, 'div', 'input-wrapper');
            $bracketTag = empty($list) ? 'span' : 'div';
            $block -> body .= $this -> writeLabel(
                'before', $labels -> before, $bracketTag, null, ['break' => !empty($list)]
            );
        }
        if (empty($list)) {
            $attrs -> set('id', $baseId);
            $attrs -> setIfNotNull('value', $element -> getValue());
            $sidecar = $data -> getPopulation() -> sidecar;
            $attrs -> setIfNotNull('*data-sidecar', $sidecar);
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            if ($visible) {
                $block -> body .= $this -> writeLabel(
                    'inner', $element -> getLabels(true) -> inner,
                    'label', new Attributes('!for', $baseId), ['break' => true]
                );
            }
        } else {
            $select = $element -> getValue();
            if ($select === null) {
                $select = $element -> getDefault();
            }
            foreach ($list as $optId => $radio) {
                $id = $baseId . '-opt' . $optId;
                $attrs -> set('id', $id);
                $value = $radio -> getValue();
                $attrs -> set('value', $value);
                if ($type == 'checkbox' && is_array($select) && in_array($value, $select)) {
                    $attrs -> setFlag('checked');
                    $checked = true;
                } elseif ($value === $select) {
                    $attrs -> setFlag('checked');
                    $checked = true;
                } else {
                    $attrs -> clearFlag('checked');
                    $checked = false;
                }
                $attrs -> setIfNotNull('*data-sidecar', $radio -> sidecar);
                if ($visible) {
                    if ($checked) {
                        $attrs -> setFlag('checked');
                    } else {
                        $attrs -> clearFlag('checked');
                    }
                    $block -> body .= "<div>\n  " . $this -> writeTag('input', $attrs) . "\n"
                        . '  '
                        . $this -> writeLabel(
                            '', $radio -> getLabel(), 'label',
                            new Attributes('!for',  $id), ['break' => true]
                        )
                        . "</div>\n";
                } elseif ($checked) {
                    $block -> body .= $this -> writeTag('input', $attrs) . "\n";
                }
            }
        }
        if ($visible) {
            $block -> body .= $this -> writeLabel(
                'after', $labels -> after, $bracketTag,
                [], ['break' => !empty($list)]
            );
            $block -> close();
            $block -> body .= ($this -> context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
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
     * Process layout options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoLayout($scope, $choice, $values = []) {
        if ($scope == 'check') {
            $this -> showDoLayoutCheck($choice, $value);
            return;
        }
        if (!isset($this -> custom[$scope])) {
            $this -> custom[$scope] = [];
        }
        unset($this -> custom['input-wrapper']);
        $this -> custom[$scope]['layout'] = $choice;
        $this -> custom[$scope]['cell-wrapper'] = new Attributes('class', ['form-row']);
        $this -> custom[$scope]['group-wrapper'] = new Attributes('class', ['form-group']);
        if ($choice === 'horizontal') {
            $this -> showDoLayoutAnyHorizontal($scope, $values);
        } elseif ($choice === 'vertical') {
        }
    }

    /**
     * Process horizontal layout settings for any scope
     * @param string $scope Names the settings scope/element this applies to.
     * @param array $values Array of colon-delimited settings including the initial keyword.
     * @throws \RuntimeException
     */
    protected function showDoLayoutAnyHorizontal($scope, $values) {
        $apply = &$this -> custom[$scope];
        // possible values for arguments:
        // h            - We get to decide
        // h:nxx        - Ignored, we decide
        // h:nxx/mxx    - Ignored, we decide
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Ignored, we decide
        // h:.c1:.c2    - Class for headers / input elements
        $default = true;
        $apply['group-wrapper'] -> append('class', 'row');
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
        $apply = &$this -> custom[$scope];
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
     * Process a show layout command for checkboxes/radio inputs.
     * @param type $choice
     * @param type $value
     */
    protected function showDoLayoutCheck($choice, $value = []) {
        // options are vertical, inline
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
        $this -> custom[$scope]['button-attrs'] = new Attributes('class', ['btn btn-' . $choice]);
    }

    /**
     * Process size options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoSize($scope, $choice, $values = []) {
        static $buttonClasses = ['large' => 'btn-lg', 'small' => 'btn-sm'];
        if (!isset($this -> custom[$scope])) {
            $this -> custom[$scope] = [];
        }
        $this -> custom[$scope]['size'] = $choice;
        switch ($scope) {
            case 'button':
                if (isset($buttonClasses[$choice])) {
                    $this -> custom[$scope]['size-attrs'] = new Attributes(
                        'class', [$buttonClasses[$choice]]
                    );
                } else {
                    unset($this -> custom[$scope]['size-attrs']);
                }
                break;
        }
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

