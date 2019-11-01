<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm\Manager;
use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Element\Element;
use Abivia\NextForm\Traits\ShowableTrait;

/**
 * A base for HTML rendering
 */
abstract class Html implements RendererInterface
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
     * Maps element types to render methods.
     * @var array
     */
    static $renderMethodCache = [];

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
                'form' => [
                    // Optional prefix "rr-" allows applications to provide
                    // renderer-specific settings.
                    'a' => '/(([a-z][a-z0-9]-)?(sm|md|lg|xl)\-[0-5]:?)+/',
                    'b' => '/(([a-z][a-z0-9]-)?([0-5]):?)+/',
                ],
            ],
            'validateMode' => 'pack',
        ],
        'fill' => [
            'default' => 'solid',
            'validate' => [
                'form' => '|outline|solid',
            ],
        ],
        'hidden' => [
            'default' => 'nf_hidden',
        ],
        'layout' => [
            'default' => 'vertical',
            'validate' => [
                'form' => [
                    'horizontal' => '/^hor/i', 'vertical' => '/^ver/i', 'inline' => '/^in/i'
                ],
            ],
        ],
        'purpose' => [
            'default' => 'primary',
            'validate' => [
                'form' => '|dark|danger|info|light|link|primary|secondary|success|warning',
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

    public function __construct($options = [])
    {
        self::$showDefaultScope = 'form';
    }

    /**
     * Generate a hidden element.
     *
     * @param Binding $binding
     * @param mixed $value
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function elementHidden($binding, $value)
    {
        $block = new Block();
        $baseId = $binding->getId();
        $formName = $binding->getFormName(true);
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
                $attrs->set('name', $formName . '[' . \htmlspecialchars($key) . ']');
                $attrs->set('value', $entry);
                $block->body .= $this->writeTag('input', $attrs) . "\n";
            }
        } else {
            $attrs->set('id', $baseId);
            $attrs->set('name', $formName);
            $attrs->setIfNotNull('value', $value);
            $block->body .= $this->writeTag('input', $attrs) . "\n";
        }
        return $block;
    }

    /**
     * Generate hidden elements for an option list.
     * @param FieldBinding $binding The binding we're generating for.
     * @return \Abivia\NextForm\Renderer\Block The output block.
     */
    public function elementHiddenList(FieldBinding $binding)
    {
        $needEmpty = true;
        $block = new Block();
        $baseId = $binding->getId();
        $select = $binding->getValue();
        $list = $binding->getList(true);
        $attrs = new Attributes('type', 'hidden');
        $attrs->set('name', $binding->getFormName(true) . (empty($list) ? '' : '[]'));
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

    protected function getRenderClass(Element $element)
    {
        $engineClass = \get_class($this);
        $classPath = \get_class($element);
        if (!isset($this->renderClassCache[$classPath])) {
            $classParts = \explode('\\', $classPath);
            $this->renderClassCache[$classPath] = $engineClass
                . '\\' . \array_pop($classParts);
        }
        return $this->renderClassCache[$classPath];
    }

    protected function getRenderMethod(Element $element)
    {
        $classPath = \get_class($element);
        if (!isset(self::$renderMethodCache[$classPath])) {
            $classParts = \explode('\\', $classPath);
            self::$renderMethodCache[$classPath] = 'render' . \array_pop($classParts);
        }
        return self::$renderMethodCache[$classPath];
    }

    /**
     * Generate attributes for a group container.
     * @param Binding $binding
     * @return \Abivia\NextForm\Renderer\Attributes
     */
    public function groupAttributes(Binding $binding, $options = []) : Attributes
    {
        $id = $options['id'] ?? $binding->getId();
        $element = $binding->getElement();
        $container = new Attributes('id', $id . '_container');
        if (!$element->getDisplay()) {
            //$container->set('style', 'display:none');
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

    protected function initialize() {
        // Reset the context
        $this->context = [];
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
            throw new \RuntimeException($selector . ' is not valid in current context.');
        }
        return $this->context[$selector];
    }

    public function render(Binding $binding, $options = []) : Block
    {
        if (!isset($options['access'])) {
            $options['access'] = 'write';
        }
        if ($options['access'] === 'none') {
            return new Block();
        }
        // Temporary code for conversion to rendering subclasses
        $renderClass = $this->getRenderClass($binding->getElement());
        if (\class_exists($renderClass)) {
            $test = new $renderClass($this, $binding);
            $block = $test->render($options);
        } else {
            $method = $this->getRenderMethod($binding->getElement());
            if (!method_exists($this, $method)) {
                throw new \RuntimeException('Unable to render element ' . get_class($binding->getElement()));
            }
            $block = $this->$method($binding, $options);
        }
        return $block;
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
    protected function show($scope, $key, $args)
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
     * Process hidden options, called from show()
     * @param string $scope Names the settings scope/element this applies to.
     * @param string $choice Primary option selection
     * @param array $values Array of colon-delimited settings including the initial keyword.
     */
    protected function showDoHidden($scope, $choice, $values = [])
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
    protected function showGet($scope, $key)
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
    protected function showGetLocal($scope, $key)
    {
        if (!isset($this->showState[$scope])) {
            return null;
        }
        if (!isset($this->showState[$scope][$key])) {
            return null;
        }
        return $this->showState[$scope][$key];
    }

    protected function showValidate($setting, $mode, $rules, $key) {
        if ($mode == 'pack') {
            $setting = implode(':', $setting);
        } else {
            $setting = $setting[0];
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
            // Plain string match
            $choice = $setting;
            $valid = \strpos($rules, '|' . $choice) !== false;
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
     * @return \Abivia\NextForm\Renderer\Block
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
        if (isset($options['token'])) {
            $pageData->token = $options['token'];
        } else {
            $pageData->token = \bin2hex(random_bytes(32));
        }
        $nfToken = $options['tokenName'] ?? 'nf_token';
        if ($pageData->token !== '') {
            $pageData->body .= '<input id="' . $nfToken . '"'
                . ' name="' . $nfToken . '" type="hidden"'
                . ' value="' . $pageData->token . '">' . "\n";
        }
        return $pageData;
    }

    /**
     * Conditionally write an element into an open Block suitable for merging.
     * @param string $tag Name of the element to write (div, span, etc.)
     * @param array $options Name(type,default): append(string,''), force(bool,false),
     *                      show(string,''), attrs(Attributes,null)
     * @return \Abivia\NextForm\Renderer\Block
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
     * @param \Abivia\NextForm\Renderer\Attributes $attrs HTML attributes to associate with the element
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
     * @param \Abivia\NextForm\Renderer\Attributes $attrs
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

