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
     * What we just to join elements of some attributes
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
     * HTML attributes that we give preference to when generating
     * @var array
     */
    static $highlightAttribute = [
        'id', 'name', 'type', 'for', 'class', 'style', 'value', 'min', 'max'
    ];
    /**
     * Attribute masks for <input> elements. This array has to be initialized by a constructor.
     * @var array
     */
    static $inputAttributes = [
        '*' => [
            'autocomplete' => true, 'autofocus' => true,
            'dirname' => true, 'disabled' => true, 'form' => true,
            'name' => true, 'readonly' => true, 'type' => true, 'value' => true,
            // Globals
            'accesskey' => true, 'class' => true, 'contenteditable' => true,
            'dir' => true, 'draggable' => true, 'dropzone' => true,
            'id' => true, 'lang' => true,
            'spellcheck' => true, 'style' => true, 'tabindex' => true, 'title' => true,
            'translate' => true,
        ],
        'button' => [],
        'checkbox' => ['checked' => true, 'required' => true, ],
        'color' => [],
        'date' => ['max' => true, 'min' => true, 'pattern' => true, 'step' => true, ],
        'datetime-local' => ['max' => true, 'min' => true, 'required' => true, 'step' => true, ],
        'email' => [
            'list' => true, 'multiple' => true, 'pattern' => true,
            'placeholder' => true, 'required' => true, 'size' => true,
        ],
        'file' => [
            'accept' => true, 'capture' => true, 'multiple' => true,
            'readonly' => false, 'required' => true, 'value' => false
        ],
        'hidden' => [
            'dirname' => false, 'readonly' => false, 'required' => false
        ],
        'image' => [
            'alt' => true, 'formaction' => true, 'formenctype' => true,
            'formmethod' => true, 'formtarget' => true, 'height' => true,
            'src' => true, 'width' => true,
        ],
        'month' => ['max' => true, 'min' => true, 'required' => true, 'step' => true, ],
        'number' => [
            'list' => true, 'max' => true, 'maxlength' => true, 'min' => true,
            'required' => true, 'step' => true,
        ],
        'password' => [
            'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true, 'required' => true, 'size' => true,
        ],
        'radio' => ['checked' => true, 'required' => true, ],
        'range' => ['max' => true, 'min' => true, 'step' => true, ],
        'reset' => [],
        'search' => [
            'list' => true, 'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        // Select isn't an input type but it shares attributes
        'select' => [
            'multiple' => true, 'readonly' => false, 'required' => true, 'rows' => true,
            'value' => false
        ],
        'submit' => [
            'formaction' => true, 'formenctype' => true, 'formmethod' => true,
            'formtarget' => true,
        ],
        'tel' => [
            'list' => true, 'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'text' => [
            'list' => true, 'maxlength' => true, 'minlength' => true,
            'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        // Textarea isn't an input type but it shares attributes
        'textarea' => [
            'cols' => true, 'list' => true, 'maxlength' => true, 'minlength' => true,
            'placeholder' => true, 'required' => true, 'rows' => true,
        ],
        'time' => ['max' => true, 'min' => true, 'step' => true, ],
        'url' => [
            'list' => true, 'pattern' => true, 'placeholder' => true,
            'required' => true, 'size' => true,
        ],
        'week' => ['max' => true, 'min' => true, 'required' => true, 'step' => true, ],
    ];
    /**
     * Types of <input> that we'll auto-generate a confirmation for
     * @var array
     */
    static $inputConfirmable = [
        'email', 'number', 'password', 'tel', 'text',
    ];
    /**
     * Date formats for various date/time input controls.
     * @var aray
     */
    static $inputDateTime = [
        'date' => 'Y-m-d',
        'datetime-local' => 'Y-m-d\TH:i',
        'month' => 'Y-m',
        'time' => 'H:i',
        'week' => 'Y-\WW',
    ];
    /**
     * Quick lookup for self-closing elements
     * @var array
     */
    static $selfClose = ['input' => 1, 'option' => 2];
    /**
     * Map validation-related attributes to properties in a Data\Validation object.
     * @var array
     */
    static $validationMap = [
        'accept' => ['accept', []],
        'maxlength' => ['maxLength', null],
        'minlength' => ['minLength', null],
        'max' => ['maxValue', null],
        'min' => ['minValue', null],
        '=multiple' => ['multiple', false],
        'pattern' => ['-pattern', ''],
        '=required' => ['required', false],
        'step' => ['step', null],
    ];

    /**
     * This constructor must be called once before the static inputAttributes map works.
     * @param array $options
     */
    public function __construct($options = []) {
        // Build a non-sparse input attribute matrix
        if (isset(self::$inputAttributes['*'])) {
            // Merge all attributes into the common defaults
            $common = self::$inputAttributes['*'];
            unset(self::$inputAttributes['*']);
            foreach (self::$inputAttributes as $attrs) {
                foreach (array_keys($attrs) as $attrName) {
                    if (!isset($common[$attrName])) {
                        $common[$attrName] = false;
                    }
                }
            }
            ksort($common);
            // Overwrite the defaults for each input type
            foreach (self::$inputAttributes as &$attrs) {
                $attrs = array_merge($common, $attrs);
            }
        }
    }

    /**
     * Add validation elements to an attributes list
     * @param array $attrs The attribute list
     * @param string $type The input type we're generating
     * @param \Abivia\NextForm\Data\Validation $validation
     */
    protected function addValidation(&$attrs, $type, $validation) {
        foreach (self::$validationMap as $attrName => $specs) {
            list($lookup) = $this -> parseAttribute($attrName);
            if (self::$inputAttributes[$type][$lookup]) {
                $setting = $validation -> get($specs[0]);
                if ($setting === $specs[1]) {
                    continue;
                }
                if ($lookup === 'accept') {
                    $attrs[$attrName] = implode(',', $setting);
                } elseif (
                    ($lookup == 'min' || $lookup == 'max')
                    && isset(self::$inputDateTime[$type])
                ){
                    $attrs[$attrName] = date(self::$inputDateTime[$type], strtotime($setting));
                } else {
                    $attrs[$attrName] = $setting;
                }
            }
        }
    }

    protected function initialize() {
        // Reset the context
        $this -> context = [];
    }

    protected function mergeCustom($custom, $attrs = []) {
        foreach ($custom as $attr => $list) {
            $glue = self::$attrJoin[$attr];
            if (isset($glue[1])) {
                // Merge keys from the list into the values
                foreach ($list as $term => &$value) {
                    $value = $term . $glue[1] . $value;
                }
            }
            if (isset($attrs[$attr])) {
                $list[] = $attrs[$attr];
            }
            if (!empty($list)) {
                $attrs[$attr] = implode($glue[0], $list);
            }
        }
        return $attrs;
    }

    /**
     * Extract a processing command (! no escape; = no value; * JSON encode) from an attribute, if any
     * @param string $attrName The attribute command and name
     * @return array Attribute name in the first element, command (or '') in the second.
     */
    protected function parseAttribute($attrName) {
        if (strpos('!=*', $attrName[0]) !== false) {
            $cmd = $attrName[0];
            $attrName = substr($attrName, 1);
        } else {
            $cmd = '';
        }
        return [$attrName, $cmd];
    }

    public function popContext(Block $block, $options = []) {
        if (count($this -> contextStack) > 1) {
            $this -> context = array_pop($this -> contextStack);
            $this -> custom = array_pop($this -> customStack);
        }
    }

    public function pushContext($options = []) {
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
    public function setShow($settings) {
        if (is_string($settings)) {
            $settings = NextForm::tokenizeShow($settings);
        }
        foreach ($settings as $key => $value) {
            $this -> show($key, $value);
        }
    }

    /**
     * Process a show property, setting internal data structures as required.
     * @param string $key The name of the setting
     * @param array $args A list of arguments
     * @throws \RuntimeError
     */
    protected function show($key, $args) {
        $valid = false;
        switch ($key) {
            case 'layout':
            case 'size':
                // Keyword selection. Match the minimal unique subset for each option.
                foreach (NextForm::$showValidate[$key] as $choice => $match) {
                    $matchParts = explode(':', $match);
                    if (preg_match($matchParts[0], $args[0])) {
                        $method = 'showDo' . ucfirst($key);
                        if (method_exists($this, $method)) {
                            $this -> $method($choice, $args);
                        }
                        $valid = true;
                        break;
                    }
                }
                break;
            default:
                $valid = isset(NextForm::$showRules[$key]);
                break;
        }
        if (!$valid) {
            throw new \RuntimeException($args[0] . ' is not a valid setting for ' . $key);
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
     * Encode an attribute into escaped HTML
     * @param string $atrName The attribute name with optional processing command.
     * @param string $value The attribute value.
     * @return string
     */
    protected function writeAttribute($attrName, $cmd, $value) {
        switch ($cmd) {
            case '!': {
                // Attrribute that does not need to be escaped
                $html = ' ' . $attrName . '="' . $value . '"';
            }
            break;

            case '*': {
                // JSON-endoced attribute
                $html = ' ' . $attrName . '="' . htmlspecialchars(json_encode($value)) . '"';
            }
            break;

            case '=': {
                // Stand-alone attribute with no value
                $html = ' ' . $attrName;
            }
            break;

            default: {
                $html = ' ' . $attrName . '="' . htmlspecialchars($value) . '"';
            }
            break;

        }
        return $html;
    }

    protected function writeLabel($purpose, $text, $tag, $attrs = []) {
        if ($text !== null) {
            $text = htmlspecialchars($text);
        } else {
            if ($this -> custom['layout'] === 'horizontal' && $purpose === 'heading') {
                $text = '&nbsp;';
            } else {
                return '';
            }
        }
        if (isset($this -> custom[$purpose])) {
            $attrs = $this -> mergeCustom($this -> custom[$purpose], $attrs);
        }
        $html = $this -> writeTag($tag, $attrs)
            . $text
            . '</' . $tag . '>' . ($tag === 'span' ? '' : "\n")
        ;
        return $html;
    }

    /**
     * Write an element and attributes into escaped HTML
     * @param array $attrs
     * @return string
     */
    protected function writeTag($tag, $attrs = [], $text = null) {
        $html = '<' . $tag;
        $parts = [];
        if ($tag === 'input') {
            $mask = self::$inputAttributes[$attrs['type']];
            foreach ($attrs as $attrName => $value) {
                // For input elements, only write the allowed attributes
                list($lookup, $cmd) = $this -> parseAttribute($attrName);
                if (
                    (isset($mask[$lookup]) && $mask[$lookup])
                    || substr($lookup, 0, 5) === 'data-'
                ) {
                    $parts[$lookup] = $this -> writeAttribute($lookup, $cmd, $value);
                }
            }
        } else {
            foreach ($attrs as $attrName => $value) {
                list($lookup, $cmd) = $this -> parseAttribute($attrName);
                $parts[$lookup] = $this -> writeAttribute($lookup, $cmd, $value);
            }
        }
        foreach (self::$highlightAttribute as $attrName) {
            if (isset($parts[$attrName])) {
                $html .= $parts[$attrName];
                unset($parts[$attrName]);
            }
        }
        $html .= implode('', $parts);
        if (isset(self::$selfClose[$tag]) && $text === null) {
            $html .= '/>';
        } elseif ($text !== null) {
            $html .= '>' . htmlentities($text) . '</' . $tag . '>';
        } else {
            $html .= '>';
        }
        return $html;
    }

    protected function writeWrapper(Block $block, $tag, $purpose, $options = []) {
        $hasPost = true;
        if (isset($this -> custom[$purpose])) {
            $attrs = $this -> mergeCustom($this -> custom[$purpose]);
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

