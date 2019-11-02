<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\ContainerBinding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Binding\SimpleBinding;

/**
 * A skeletal renderer that generates a very basic form.
 */
class SimpleHtml extends CommonHtml implements RendererInterface
{

    /**
     * Maps element types to render methods.
     * @var array
     */
    static $renderMethodCache = [];

    public function __construct($options = [])
    {
        parent::__construct($options);
        self::$showDefaultScope = 'form';
        $this->initialize();
    }

    protected function checkList(
        Block $block,
        FieldBinding $binding,
        $list,
        $type,
        Attributes $attrs
    ) {
        $baseId = $binding->getId();
        $select = $binding->getValue();
        if ($select === null) {
            $select = $binding->getElement()->getDefault();
        }
        foreach ($list as $optId => $radio) {
            $optAttrs = $attrs->copy();
            $id = $baseId . '_opt' . $optId;
            $optAttrs->set('id', $id);
            $value = $radio->getValue();
            $optAttrs->set('value', $value);
            $optAttrs->setFlag('disabled', !$radio->getEnabled());
            if (
                $type == 'checkbox'
                && is_array($select) && in_array($value, $select)
            ) {
                $optAttrs->setFlag('checked');
                $checked = true;
            } elseif ($value === $select) {
                $optAttrs->setFlag('checked');
                $checked = true;
            } else {
                $optAttrs->setFlag('checked', false);
                $checked = false;
            }
            $optAttrs->setIfNotNull('data-nf-name', $radio->getName());
            $optAttrs->setIfNotEmpty('*data-nf-group', $radio->getGroups());
            $optAttrs->setIfNotNull('*data-nf-sidecar', $radio->sidecar);
            if ($checked) {
                $optAttrs->setFlag('checked');
            } else {
                $optAttrs->setFlag('checked', false);
            }
            $block->body .= "<div>\n" . $this->writeTag('input', $optAttrs) . "\n"
                . $this->writeLabel(
                    '', $radio->getLabel(), 'label',
                    new Attributes('!for',  $id), ['break' => true]
                )
                . "</div>\n";
        }
    }

    protected function initialize()
    {
        parent::initialize();
        // Reset the context
        $this->context = [
            'inCell' => false
        ];
        // Initialize custom settings
        $this->setShow('layout:vertical');
    }

    /**
     * Render Field elements for checkbox and radio types.
     * @param FieldBinding $binding
     * @param array $options
     * @return Block
     */
    protected function renderFieldCheckbox(FieldBinding $binding, $options = [])
    {
        if ($options['access'] === 'hide') {
            // Generate hidden elements and return
            return $this->elementHiddenList($binding);
        }

        // Get things we need to generate attributes
        $baseId = $binding->getId();
        $labels = $binding->getLabels(true);
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();

        // Set attributes for the input
        $attrs = new Attributes('type', $type);
        $attrs->setFlag('readonly', $binding->getElement()->getReadonly() || $options['access'] == 'view');
        $list = $binding->getList(true);
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);
        $attrs->set('name', $binding->getFormName());

        // Start generating output
        $block = $this->writeElement(
            'div', [
                'attributes' => $this->groupAttributes($binding)
            ]
        );
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'div', null, ['break' => true]
        );
        $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));
        $bracketTag = empty($list) ? 'span' : 'div';
        $block->body .= $this->writeLabel(
            'before', $labels->before, $bracketTag, null, ['break' => !empty($list)]
        );
        if (empty($list)) {
            $attrs->set('id', $baseId);
            $value = $binding->getValue();
            if ($value !== null) {
                $attrs->set('value', $value);
                if ($value === $binding->getElement()->getDefault()) {
                    $attrs->setFlag('checked');
                }
            }
            $block->body .= $this->writeTag('input', $attrs) . "\n";
            $block->body .= $this->writeLabel(
                'inner', $binding->getLabels(true)->inner,
                'label', new Attributes('!for', $baseId), ['break' => true]
            );
        } else {
            $this->checkList($block, $binding, $list, $type, clone $attrs);
        }
        $block->body .= $this->writeLabel(
            'after', $labels->after, $bracketTag, null, ['break' => !empty($list)]
        );
        $block->close();
        $block->body .= ($this->context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        return $block;
    }

    protected function renderFieldFile(FieldBinding $binding, $options = [])
    {
        $value = $binding->getValue();
        if ($options['access'] === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            return $this->elementHidden($binding, $value);
        }

        // We can see or change the data
        $attrs = new Attributes();
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();
        $attrs->set('id', $binding->getId());
        if ($options['access'] == 'view') {
            $type = 'text';
        }
        $attrs->set('name', $binding->getFormName());
        $attrs->setIfNotNull('value', is_array($value) ? implode(',', $value) : $value);
        $labels = $binding->getLabels(true);

        $block = $this->writeElement(
            'div', ['attributes' => $this->groupAttributes($binding)]
        );
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'label',
            new Attributes('!for', $binding->getId()), ['break' => true]
        );
        $attrs->setIfNotNull('placeholder', $labels->inner);
        $attrs->set('type', $type);
        $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));
        $block->body .= $this->writeLabel('before', $labels->before, 'span');
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);
        // Render the data list if there is one
        $block->merge($this->dataList($attrs, $binding, $type, $options));
        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs->addValidation($type, $data->getValidation());
            $attrs->setFlag('readonly', $binding->getElement()->getReadonly());
        } else {
            // View Access
            $attrs->set('type', 'text');
            $attrs->setFlag('readonly');
        }
        // Generate the input element
        $block->body .= $this->writeTag('input', $attrs) . "\n"
            . $this->writeLabel('after', $labels->after, 'span');
        $block->close();
        $block->body .= ($this->context['inCell'] ? '&nbsp;' : '<br/>') . "\n";
        return $block;
    }

    protected function renderFieldImage(FieldBinding $binding, $options = [])
    {
        $attrs = new Attributes();
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();
        $block = new Block();
        return; /// UNIMPLEMENTED
        $attrs['id'] = $binding->getId();
        $attrs->setFlag('readonly', $binding->getReadonly() || $options['access'] == 'view');
        $attrs['name'] = $binding->getFormName();
        $value = $binding->getValue();
        if ($options['access'] === 'hide' || $type === 'hidden') {
            //
            // No write/view permissions, the field is hidden, we don't need labels, etc.
            //
            $block->merge($this->elementHidden($binding, $value));
        } else {
            //
            // We can see or change the data
            //
            if ($value !== null) {
                $attrs['value'] = $value;
            }
            $labels = $binding->getLabels(true);
            $block->body .= $this->writeLabel(
                'headingAttributes', $labels->heading, 'label',
                ['!for' => $binding->getId()], ['break' => true]
            );
            $labels->insertInnerTo($attrs, 'placeholder');
            if ($type === 'range' && $options['access'] === 'view') {
                $type = 'text';
            }
            $attrs['type'] = $type;
            $block->body .= $this->writeLabel('before', $labels->before, 'span');
            $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);

            // Render the data list if there is one
            $block->merge($this->dataList($attrs, $binding, $type, $options));
            if ($options['access'] === 'write') {
                // Write access: Add in any validation
                $this->addValidation($attrs, $type, $data->getValidation());
            }
            // Generate the input element
            $block->body .= $this->writeTag('input', $attrs)
                . $this->writeLabel('after', $labels->after, 'span')
                . ($this->context['inCell'] ? '&nbsp;' : '<br/>')
                . "\n";
        }
        return $block;
    }

    protected function renderFieldSelect(FieldBinding $binding, $options = [])
    {
        $value = $binding->getValue();
        if ($options['access'] === 'hide') {

            // Hide: generate one or more hidden input elements
            return $this->elementHidden($binding, $value);
        }
        // This element is displayed
        $attrs = new Attributes();
        $block = new Block();
        $baseId = $binding->getId();
        $labels = $binding->getLabels(true);
        $data = $binding->getDataProperty();
        $multiple = $data->getValidation()->get('multiple');

        $attrs->set('name', $binding->getFormName());

        $block = $this->writeElement(
            'div', ['attributes' => $this->groupAttributes($binding)]
        );
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'div', null, ['break' => true]
        );
        $block->merge($this->writeElement('div', ['show' => 'inputWrapperAttributes']));
        $block->body .= $this->writeLabel(
            'before', $labels->before, 'div', null, ['break' => true]
        );
        if ($options['access'] == 'view') {
            $list = $binding->getFlatList(true);
            // render as hidden with text
            $attrs->set('type', 'hidden');
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
        } else {
            // Generate an actual select!
            if ($value === null) {
                $value = $binding->getElement()->getDefault();
            }
            if (!is_array($value)) {
                $value = [$value];
            }
            $attrs->set('id', $baseId);
            if (($rows = $data->getPresentation()->getRows()) !== null) {
                $attrs->set('size', $rows);
            }
            $attrs->addValidation('select', $data->getValidation());
            $block->body .= $this->writeTag('select', $attrs) . "\n";
            $block->merge(
                $this->renderFieldSelectOptions($binding->getList(true), $value)
            );
            $block->body .= '</select>' . "\n";
        }
        $this->writeLabel('after', $labels->after, 'div', null, ['break' => true]);
        $block->close();
        $block->body .= ($this->context['inCell'] ? '&nbsp;' : '<br/>') . "\n";

        return $block;
    }

    protected function renderFieldTextarea(FieldBinding $binding, $options = [])
    {
        $value = $binding->getValue();
        if ($options['access'] === 'hide') {

            // No write/view permissions, the field is hidden, we don't need labels, etc.
            return $this->elementHidden($binding, $value);
        }

        // We can see or change the data
        $attrs = new Attributes();
        $data = $binding->getDataProperty();
        $presentation = $data->getPresentation();
        $type = $presentation->getType();
        $attrs->set('id', $binding->getId());
        $attrs->setFlag('readonly', $binding->getElement()->getReadonly() || $options['access'] == 'view');
        $attrs->set('name', $binding->getFormName());

        $block = $this->writeElement(
            'div', ['attributes' => $this->groupAttributes($binding)]
        );
        $labels = $binding->getLabels(true);
        $block->body .= $this->writeLabel(
            'headingAttributes', $labels->heading, 'label',
            new Attributes('!for', $attrs->get('id')), ['break' => true]
        );
        $attrs->setIfNotNull('placeholder', $labels->inner);
        $attrs->setIfNotNull('cols', $presentation->getCols());
        $attrs->setIfNotNull('rows', $presentation->getRows());
        $block->body .= $this->writeLabel(
            'before', $labels->before, 'div', null, ['break' => true]
        );
        $attrs->setIfNotNull('*data-nf-sidecar', $data->getPopulation()->sidecar);
        if ($options['access'] === 'write') {
            // Write access: Add in any validation
            $attrs->addValidation($type, $data->getValidation());
        }
        if ($value === null) {
            $value = '';
        }
        // Generate the textarea element
        $block->body .= $this->writeTag('textarea', $attrs, $value)
            . $this->writeLabel(
                'after', $labels->after, 'div', null, ['break' => true]
            )
            . "\n";

        $block->close();
        return $block;
    }

    protected function renderSectionElement(ContainerBinding $binding, $options = [])
    {
        $block = new Block();
        $labels = $binding->getLabels(true);
        $block->body = '<fieldset>' . "\n";
        if ($labels !== null) {
            $block->body .= $this->writeLabel(
                '', $labels->heading, 'legend', null, ['break' => true]
            );
        }
        $block->post = '</fieldset>' . "\n";
        return $block;
    }

    protected function renderStaticElement(SimpleBinding $binding, $options = [])
    {
        $block = new Block();

        // There's no way to hide this element so if access is hidden, skip it.
        if ($options['access'] === 'hide') {
            return $block;
        }

        $block = $this->writeElement(
            'div', ['attributes' => $this->groupAttributes($binding)]
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
        $block->body .= $binding->getElement()->getHtml() ? $value : htmlspecialchars($value);
        $block->close();
        $block->body .= "<br/>\n";

        return $block;
    }

    protected function renderTriggers(FieldBinding $binding) : Block
    {
        return new Block;
    }

    public function setOptions($options = [])
    {

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

        $styleList = ['display' => 'inline-block', 'padding' => '0.5rem'];
        if ($choice == 'a') {
            $styleList['padding-left'] = '1rem';
        } else {
            foreach ($values as $value) {
                \preg_match(
                    '/(?<prefix>[a-z][a-z0-9]-)?(?<weight>[0-5])/',
                    $value, $match
                );
                if ($match['prefix'] !== '' && $match['prefix'] !== 'bs-') {
                    continue;
                }
                $weight = (int) $match['weight'];
            }
            $styleList['padding-left'] = round(2 * $weight / 5, 2) . 'rem';
        }
        if (!empty($styleList)) {
            $this->showState[$scope]['cellspacing']
                = new Attributes('style', $styleList);
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
        if (!isset($this->showState[$scope])) {
            $this->showState[$scope] = [];
        }
        // Clear out anything that might have been set by previous commands.
        unset($this->showState[$scope]['cellElementAttributes']);
        unset($this->showState[$scope]['headingAttributes']);
        unset($this->showState[$scope]['inputWrapperAttributes']);
        $this->showState[$scope]['layout'] = $choice;
        if ($choice === 'horizontal') {
            $this->showDoLayoutAnyHorizontal($scope, $values);
        } elseif ($choice === 'vertical') {
            $this->showDoLayoutAnyVertical($scope, $values);
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
        // h:nxx        - First column width in CSS units
        // h:nxx/mxx    - CSS units for headers / input elements
        // h:n:m:t      - ratio of headers to inputs over space t. If no t, t=n+m
        // h:.c1        - Class for headers
        // h:.c1:.c2    - Class for headers / input elements
        $apply = &$this->showState[$scope];
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
                $apply['inputWrapperAttributes'] = new Attributes(
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
                    $apply['inputWrapperAttributes'] = new Attributes('class', [substr($values[2], 1)]);
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
                    $apply['inputWrapperAttributes'] = new Attributes(
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
                    $apply['inputWrapperAttributes'] = new Attributes(
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
        if (isset($apply['inputWrapperAttributes'])) {
            $apply['cellElementAttributes'] = $apply['inputWrapperAttributes'];
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
        // v            - Default, nothing to do
        // v:mxx        - CSS units for input elements
        // v:.c2        - Class for input elements
        // v:m:t        - ratio of inputs over space t.
        $apply = $this->showState[$scope];
        switch (count($values)) {
            case 1:
                // No specification, nothing to do
                break;
            case 2:
                if ($values[1][0] == '.') {
                    // Single class specification
                    $apply['inputWrapperAttributes'] = [
                        'class' => [substr($values[1], 1)],
                    ];
                } else {
                    // Single CSS units
                    $apply['inputWrapperAttributes'] = [
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
                    $apply['inputWrapperAttributes'] = [
                        'style' => [
                            'display' => 'inline-block',
                            'vertical-align' => 'top',
                            'width' => round(100.0 * $part1 / $sum, 3) . '%'
                        ],
                    ];
                }
                break;
        }
        if (isset($apply['inputWrapperAttributes'])) {
            $apply['cellElementAttributes'] = $apply['inputWrapperAttributes'];
        }
    }

}

