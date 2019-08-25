<?php

namespace Abivia\NextForm\Data;

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Text labels associated with a data object.
 *
 * Values default to null to allow inheritance form another level; an explicit blank
 * overwrites inheritance.
 */
class Labels implements \JsonSerializable{
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    /**
     * Text to display after the body of an item
     * @var string
     */
    public $after = null;
    public $before = null;
    public $confirm = null;
    public $error = null;
    public $heading = null;
    public $help = null;
    public $inner = null;
    static protected $jsonEncodeMethod = [
        'translate' => ['drop:true'],
        'after' => ['drop:null'],
        'before' => ['drop:null'],
        'confirm' => ['drop:null'],
        'error' => ['drop:null'],
        'heading' => ['drop:null'],
        'help' => ['drop:null'],
        'inner' => ['drop:null'],
    ];
    /**
     * A list of the properties that contain text.
     * @var array
     */
    static private $textProperties = [
        'after', 'before', 'confirm', 'error', 'heading', 'help', 'inner'
    ];
    public $translate = true;

    /**
     * Get a label by name
     * @param string $labelName
     * @return string
     * @throws \RuntimeException
     */
    public function get($labelName) {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        return $this -> $labelName;
    }

    public function has($labelName) {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        return $this -> $labelName !== null;
    }

    /**
     * Check to see if there are any non-null text labels.
     * @return bool
     */
    public function isEmpty() {
        foreach (self::$textProperties as $prop) {
            if ($this -> $prop !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Merge another label set into this one and return a new merged object.
     * @param \Abivia\NextForm\Data\Labels $merge
     * @return \Abivia\NextForm\Data\Labels
     */
    public function &merge(Labels $merge) {
        $newLabels = clone $this;
        foreach (self::$textProperties as $prop) {
            if ($merge -> $prop !== null) {
                $newLabels -> $prop = $merge -> $prop;
            }
        }
        $newLabels -> translate = $newLabels -> translate || $merge -> translate;
        return $newLabels;
    }

    /**
     * Set a label by name
     * @param string $labelName
     * @param string $text
     * @return $this
     * @throws \RuntimeException
     */
    public function set($labelName, $text) {
        if (in_array($labelName, self::$textProperties)) {
            $this -> $labelName = $text;
        } else {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        return $this;
    }

    /**
     * Create a translated version of the labels.
     * @param Translator $translate
     * @return \Abivia\NextForm\Data\Labels
     */
    public function translate(Translator $translate) {
        $newLabels = clone $this;
        if ($newLabels -> translate) {
            foreach (self::$textProperties as $prop) {
                if ($newLabels -> $prop !== null) {
                    $newLabels -> $prop = $translate -> trans($this -> $prop);
                }
            }
        }
        $newLabels -> translate = false;
        return $newLabels;
    }

}
