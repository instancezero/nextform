<?php

namespace Abivia\NextForm\Data;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Text labels associated with a data object.
 *
 * Values default to null to allow inheritance form another level; an explicit blank
 * overwrites inheritance.
 */
class Labels implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Text to display after the body of an item.
     * @var string
     */
    public $after = null;

    /**
     * Text to display before the body of an item.
     * @var string
     */
    public $before = null;

    /**
     * Heading to use when asking for a confirmation.
     * @var string
     */
    public $confirm = null;

    /**
     * Text to display when there is an error.
     * @var string
     */
    public $error = null;

    /**
     * Text to display before as the item header.
     * @var string
     */
    public $heading = null;

    /**
     * Text to display for item help.
     * @var string
     */
    public $help = null;

    /**
     * Text to display "inside" an item: placeholder or label on a check/radio.
     * @var string
     */
    public $inner = null;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
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
     * Reference to the schema this property is part of.
     * @var Schema
     */
    protected $schema;

    /**
     * A list of the properties that contain text.
     * @var string[]
     */
    static private $textProperties = [
        'after', 'before', 'confirm', 'error', 'heading', 'help', 'inner'
    ];

    /**
     * Flag indicating if these labels should be translated or not.
     * @var bool
     */
    public $translate = true;

    protected function configureInitialize()
    {
        if (isset($this->configureOptions['_schema'])) {
            $this->schema = $this->configureOptions['_schema'];
        }
    }

    /**
     * Get a label by name
     * @param string $labelName
     * @return string
     * @throws \RuntimeException
     */
    public function get($labelName)
    {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        return $this->$labelName;
    }

    /**
     * Determine if a label type has been set or not.
     * @param string $labelName
     * @return bool
     * @throws \RuntimeException
     */
    public function has($labelName) : bool
    {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        return $this->$labelName !== null;
    }

    /**
     * Check to see if there are any non-null elements in the label.
     * @return bool
     */
    public function isEmpty() : bool
    {
        foreach (self::$textProperties as $prop) {
            if ($this->$prop !== null) {
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
    public function merge(Labels $merge = null)
    {
        $newLabels = clone $this;
        if ($merge !== null) {
            foreach (self::$textProperties as $prop) {
                if ($merge->$prop !== null) {
                    $newLabels->$prop = $merge->$prop;
                }
            }
            $newLabels->translate = $newLabels->translate || $merge->translate;
        }
        return $newLabels;
    }

    /**
     * Set a label by name
     * @param string $labelName
     * @param string $text
     * @return \self
     * @throws \RuntimeException
     */
    public function set($labelName, $text) : self
    {
        if (in_array($labelName, self::$textProperties)) {
            $this->$labelName = $text;
        } else {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        return $this;
    }

    /**
     * Create a translated version of the labels.
     * @param Translator $translator The translation facility.
     * @return \Abivia\NextForm\Data\Labels
     */
    public function translate(Translator $translator = null) : Labels
    {
        // Create a copy for the translated strings
        $newLabels = clone $this;

        // Merge in any schema defaults
        if ($this->schema) {
            $newLabels = $newLabels->merge($this->schema->getDefault('labels'));
        }

        // Perform the translation
        if ($newLabels->translate && $translator !== null) {
            foreach (self::$textProperties as $prop) {
                if ($newLabels->$prop !== null) {
                    $newLabels->$prop = $translator->trans($this->$prop);
                }
            }
        }
        $newLabels->translate = false;
        return $newLabels;
    }

}
