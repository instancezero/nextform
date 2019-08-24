<?php
namespace Abivia\NextForm\Renderer;

use Abivia\NextForm;
use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Element\Element;

/**
 * A base for HTML rendering
 */
abstract class Html implements Renderer {

    /**
     * What we need to join elements of some attributes
     * @var array
     */
    static protected $attrJoin = ['class' => [' '], 'style' => ['; ', ":"]];

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
     * Initial settings for the show attributes
     * @var array
     */
    static protected $showDefault = [
        'size:regular',
        'fill:solid',
        'purpose:primary',
    ];
    /**
     * Default values for the show settings
     * @var array
     */
    static public $showRules = [
        'fill' => ['solid'],
        'layout' => ['vertical'],
        'purpose' => ['primary'],
        'size' => ['regular'],
    ];
    /**
     * Keyword matching for the show settings; regex:method
     * @var array
     */
    static $showValidate = [
        'layout' => [
            'horizontal' => '/h/', 'vertical' => '/v/', 'inline' => '/i/'
        ],
        'size' => [
            'large' => '/l/', 'regular' => '/[mr]/', 'small' => '/s/'
        ],
    ];

    protected function initialize() {
        // Reset the context
        $this -> context = [];
    }

    /**
     * Merge HTML element attributes into attributes from the custom settings using rules in attrJoin.
     * @param array $custom Arrays of custom settings indexed by attribute
     * @param type $attrs Application settings indexed by attribute or a string
     * @return array Merged attributes
     */
    protected function mergeCustom($custom, $attrs = []) {
        foreach ($custom as $attr => $list) {
            $glue = self::$attrJoin[$attr];
            if (isset($glue[1])) {
                // Start by merging arrays
                $merged = [];
                if (isset($attrs[$attr])) {
                    $append = is_string($attrs[$attr]);
                    if (!$append) {
                        $list = array_merge($list, $attrs[$attr]);
                    }
                } else {
                    $append = false;
                }
                // Both lists should be associative, merge keys into the values
                foreach ($list as $term => &$value) {
                    $merged[] = $term . $glue[1] . $value;
                }
                if ($append) {
                    $merged[] = $attrs[$attr];
                }
            } else {
                // Start by merging arrays
                if (isset($attrs[$attr])) {
                    if (is_string($attrs[$attr])) {
                        $list[] = $attrs[$attr];
                    } else {
                        $list = array_merge($list, $attrs[$attr]);
                    }
                }
                // Reverse, de-dup, reverse
                $merged = array_reverse(array_merge(array_reverse($list)));
            }
            // Finally, implode to a string
            if (!empty($list)) {
                $attrs[$attr] = implode($glue[0], $merged);
            }
        }
        return $attrs;
    }

    protected function mergeShow($scope, $selector) {
        if (isset($this -> custom[$scope])) {
            $descend = $this -> custom[$scope];
            foreach ($selector as $key) {
                if (!isset($descend[$key])) {
                    return null;
                }
                $descend = $descend[$key];
            }
        } elseif ($scope !== 'form') {
            $descend = $this -> mergeShow('form', $selector);
        }
        return $descend;
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
        $valid = false;
        switch ($key) {
            case 'layout':
            case 'size':
                // Keyword selection. Match the minimal unique subset for each option.
                foreach (self::$showValidate[$key] as $choice => $match) {
                    $matchParts = explode(':', $match);
                    if (preg_match($matchParts[0], $args[0])) {
                        $method = 'showDo' . ucfirst($key);
                        if (method_exists($this, $method)) {
                            $this -> $method($scope, $choice, $args);
                        }
                        $valid = true;
                        break;
                    }
                }
                if (!$valid) {
                    throw new \RuntimeException(
                        'Invalid show setting: ' . $args[0] . ' is not valid for ' . $key
                    );
                }
                break;
            default:
                if (!isset(self::$showRules[$key])) {
                    throw new \RuntimeException(
                        'Invalid show: ' . $key . ' is not recognized.'
                    );
                }
                $method = 'showDo' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this -> $method($scope, $args[0], $args);
                }
                $valid = true;
                break;
        }
    }

    public function start($options = []) {
        $this -> initialize();
        $attrs = ['method' => isset($options['method']) ? $options['method'] : 'post'];
        if (isset($options['action'])) {
            $attrs['action'] = $options['action'];
        }
        if (isset($options['id'])) {
            $attrs['id'] = $options['id'];
        }
        if (isset($options['name'])) {
            $attrs['name'] = $options['name'];
        }
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
            $attrs = $this -> mergeCustom($this -> custom['form'][$purpose], $attrs);
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
        $html = '<' . $tag . $attrs -> write();
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
            $attrs = $this -> mergeCustom($this -> custom[$scope][$purpose], $attrs);
            $block -> body .= $this -> writeTag($tag, $attrs) . "\n";
        } elseif ($attrs !== null) {
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

