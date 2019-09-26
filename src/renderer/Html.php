<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm;
use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\FieldElement;

/**
 * A base for HTML rendering
 */
abstract class Html implements Renderer {
    use \Abivia\NextForm\Traits\Showable;

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
     * Default values and validation rules for show settings
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
        'fill' => [
            'default' => 'solid',
            'validate' => [
                'form' => '|outline|solid',
            ],
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

    public function __construct($options = []) {
        self::$showDefaultScope = 'form';
    }

    protected function elementHidden($element, $value) {
        $block = new Block;
        $baseId = $element -> getId();
        $formName = $element -> getFormName();
        $attrs = new Attributes('type', 'hidden');
        if ($element instanceof FieldElement) {
            $attrs -> setIfNotNull(
                '*data-sidecar',
                $element -> getDataProperty() -> getPopulation() -> sidecar
            );
        }
        if (is_array($value)) {
            $optId = 0;
            foreach ($value as $key => $entry) {
                $attrs -> set('id', $baseId . '-opt' . $optId);
                ++$optId;
                $attrs -> set('name', $formName . '[' . htmlspecialchars($key) . ']');
                $attrs -> set('value', $entry);
                $block -> body .= $this -> writeTag('input', $attrs) . "\n";
            }
        } else {
            $attrs -> set('id', $baseId);
            $attrs -> set('name', $formName);
            $attrs -> setIfNotNull('value', $value);
            $block -> body .= $this -> writeTag('input', $attrs) . "\n";
        }
        return $block;
    }

    /**
     * Generate hidden elements for an option list.
     * @param FieldElement $element The element we're generating for.
     * @return \Abivia\NextForm\Renderer\Block The output block.
     */
    protected function elementHiddenList(FieldElement $element) {
        $needEmpty = true;
        $block = new Block;
        $baseId = $element -> getId();
        $select = $element -> getValue();
        $list = $element -> getList(true);
        $attrs = new Attributes('type', 'hidden');
        $attrs -> set('name', $element -> getFormName() . (empty($list) ? '' : '[]'));
        if ($select === null) {
            $select = $element -> getDefault();
        }
        foreach ($list as $optId => $radio) {
            $optAttrs = $attrs -> copy();
            $id = $baseId . '-opt' . $optId;
            $optAttrs -> set('id', $id);
            $value = $radio -> getValue();
            $optAttrs -> set('value', $value);
            $optAttrs -> setIfNotNull('*data-sidecar', $radio -> sidecar);
            if (is_array($select)) {
                $checked = in_array($value, $select);
            } else {
                $checked = $value === $select;
            }
            if ($checked) {
                $block -> body .= $this -> writeTag('input', $optAttrs) . "\n";
                $needEmpty = false;
            }
        }
        if ($needEmpty) {
            $block = $this -> elementHidden($element, $select);
        }
        return $block;
    }

    protected function getRenderMethod(Element $element) {
        $classPath = get_class($element);
        if (!isset(self::$renderMethodCache[$classPath])) {
            $classParts = explode('\\', $classPath);
            self::$renderMethodCache[$classPath] = 'render' . array_pop($classParts);
        }
        return self::$renderMethodCache[$classPath];
    }

    /**
     * Generate attributes for a group container.
     * @param Element $element
     * @return \Abivia\NextForm\Renderer\Attributes
     */
    protected function groupAttributes($element, $options = []) : Attributes {
        $id = $options['id'] ?? $element -> getId();
        $container = new Attributes('id', $id . '-container');
        if (!$element -> getVisible()) {
            $container -> set('style', 'display:none');
        }
        $groups = $element -> getGroups();
        if (!empty($groups)) {
            $container -> set('*data-nf-group', $groups);
        }
        $container -> set('data-nf-for', $id);
        return $container;
    }

    protected function initialize() {
        // Reset the context
        $this -> context = [];
    }

    /**
     * Pop the rendering context
     */
    public function popContext() {
        if (count($this -> contextStack)) {
            $this -> context = array_pop($this -> contextStack);
            $this -> showState = array_pop($this -> showStack);
        }
    }

    /**
     * Push the rendering context
     */
    public function pushContext() {
        array_push($this -> contextStack, $this -> context);
        array_push($this -> showStack, $this -> showState);
    }

    public function queryContext($selector) {
        if (!isset($this -> context[$selector])) {
            throw new RuntimeException($selector . ' is not valid in current context.');
        }
        return $this -> context[$selector];
    }

    public function render(Element $element, $options = []) : Block {
        if (!isset($options['access'])) {
            $options['access'] = 'write';
        }
        if ($options['access'] === 'none') {
            return new Block;
        }
        $method = $this -> getRenderMethod($element);
        if (method_exists($this, $method)) {
            $result = $this -> $method($element, $options);
        } else {
            throw new \RuntimeException('Unable to render element ' . get_class($element));
        }
        return $result;
    }

    public function setOptions($options = []) {

    }

    /**
     * Convert a set of visual settings into rendering parameters.
     * @param string $settings
     */
    public function setShow($settings, $defaultScope = '') {
        $settings = self::showTokenize($settings, $defaultScope);
        foreach ($settings as $scope => $list) {
            foreach ($list as $key => $value) {
                $this -> show($scope, $key, $value);
            }
        }
    }

    /**
     * Process a show property, setting internal data structures as required.
     * @param string $key The name of the setting
     * @param array $args A list of arguments
     * @throws \RuntimeError
     */
    protected function show($scope, $key, $args) {
        if (!isset(self::$showRules[$key])) {
            throw new \RuntimeException(
                'Invalid show: ' . $key . ' is not recognized.'
            );
        }
        if (empty($args) && isset(self::$showRules[$key]['default'])) {
            $args[0] = self::$showRules[$key]['default'];
        }
        if (isset(self::$showRules[$key]['validate'][$scope])) {
            $rules = self::$showRules[$key]['validate'][$scope];
        } elseif (isset(self::$showRules[$key]['validate']['form'])) {
            $rules = self::$showRules[$key]['validate']['form'];
        } else {
            $rules = null;
        }
        if ($rules) {
            $valid = false;
            if (is_array($rules)) {
                // Keyword selection. Match the minimal unique subset for each option.
                foreach ($rules as $choice => $match) {
                    $matchParts = explode(':', $match);
                    if (preg_match($matchParts[0], $args[0])) {
                        $valid = true;
                        break;
                    }
                }
            } else {
                // Plain string match
                $choice = $args[0];
                $valid = strpos($rules, '|' . $choice) !== false;
            }
            if (!$valid) {
                throw new \RuntimeException(
                    'Invalid show setting: ' . $args[0] . ' is not valid for ' . $key
                );
            }
        } else {
            $choice = $args[0];
            $valid = true;
        }
        if ($valid) {
            // See if there's a method to process subsequent arguments,
            // if not, just store the setting in $choice
            $method = 'showDo' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this -> $method($scope, $choice, $args);
            } else {
                if (!isset($this -> showState[$scope])) {
                    $this -> showState[$scope] = [];
                }
                $this -> showState[$scope][$key] = $choice;
            }
        }
    }

    /**
     * Look for a show setting, falling back to the form if required.
     * @param string $scope The scope to be searched for a value.
     * @param string $key The index of the value we want.
     * @return mixed
     */
    protected function showGet($scope, $key) {

        if (($result = $this -> showGetLocal($scope, $key)) !== null) {
            return $result;
        }
        if ($scope !== 'form') {
            // Look for something specified at the form level
            if (($result = $this -> showGetLocal('form', $key)) !== null) {
                return $result;
            }
        }
        if (isset(self::$showRules[$key]['default'])) {
            $this -> showState['form'][$key] = self::$showRules[$key]['default'];
            return $this -> showState['form'][$key];
        }
        return null;
    }

    /**
     * Look for a matching show setting
     * @param string $scope The scope to be searched for a value.
     * @param string $key The index of the value we want.
     * @return mixed
     */
    protected function showGetLocal($scope, $key) {
        if (!isset($this -> showState[$scope])) {
            return null;
        }
        if (!isset($this -> showState[$scope][$key])) {
            return null;
        }
        return $this -> showState[$scope][$key];
    }

    /**
     * Start form generation
     * @param array $options @see NextForm
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function start($options = []) : Block {
        $this -> initialize();
        if (isset($options['attrs'])) {
            $attrs = $options['attrs'];
        } else {
            $attrs = new Attributes;
        }
        $attrs -> set('method', isset($options['method']) ? $options['method'] : 'post');
        $attrs -> setIfSet('action', $options);

        $pageData = new Block();
        $pageData -> body = $this -> writeTag('form', $attrs) . "\n";
        $pageData -> post = '</form>' . "\n";
        if (isset($options['token'])) {
            $pageData -> token = $options['token'];
        } else {
            $pageData -> token = bin2hex(random_bytes(32));
        }
        $nfToken = $options['tokenName'] ?? 'nf_token';
        if ($pageData -> token !== '') {
            $pageData -> body .= '<input id="' . $nfToken . '"'
                . ' name="' . $nfToken . '" type="hidden"'
                . ' value="' . $pageData -> token . '">' . "\n";
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
    protected function writeElement($tag, $options = []) {
        $hasPost = false;
        $attrs = $options['attrs'] ?? null;
        if (isset($options['show'])) {
            list($scope, $setting) = self::showGetSetting($options['show']);
        } else {
            $scope = false;
        }
        $block = new Block;
        if ($scope && isset($this -> showState[$scope][$setting])) {
            $attrs = $this -> showState[$scope][$setting] -> combine($attrs);
            $block -> body = $this -> writeTag($tag, $attrs) . "\n";
            $hasPost = true;
        } elseif ($attrs !== null && !$attrs -> isEmpty()) {
            $block -> body = $this -> writeTag($tag, $attrs) . "\n";
            $hasPost = true;
        } elseif ($options['force'] ?? false) {
            $block -> body = '<' . $tag . ">\n";
            $hasPost = true;
        }
        if ($hasPost) {
            $block -> post = '</' . $tag . ">\n"
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
    protected function writeLabel($purpose, $text, $tag, $attrs = null, $options = []) {
        if ($text === null) {
            // In horizontal layouts we always generate an element
            if (
                $this -> showState['form']['layout'] === 'horizontal'
                && $purpose === 'headingAttributes'
            ) {
                $text = '&nbsp;';
            } else {
                return '';
            }
        } else {
            $text = htmlspecialchars($text);
        }
        if (isset($this -> showState['form'][$purpose])) {
            $attrs = $this -> showState['form'][$purpose] -> combine($attrs);
        }
        $breakTag = $options['break'] ?? false;
        $html = $this -> writeTag($tag, $attrs)
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
    protected function writeTag($tag, $attrs = null, $text = null) {
        $html = '<' . $tag . ($attrs ? $attrs -> write($tag) : '');
        if (isset(self::$selfClose[$tag]) && $text === null) {
            $html .= '/>';
        } elseif ($text !== null) {
            $html .= '>' . htmlentities($text) . '</' . $tag . '>';
        } else {
            $html .= '>';
        }
        return $html;
    }

    /**
     * Conditionally write a wrapper element around an existing block
     * @param \Abivia\NextForm\Renderer\Block $block
     * @param string $tag Name of the element to write (div, span, etc.)
     * @param array $options Name(type,default): append(string,''), force(bool,false),
     *                      show(string,''), attrs(Attributes,null)
     * @return \Abivia\NextForm\Renderer\Block
     * @deprecated Replace with writeElement...
     */
    protected function writeWrapper(Block $block, $tag, $options = []) {
        $hasPost = false;
        $attrs = $options['attrs'] ?? null;
        if (isset($options['show'])) {
            list($scope, $setting) = self::showGetSetting($options['show']);
        } else {
            $scope = false;
        }
        if ($scope && isset($this -> showState[$scope][$setting])) {
            $attrs = $this -> showState[$scope][$setting] -> combine($attrs);
            $block -> body .= $this -> writeTag($tag, $attrs) . "\n";
            $hasPost = true;
        } elseif ($attrs !== null && !$attrs -> isEmpty()) {
            $block -> body .= $this -> writeTag($tag, $attrs) . "\n";
            $hasPost = true;
        } elseif ($options['force'] ?? false) {
            $block -> body .= '<' . $tag . ">\n";
            $hasPost = true;
        }
        if ($hasPost) {
            $block -> post = '</' . $tag . ">\n"
                . (isset($options['append']) ? $options['append'] : '')
                . $block -> post;
        }
        return $block;
    }

}

