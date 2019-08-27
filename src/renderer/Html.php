<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm;
use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;

/**
 * A base for HTML rendering
 */
abstract class Html implements Renderer {

    protected $context = [];
    protected $contextStack = [];

    /**
     * Custom classes and styles to apply to various form elements
     * @var array
     */
    protected $custom = [];
    protected $customStack = [];

    /**
     * Types of <input> that we'll auto-generate a confirmation for
     * @var array
     */
    static $inputConfirmable = [
        'email', 'number', 'password', 'tel', 'text',
    ];
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
                'form' => '|button|button-group|default|no-label',
            ],
        ],
        'fill' => [
            'default' => 'solid',
            'validate' => [
                'form' => '|none|solid',
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

    protected function initialize() {
        // Reset the context
        $this -> context = [];
    }

    public function popContext() {
        if (count($this -> contextStack)) {
            $this -> context = array_pop($this -> contextStack);
            $this -> custom = array_pop($this -> customStack);
        }
    }

    public function pushContext() {
        array_push($this -> contextStack, $this -> context);
        array_push($this -> customStack, $this -> custom);
    }

    public function queryContext($selector) {
        if (!isset($this -> context[$selector])) {
            throw new RuntimeException($selector . ' is not valid in current context.');
        }
        return $this -> context[$selector];
    }

    abstract public function render(Element $element, $options = []);

    public function setOptions($options = []) {

    }

    /**
     * Convert a set of visual settings into rendering parameters.
     * @param array $settings
     */
    public function setShow($settings, $defaultScope = 'form') {
        if (is_string($settings)) {
            $settings = NextForm::tokenizeShow($settings, $defaultScope);
        }
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
            if ($valid) {
                $method = 'showDo' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this -> $method($scope, $choice, $args);
                }
            } else {
                throw new \RuntimeException(
                    'Invalid show setting: ' . $args[0] . ' is not valid for ' . $key
                );
            }
        } else {
            $method = 'showDo' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this -> $method($scope, $args[0], $args);
            }
        }
    }

    /**
     * Look for a matching show setting
     * @param string $scope The scope to be searched for a value.
     * @param array $selector a List of indexes into the value we want
     * @return mixed
     */
    protected function showFind($scope, $selector) {
        $descend = null;
        if (isset($this -> custom[$scope])) {
            $descend = $this -> custom[$scope];
            foreach ($selector as $key) {
                if (!isset($descend[$key])) {
                    return null;
                }
                $descend = $descend[$key];
            }
        }
        return $descend;
    }

    /**
     * Look for a show setting, falling back to the form if required.
     * @param string $scope The scope to be searched for a value.
     * @param array $selector a List of indexes into the value we want
     * @return mixed
     */
    protected function showFindAll($scope, $selector) {
        $descend = $this -> showFind($scope, $selector);
        if ($descend === null && $scope != 'form') {
            // Look for something specified at the form level
            $descend = $this -> showFind('form', $selector);
        }
        return $descend;
    }

    public function start($options = []) {
        $this -> initialize();
        $attrs = new Attributes;
        $attrs -> set('method', isset($options['method']) ? $options['method'] : 'post');
        $attrs -> setIfSet('action', $options);
        $attrs -> setIfSet('id', $options);
        $attrs -> setIfSet('name', $options);

        $pageData = new Block();
        $pageData -> body = $this -> writeTag('form', $attrs) . "\n";
        $pageData -> post = '</form>' . "\n";
        return $pageData;
    }

    /**
     * Write a label.
     * @param string $purpose A string indicating what kind of label this is.
     * @param string $text The text for the label
     * @param string $tag The kind of HTML tag to wrap the label in.
     * @param \Abivia\NextForm\Renderer\Attributes $attrs HTML attributes to associate with the element
     * @param type $options break(bool,''), div(string,classes)
     * @return string
     */
    protected function writeLabel($purpose, $text, $tag, $attrs = null, $options = []) {
        if ($text !== null) {
            $text = htmlspecialchars($text);
        } else {
            if ($this -> custom['form']['layout'] === 'horizontal' && $purpose === 'heading') {
                $text = '&nbsp;';
            } else {
                return '';
            }
        }
        if (isset($this -> custom['form'][$purpose])) {
            $attrs = $this -> custom['form'][$purpose] -> combine($attrs);
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
     * Conditionally write a wrapper element
     * @param \Abivia\NextForm\Renderer\Block $block
     * @param string $tag Name of the element to write (div, span, etc.)
     * @param string $purpose Name of the purpose of this tag
     * @param \Abivia\NextForm\Renderer\Attributes $attrs attributes to be attached to the wrapper
     * @param array $options Name(type,default): append(string,''), force(bool,false), scope(string,'form')
     * @return \Abivia\NextForm\Renderer\Block
     */
    protected function writeWrapper(Block $block, $tag, $purpose, $attrs = null, $options = []) {
        $hasPost = true;
        $scope = $options['scope'] ?? 'form';
        if (isset($this -> custom[$scope][$purpose])) {
            $attrs = $this -> custom[$scope][$purpose] -> combine($attrs);
            $block -> body .= $this -> writeTag($tag, $attrs) . "\n";
        } elseif ($attrs !== null && !$attrs -> empty()) {
            $block -> body .= $this -> writeTag($tag, $attrs) . "\n";
        } elseif (isset($options['force']) && $options['force']) {
            $block -> body .= '<' . $tag . ">\n";
        } else {
            $hasPost = false;
        }
        if ($hasPost) {
            $block -> post = '</' . $tag . ">\n"
                . (isset($options['append']) ? $options['append'] : '')
                . $block -> post;
        }
        return $block;
    }

}

