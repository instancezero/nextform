<?php

Namespace Abivia\NextForm\Renderer;

/**
 *
 */
class Attributes {
    /**
     * What we need to join elements of some attributes (default is space delimited)
     * @var array
     */
    static protected $attrJoin = ['style' => ['; ', ":"]];

    protected $attrs = [];

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
     * This constructor sets up the static inputAttributes map on first call.
     * @param string $name Optional name of an initial value.
     * @param string $value Optional initial value.
     */
    public function __construct($name = null, $value = null) {
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
        if ($name !== null) {
            $this -> attrs[$name] = $value;
        }
    }

    /**
     * Add validation elements to an attributes list
     * @param string $type The input type we're generating
     * @param \Abivia\NextForm\Data\Validation $validation
     */
    public function addValidation($type, $validation) {
        foreach (self::$validationMap as $attrName => $specs) {
            list($lookup) = $this -> parseName($attrName);
            if (self::$inputAttributes[$type][$lookup]) {
                $setting = $validation -> get($specs[0]);
                if ($setting === $specs[1]) {
                    continue;
                }
                if ($lookup === 'accept') {
                    $this -> attrs[$attrName] = implode(',', $setting);
                } elseif (
                    ($lookup == 'min' || $lookup == 'max')
                    && isset(self::$inputDateTime[$type])
                ){
                    $this -> attrs[$attrName] = date(self::$inputDateTime[$type], strtotime($setting));
                } else {
                    $this -> attrs[$attrName] = $setting;
                }
            }
        }
    }

    /**
     * Combine attributes into this set and return a copy.
     * @param \Abivia\NextForm\Renderer\Attributes $source Application settings.
     * @return \Abivia\NextForm\Renderer\Attributes New object with merged attributes.
     */
    public function combine($source = null) : Attributes {
        $result = clone $this;
        $result -> merge($source);
        return $result;
    }

    /**
     * Make a (deep) copy of this object
     * @return \Abivia\NextForm\Renderer\Attributes
     */
    public function copy() : Attributes {
        return clone $this;
    }

    /**
     * Delete an attribute
     * @param type $name Name of the attribute to be removed.
     * @return \self
     */
    public function delete($name) : self {
        unset($this -> attrs[$name]);
        return $this;
    }

    /**
     * Flatten an array-valued attribute to a string using rules for a named attribute.
     * @param string $attrName Attribute rules to use.
     * @param array $value
     * @return string
     */
    protected function flatten($attrName, $value) {
        if (!is_array($value)) {
            return $value;
        }
        $result = '';
        if (isset(self::$attrJoin[$attrName])) {
            $glue = self::$attrJoin[$attrName];
            if (isset($glue[1])) {
                foreach ($value as $key => &$entry) {
                    $entry = $key . $glue[1] . $entry;
                }
            }
            $result = implode($glue[0], $value);
        } else {
            $result = implode(' ', $value);
        }
        return $result;
    }

    /**
     * Get an attribute's value.
     * @param string $name Name of the atribute to retrieve.
     * @return mixed Attribute value.
     */
    public function get($name) {
        return $this -> attrs[$name];
    }

    /**
     * Get a reference to an attribute.
     * @param string $name Name of the attribute to retrieve.
     * @return mixed Attribute value.
     */
    public function &getReference($name) {
        return $this -> attrs[$name];
    }

    /**
     * Get an array containing all the attributes.
     * @return array
     */
    public function getAll() {
        return $this -> attrs;
    }

    /**
     * Check to see if an attribute is set; the attribute may still be empty.
     * @param string $name Name of the attribute to check.
     * @return bool True if the attribute exists.
     */
    public function has($name) : bool{
        return isset($this -> attrs[$name]);
    }

    /**
     * Check to see if an attribute is premitted to be written in context.
     * @param string $name The nem of the attribute to check.
     * @param array $mask Allowable flags, boolean indexed by attribute name.
     * @return boolean
     */
    protected function include($name, $mask) {
        $prefix = substr($name, 0, 5);
        if ($prefix === 'aria-') {
            return true;
        }
        if ($prefix === 'data-') {
            return true;
        }
        return (isset($mask[$name]) && $mask[$name]);
    }

    /**
     * Check to see if an input type has an attribute in its element definition.
     * @param string $type The input type (extended to select, textarea...).
     * @param string $name The attribute name.
     * @return bool
     */
    static function inputHas($type, $name) : bool {
        if (!isset(self::$inputAttributes[$type])) {
            return false;
        }
        if (!isset(self::$inputAttributes[$type][$name])) {
            return false;
        }
        return self::$inputAttributes[$type][$name];
    }

    /**
     * Determine if there are any attributes set.
     * @return bool
     */
    public function isEmpty() {
        return empty($this -> attrs);
    }

    /**
     * Add an element to the named attribute.
     * @param string $name Name of the attribute to operate on.
     * @param mixed $value The value to be appended.
     * @return \self
     */
    public function itemAppend($name, $value) : self {
        if (!isset($this -> attrs[$name])) {
            $this -> attrs[$name] = [];
        } elseif(!is_array($this -> attrs[$name])) {
            $this -> attrs[$name] = [$this -> attrs[$name]];
        }
        $this -> attrs[$name][] = $value;
        return $this;
    }

    /**
     * Remove one or more items from an attribute.
     * @param string $name Name of the attribute to operate on.
     * @param array $items Items to delete. If associative, keys are used for removal.
     * @return \self
     */
    public function itemDelete($name, $items) : self {
        if (!isset($this -> attrs[$name]) || !is_array($this -> attrs[$name])) {
            return $this;
        }
        // Determine if this is an associative array or not
        if (count(array_filter(array_keys($items), 'is_string')) > 0) {
            // Delete all matching keys
            foreach(array_keys($items) as $key) {
                unset($this -> attrs[$name][$key]);
            }
        } else {
            // Delete all matching values
            foreach ($items as $value) {
                if (($key = array_search($value, $this -> attrs[$name]))) {
                    unset($this -> attrs[$name][$key]);
                }
            }
        }
        return $this;
    }

    /**
     * Remove one or more items from an attribute by key.
     * @param string $name Name of the attribute to operate on.
     * @param string|array $keys The array key(s) to be removed.
     * @return \self
     */
    public function itemDeleteKey($name, $keys) : self {
        if (!isset($this -> attrs[$name]) || !is_array($this -> attrs[$name])) {
            return $this;
        }
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        // Delete all matching keys
        foreach($keys as $key) {
            unset($this -> attrs[$name][$key]);
        }
        return $this;
    }

    /**
     * Uniquely insert one or more items into the named attribute.
     * @param string $name Name of the attribute to operate on.
     * @param mixed $items One or more items to merge, scalar, array, or associative array.
     * @return \self
     */
    public function itemInsert($name, $items) : self {
        if (!isset($this -> attrs[$name])) {
            $this -> attrs[$name] = [];
        } elseif(!is_array($this -> attrs[$name])) {
            $this -> attrs[$name] = [$this -> attrs[$name]];
        }
        if (!is_srray($items)) {
            $items = [$items];
        }
        $this -> attrs[$name] = array_unique(array_merge($this -> attrs[$name], $items));

        return $this;
    }

    /**
     * Merge attributes into this set.
     * @param \Abivia\NextForm\Renderer\Attributes $source Application settings.
     * @return \self
     */
    public function merge($source = null) : self {
        if ($source === null) {
            return $this;
        }
        $merge = $source -> getAll();
        foreach ($merge as $name => $list) {
            if (isset($this -> attrs[$name])) {
                if (is_array($this -> attrs[$name]) || is_array($list)) {
                    // Make sure both components are arrays
                    if (!is_array($this -> attrs[$name])) {
                        $this -> attrs[$name] = [$this -> attrs[$name]];
                    }
                    if (!is_array($list)) {
                        $list = [$list];
                    }
                    $this -> attrs[$name] = array_merge($this -> attrs[$name], $list);
                } else {
                    $this -> attrs[$name] .= (isset(self::$attrJoin[$name])
                            ? self::$attrJoin[$name][0] : ' '
                        )
                        . $list;
                }
            } else {
                $this -> attrs[$name] = $list;
            }
        }
        return $this;
    }

    /**
     * Extract a processing command (! no escape; = no value; * JSON encode) from an attribute, if any
     * @param string $name The attribute command and name
     * @return array Attribute name in the first element, command (or '') in the second.
     */
    protected function parseName($name) {
        if (strpos('!=*', $name[0]) !== false) {
            $cmd = $name[0];
            $name = substr($name, 1);
        } else {
            $cmd = '';
        }
        return [$name, $cmd];
    }

    /**
     * Set an attribute value.
     * @param string $name Name of the attribute to set.
     * @param mixed $value Value to set the attribute to.
     * @return \self
     */
    public function set($name, $value) : self {
        $this -> attrs[$name] = $value;
        return $this;
    }

    /**
     * Set a flag-valued attribute.
     * @param string $name Name of the flag to set.
     * @return \self
     */
    public function setFlag($name, bool $value = true) : self {
        if ($value) {
            $this -> attrs['=' . $name] = $name;
        } else {
            unset($this -> attrs['=' . $name]);
        }
        return $this;
    }

    /**
     * Set a flag-valued attribute if the value is not null.
     * @param string $name Name of the attribute to set.
     * @param mixed $value Value to set the attribute to.
     * @return \self
     */
    public function setIfNotNull($name, $value) : self {
        if ($value !== null) {
            $this -> attrs[$name] = $value;
        }
        return $this;
    }

    /**
     * Set an attribute if a source array has a specified index.
     * @param string $name Name of the attribute to set.
     * @param array $source The data source array
     * @param string $key Optional index into source; if omitted, $name is used.
     * @return \self
     */
    public function setIfSet($name, $source, $key = null) : self {
        if ($key === null) {
            $key = $name;
        }
        if (isset($source[$key])) {
            $this -> attrs[$name] = $source[$key];
        }
        return $this;
    }

    /**
     * Encode an attribute into escaped HTML
     * @param string $name The attribute name with optional processing command.
     * @param string $value The attribute value.
     * @return string
     */
    protected function toHtml($name, $cmd, $value) {
        // was writeattribute()
        switch ($cmd) {
            case '!': {
                // Attrribute that does not need to be escaped
                $html = ' ' . $name . '="' . $this -> flatten($name, $value) . '"';
            }
            break;

            case '*': {
                // JSON-endoced attribute
                $html = ' ' . $name . '="' . htmlspecialchars(json_encode($value)) . '"';
            }
            break;

            case '=': {
                // Stand-alone attribute with no value
                $html = ' ' . $name;
            }
            break;

            default: {
                $html = ' ' . $name . '="'
                    . htmlspecialchars($this -> flatten($name, $value))
                    . '"';
            }
            break;

        }
        return $html;
    }

    /**
     * Filter, arrange, and write attributes into escaped HTML
     * @param string $tag
     * @return string
     */
    public function write($tag) {
        $mask = $tag === 'input' ? self::$inputAttributes[$this -> attrs['type']] : null;
        // Convert all the attributes to HTML, using mask as a filter
        $parts = [];
        foreach ($this -> attrs as $attrName => $value) {
            // For input elements, only write the allowed attributes
            list($lookup, $cmd) = $this -> parseName($attrName);
            if ($mask === null || $this -> include($lookup, $mask)) {
                $parts[$lookup] = $this -> toHtml($lookup, $cmd, $value);
            }
        }
        $html = '';
        foreach (self::$highlightAttribute as $attrName) {
            if (isset($parts[$attrName])) {
                $html .= $parts[$attrName];
                unset($parts[$attrName]);
            }
        }
        $html .= implode('', $parts);
        return $html;
    }

}