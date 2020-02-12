<?php
namespace Abivia\NextForm\Render;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\ContainerBinding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Element\Element;
use Abivia\NextForm\NextForm;
use Abivia\NextForm\Traits\ShowableTrait;

/**
 * A base for HTML rendering
 */
class Html implements RenderInterface
{
    use ShowableTrait;

    protected $context = [];
    protected $contextStack = [];

    /**
     * Types of <input> that we'll auto-generate a confirmation for
     * @var array
     */
    static $inputConfirmable = [
        'email', 'number', 'password', 'tel', 'text',
    ];

    /**
     * Maps element types to render classes.
     * @var array
     */
    protected $renderClassCache = [];

    /**
     * Quick lookup for self-closing elements
     * @var array
     */
    static $selfClose = ['input' => 1, 'option' => 2];

    /**
     * Default values and validation rules for show settings. Validation
     * rules are organized by scope. Options are a list of valid strings,
     * each prefixed with |, an array indexed by integers with each element
     * being an array of [regex, replace], or an array indexed with the formal
     * value with a regex for that value being the array content. Rules are
     * evaluated sequentially, the first match is used.
     *
     * @var array
     */
    static public $showRules = [
        'appearance' => [
            'default' => 'default',
            'validate' => [
                'check' => [
                    'default' => '/^default$/i',
                    'no-label' => '/^no-?labels?$/i',
                    'toggle' => '/^toggle$/i',
                    'switch' => '/^switch$/i',
                ],
                'select' => [
                    'default' => '/^default$/i',
                    'custom' => '/^custom$/i',
                ],
            ],
        ],
        'cellspacing' => [
            'default' => '3',
            'validate' => [
                'form' => '@showIsSpan',
            ],
            'validateMode' => 'pack',
        ],
        'fill' => [
            'default' => 'solid',
            'validate' => [
                'form' => '|outline|solid|',
            ],
        ],
        'hidden' => [
            'default' => 'nf_hidden',
        ],
        'layout' => [
            'default' => 'vertical',
            'validate' => [
                'form' => [
                    'horizontal' => '/^hor/i',
                    'vertical' => '/^ver/i',
                    'inline' => '/^in/i'
                ],
            ],
        ],
        'optionwidth' => [
            'default' => '',
            'validate' => [
                'check' => '@showIsSpan',
            ],
            'validateMode' => 'pack',
        ],
        'purpose' => [
            'default' => 'primary',
            'validate' => [
                'form' => '|dark|danger|info|light|link|primary|secondary|success|warning|',
            ],
        ],
        'size' => [
            'default' => 'regular',
            'validate' => [
                'form' => [
                    'large' => '/^l/', 'regular' => '/^[mr]/', 'small' => '/^s/'
                ]
            ],
        ],
    ];

    /**
     * Custom classes and styles to apply to various form elements
     * @var array
     */
    protected $showState = [];
    protected $showStack = [];

    /**
     * Patterns used for validating a span show setting.
     * @var array
     */
    static $showSpanPatterns = [
        '(?<scheme>[a-z][a-z0-9])',
        '(?<size>sm|md|lg|xl|xs)',
        '(?<weight>[0-9]+)'
    ];

    public function __construct($options = [])
    {
        self::$showDefaultScope = 'form';
        $this->initialize();
    }

    /**
     * Ensure we have a slot for the requested scope.
     *
     * @param string $scope
     */
    protected function checkShowState($scope)
    {
        if (!isset($this->showState[$scope])) {
            $this->showState[$scope] = [];
        }
    }

    /**
     * Generate a hidden element.
     *
     * @param Binding $binding
     * @param mixed $value
     * @return \Abivia\NextForm\Render\Block
     */
    public function elementHidden($binding, $value)
    {
        $block = new Block();
        $baseId = $binding->getId();
        $nameOnForm = $binding->getNameOnForm(true);
        $attrs = new Attributes('type', 'hidden');
        if ($binding instanceof \Abivia\NextForm\Form\Binding\FieldBinding) {
            $attrs->setIfNotNull(
                '*data-nf-sidecar',
                $binding->getDataProperty()->getPopulation()->sidecar
            );
        }
        if (\is_array($value)) {
            $optId = 0;
            foreach ($value as $key => $entry) {
                $attrs->set('id', $baseId . '_opt' . $optId);
                ++$optId;
                $attrs->set('name', $nameOnForm . '[' . \htmlspecialchars($key) . ']');
                $attrs->set('value', $entry);
                $block->body .= $this->writeTag('input', $attrs) . "\n";
            }
        } else {
            $attrs->set('id', $baseId);
            $attrs->set('name', $nameOnForm);
            $attrs->setIfNotNull('value', $value);
            $block->body .= $this->writeTag('input', $attrs) . "\n";
        }
        return $block;
    }

    /**
     * Generate hidden elements for an option list.
     * @param FieldBinding $binding The binding we're generating for.
     * @return \Abivia\NextForm\Render\Block The output block.
     */
    public function elementHiddenList(FieldBinding $binding)
    {
        $needEmpty = true;
        $block = new Block();
        $baseId = $binding->getId();
        $select = $binding->getValue();
        $list = $binding->getList(true);
        $attrs = new Attributes('type', 'hidden');
        $attrs->set('name', $binding->getNameOnForm(true) . (empty($list) ? '' : '[]'));
        if ($select === null) {
            $select = $binding->getElement()->getDefault();
        }
        foreach ($list as $optId => $radio) {
            $optAttrs = $attrs->copy();
            $id = $baseId . '_opt' . $optId;
            $optAttrs->set('id', $id);
            $value = $radio->getValue();
            $optAttrs->set('value', $value);
            $optAttrs->setIfNotNull('*data-nf-sidecar', $radio->sidecar);
            if (is_array($select)) {
                $checked = in_array($value, $select);
            } else {
                $checked = $value === $select;
            }
            if ($checked) {
                $block->body .= $this->writeTag('input', $optAttrs) . "\n";
                $needEmpty = false;
            }
        }
        if ($needEmpty) {
            $block = $this->elementHidden($binding, $select);
        }
        return $block;
    }

    public function epilog()
    {
        return new Block();
    }

    /**
     * Get the current access level. A container can override the option value.
     *
     * @param array $options Access is stored under the key "access".
     * @return string The access permission.
     */
    public function getAccess($options)
    {
        if ($this->context['containerAccess'] !== false) {
            $access = $this->context['containerAccess'];
        } elseif (isset($options['access'])) {
            $access = $options['access'];
        } else {
            $access = 'write';
        }
        return $access;
    }

    protected function getRenderClass(Element $element)
    {
        $engineClass = \get_class($this);
        $classPath = \get_class($element);
        if (!isset($this->renderClassCache[$engineClass])) {
            $this->renderClassCache[$engineClass] = [];
        }
        if (!isset($this->renderClassCache[$engineClass][$classPath])) {
            $lastPos = \strrpos($classPath, '\\');
            $lastPart = \substr($classPath, $lastPos) . 'Render';
            $renderClass = $engineClass . $lastPart;
            if (!\class_exists($renderClass)) {
                $lastPos = \strrpos($engineClass, '\\');
                $renderClass = \substr($engineClass, 0, $lastPos + 1)
                    . 'Html' . $lastPart;
                if (!\class_exists($renderClass)) {
                    $renderClass = false;
                }
            }
            $this->renderClassCache[$engineClass][$classPath] = $renderClass;
        }
        return $this->renderClassCache[$engineClass][$classPath];
    }

    /**
     * Generate attributes for a group container.
     * @param Binding $binding
     * @return \Abivia\NextForm\Render\Attributes
     */
    public function groupAttributes(Binding $binding, $options = []) : Attributes
    {
        $id = $options['id'] ?? $binding->getId();
        $element = $binding->getElement();
        $container = new Attributes('id', $id . NextForm::CONTAINER_LABEL);
        if (!$element->getDisplay()) {
            $container->merge($this->showGet('form', 'hidden'));
        }
        if ($this->context['inCell']) {
            if ($this->context['cellFirstElement']) {
                $this->context['cellFirstElement'] = false;
            } else {
                $container->merge($this->showGet('form', 'cellspacing'));
            }
        }
        $container->setIfNotEmpty('*data-nf-group', $element->getGroups());
        $container->set('data-nf-for', $id);
        return $container;
    }

    protected function initialize()
    {
        // Reset the context
        $this->context = [
            'containerAccess' => false,
            'inCell' => false
        ];
        // Initialize custom settings
        $this->setShow('cellspacing:3');
        $this->setShow('hidden:nf-hidden');
        $this->setShow('layout:vertical');
    }

    /**
     * Pop the rendering context
     */
    public function popContext()
    {
        if (count($this->contextStack)) {
            $this->context = \array_pop($this->contextStack);
            $this->showState = \array_pop($this->showStack);
        }
    }

    /**
     * Push the rendering context
     */
    public function pushContext()
    {
        \array_push($this->contextStack, $this->context);
        \array_push($this->showStack, $this->showState);
    }

    public function queryContext($selector)
    {
        if (!isset($this->context[$selector])) {
            throw new \RuntimeException(
                $selector . ' is not valid in the current context.'
            );
        }
        return $this->context[$selector];
    }

    public function render(Binding $binding, $options = []) : Block
    {
        if (!isset($options['access'])) {
            $options['access'] = 'write';
        }
        $block = new Block();
        if ($this->context['containerAccess'] === 'none') {
            // We're in a no-output container
            return $block;
        }
        if ($options['access'] === 'none') {
            if ($binding instanceof ContainerBinding) {
                $block->onCloseDone = [$this, 'popContext'];
                $this->pushContext();
                $this->setContext('containerAccess', 'none');
            }
            return $block;
        }
        $renderClass = $this->getRenderClass($binding->getElement());
        if ($renderClass) {
            $test = new $renderClass($this, $binding);
            $block = $test->render($options);
        } else {
            throw new \RuntimeException(
                'Unable to render element '
                . \get_class($binding->getElement())
            );
        }
        return $block;
    }

    public function renderTriggers(FieldBinding $binding) : Block
    {
        return new Block;
    }

    /**
     * Modify a setting in the current rendering context.
     *
     * @param string $selector The name of the setting to modify.
     * @param mixed $value The value to assign to the setting.
     * @return \self
     * @throws \RuntimeException
     */
    public function setContext($selector, $value) : self
    {
        $this->context[$selector] = $value;
        return $this;
    }

    public function setOptions($options = []) {

    }

    /**
     * Convert a set of visual settings into rendering parameters.
     * @param string $settings
     */
    public function setShow($settings, $defaultScope = '')
    {
        $settings = self::showTokenize($settings, $defaultScope);
        foreach ($settings as $scope => $list) {
            foreach ($list as $key => $value) {
                $this->show($scope, $key, $value);
            }
        }
    }

    /**
     * Process a show property, setting internal data structures as required.
     * @param string $key The name of the setting
     * @param array $args A list of arguments
     * @throws \RuntimeError
     */
    public function show($scope, $key, $args)
    {
        if (!isset(self::$showRules[$key])) {
            throw new \RuntimeException(
                'Invalid show: ' . $key . ' is not recognized.'
            );
        }
        $keyRules = self::$showRules[$key];
        if (empty($args) && isset($keyRules['default'])) {
            $args[0] = $keyRules['default'];
        }
        if (isset($keyRules['validate'][$scope])) {
            $rules = $keyRules['validate'][$scope];
            $validateMode = $keyRules['validateMode'] ?? 'choice';
        } elseif (isset($keyRules['validate']['form'])) {
            $rules = $keyRules['validate']['form'];
            $validateMode = $keyRules['validateMode'] ?? 'choice';
        } else {
            $rules = null;
        }
        if ($rules) {
            $choice = $this->showValidate($args, $validateMode, $rules, $key);
        } else {
            $choice = $args[0];
        }
        // See if there's a method to process subsequent arguments,
        // if not, just store the setting in $choice
        $method = 'showDo' . ucfirst($key);
        if (\method_exists($this, $method)) {
            $this->$method($scope, $choice, $args);
        } else {
            if (!isset($this->showState[$scope])) {
                $this->showState[$scope] = [];
            }
            $this->showState[$scope][$key] = $choice;
        }
    }

    /**
     * Default cell spacing options, called from show().
     *
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    public function showDoCellspacing($scope, $choice, $values = [])
    {
        $this->showState[$scope]['cellspacing']
            = new Attributes('class', ['cellspace']);
    }


    /**
     * Process layout options stub.
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    public function showDoLayout($scope, $choice, $values = [])
    {
        if (!isset($this->showState[$scope])) {
            $this->showState[$scope] = [];
        }
        $this->showState[$scope]['layout'] = $choice;
    }

    /**
     * Process hidden options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    public function showDoHidden($scope, $choice, $values = [])
    {
        if (!isset($this->showState[$scope])) {
            $this->showState[$scope] = [];
        }
        // Use the choice as a class name
        $this->showState[$scope]['hidden'] = new Attributes('class', $choice);
    }

    /**
     * Look for a show setting, falling back to the form if required.
     * @param string $scope The scope to be searched for a value.
     * @param string $key The index of the value we want.
     * @return mixed
     */
    public function showGet($scope, $key)
    {

        if (($result = $this->showGetLocal($scope, $key)) !== null) {
            return $result;
        }
        if ($scope !== 'form') {
            // Look for something specified at the form level
            if (($result = $this->showGetLocal('form', $key)) !== null) {
                return $result;
            }
        }
        if (isset(self::$showRules[$key]['default'])) {
            $this->showState['form'][$key] = self::$showRules[$key]['default'];
            return $this->showState['form'][$key];
        }
        return null;
    }

    /**
     * Look for a matching show setting
     * @param string $scope The scope to be searched for a value.
     * @param string $key The index of the value we want.
     * @return mixed
     */
    public function showGetLocal($scope, $key)
    {
        if (!isset($this->showState[$scope])) {
            return null;
        }
        if (!isset($this->showState[$scope][$key])) {
            return null;
        }
        return $this->showState[$scope][$key];
    }

    /**
     * Check to see if a string is a valid span.
     * @param string $value
     * @return bool
     */
    static public function showIsSpan($value) : bool
    {
        $segments = explode(':', $value);
        foreach ($segments as $segment) {
            $parts = explode('-', $segment);
            $numParts = count($parts);
            $pattern = '/' .
                implode('-', array_slice(self::$showSpanPatterns, 3 - $numParts))
                . '/';
            if (preg_match($pattern, $value) !== 1) {
                return false;
            }
        }
        return true;
    }

    /**
     * Break a generalized span string xx-ss-nnn down into parts and validate.
     * @param string $value span settings, multiples delimited by :
     * @return array[] Array containing arrays of match(bool), size,
     * weight(int), scheme.
     */
    static public function showParseSpan($value) {
        static $unmatched = [
            'match' => false, 'scheme' => null, 'size' => null, 'weight' => null,
            'class' => null
        ];

        $subValues = explode(':', $value);
        $results = [];
        foreach ($subValues as $subValue) {
            $result = $unmatched;
            $parts = explode('-', $subValue);
            $numParts = count($parts);
            $pattern = '/' .
                implode('-', array_slice(self::$showSpanPatterns, 3 - $numParts))
                . '/';
            if ($numParts === 3) {
                if (preg_match($pattern, $subValue, $match)) {
                    $result['match'] = true;
                    $result['scheme'] = $match['scheme'];
                    $result['size'] = $match['size'];
                    $result['weight'] = (int) $match['weight'];
                }
            } elseif ($numParts === 2) {
                if (preg_match($pattern, $subValue, $match)) {
                    $result['match'] = true;
                    $result['size'] = $match['size'];
                    $result['weight'] = (int) $match['weight'];
                }
            } elseif ($numParts === 1) {
                if (preg_match($pattern, $parts[0])) {
                    $result['match'] = true;
                    $result['size'] = 'xs';
                    $result['weight'] = (int) $parts[0];
                }
            }
            if ($result['match']) {
                if ($result['size'] === 'xs') {
                    $result['size'] = '';
                    $result['class'] = $result['weight'];
                } else {
                    $result['class'] = $result['size'] . '-'
                        . $result['weight'];
                }
            }
            $results[] = $result;
        }
        return $results;
    }

    /**
     * Validate the arguments for a show setting.
     *
     * @param array $args The arguments provided by the user/application.
     * @param string $mode Validation mode: choice or pack
     * @param mixed $rules Validation rules (provided by $showRules)
     * @param string $key The setting name.
     * @return type
     * @throws \RuntimeException
     */
    public function showValidate($args, $mode, $rules, $key) {
        if ($mode == 'pack') {
            $setting = implode(':', $args);
        } else {
            $setting = $args[0];
        }
        $valid = false;
        if (\is_array($rules)) {
            // Keyword selection or match/replace, depending on the value.
            // Scalar $match is keyword mode, Array $match is [match, replace],
            // return the key ($choice)
            foreach ($rules as $choice => $match) {
                if (\preg_match($match, $setting)) {
                    $valid = true;
                    break;
                }
            }
        } else {
            $process = $rules[0];
            // Plain string match
            $choice = $setting;
            if ($process === '|') {
                $valid = \strpos($rules, '|' . $choice . '|') !== false;
            } elseif ($process === '@') {
                $method =substr($rules, 1);
                $valid = $this->$method($choice);
            }
        }
        if (!$valid) {
            throw new \RuntimeException(
                'Invalid show setting: ' . $setting . ' is not valid for ' . $key
            );
        }
        return $choice;
    }

    /**
     * Start form generation
     * @param array $options @see Manager
     * @return \Abivia\NextForm\Render\Block
     */
    public function start($options = []) : Block
    {
        $this->initialize();
        if (isset($options['attributes'])) {
            $attrs = $options['attributes'];
        } else {
            $attrs = new Attributes();
        }
        if (!$attrs->has('id') || !$attrs->has('name')) {
            throw new \RuntimeException('Form must have name and id attributes');
        }
        $attrs->set('method', isset($options['method']) ? $options['method'] : 'post');
        $attrs->setIfSet('action', $options);

        $pageData = new Block();
        $pageData->styles = '.nf-hidden {display:none}' . "\n";
        $pageData->body = $this->writeTag('form', $attrs) . "\n";
        $pageData->post = '</form>' . "\n";
        $nfToken = NextForm::getCsrfToken();
        if ($nfToken[0] !== '') {
            $pageData->body .= '<input id="' . $nfToken[0] . '"'
                . ' name="' . $nfToken[0] . '" type="hidden"'
                . ' value="' . $nfToken[1] . '">' . "\n";
        }
        return $pageData;
    }

    /**
     * Conditionally write an element into an open Block suitable for merging.
     * @param string $tag Name of the element to write (div, span, etc.)
     * @param array $options Name(type,default): append(string,''), force(bool,false),
     *                      show(string,''), attrs(Attributes,null)
     * @return \Abivia\NextForm\Render\Block
     */
    public function writeElement($tag, $options = [])
    {
        $hasPost = false;
        $attrs = $options['attributes'] ?? new Attributes();
        if (isset($options['show'])) {
            list($scope, $setting) = self::showGetSetting($options['show']);
        } else {
            $scope = false;
        }
        $block = new Block();
        if ($scope && isset($this->showState[$scope][$setting])) {
            $attrs = $attrs->merge($this->showState[$scope][$setting]);
            $block->body = $this->writeTag($tag, $attrs) . "\n";
            $hasPost = true;
        } elseif (!$attrs->isEmpty()) {
            $block->body = $this->writeTag($tag, $attrs) . "\n";
            $hasPost = true;
        } elseif ($options['force'] ?? false) {
            $block->body = '<' . $tag . ">\n";
            $hasPost = true;
        }
        if ($hasPost) {
            $block->post = '</' . $tag . ">\n"
                . (isset($options['append']) ? $options['append'] : '');
        }
        return $block;
    }

    /**
     * Write a label if required.
     * @param string $purpose A string indicating what this label is for.
     * @param string $text The text for the label
     * @param string $tag The kind of HTML tag to wrap the label in.
     * @param \Abivia\NextForm\Render\Attributes $attrs HTML attributes to associate with the element
     * @param type $options break(bool,''), div(string,classes)
     * @return string
     */
    public function writeLabel($purpose, $text, $tag, $attrs = null, $options = [])
    {
        if ($text === null) {
            // In horizontal layouts we always generate an element
            if (
                $this->showState['form']['layout'] === 'horizontal'
                && $purpose === 'headingAttributes'
            ) {
                $text = '&nbsp;';
            } else {
                return '';
            }
        } else {
            $text = \htmlspecialchars($text);
        }
        if (isset($this->showState['form'][$purpose])) {
            $attrs = $attrs ?? new Attributes();
            $attrs = $attrs->merge($this->showState['form'][$purpose]);
        }
        $breakTag = $options['break'] ?? false;
        $html = $this->writeTag($tag, $attrs)
            . $text
            . '</' . $tag . '>' . ($breakTag ? "\n" : '')
        ;
        // Check to see if we should wrap this in a div.
        if (isset($options['div'])) {
            $html = '<div class="' . $options['div'] . '">' . "\n"
                . $html . ($breakTag ? '' : "\n") . "</div>\n";
        }
        return $html;
    }

    /**
     * Write an element and attributes into escaped HTML
     * @param \Abivia\NextForm\Render\Attributes $attrs
     * @return string
     */
    public function writeTag($tag, $attrs = null, $text = null)
    {
        $html = '<' . $tag . ($attrs ? $attrs->write($tag) : '');
        if (isset(self::$selfClose[$tag]) && $text === null) {
            $html .= '/>';
        } elseif ($text !== null) {
            $html .= '>' . \htmlentities($text) . '</' . $tag . '>';
        } else {
            $html .= '>';
        }
        return $html;
    }

}

