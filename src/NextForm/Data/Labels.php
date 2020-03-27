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
     * Text to display when validation passes.
     * @var string
     */
    public $accept = null;

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
     * Labels to use when asking for a confirmation.
     * @var Labels
     */
    protected $confirm = null;

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
        'accept' => ['drop:null'],
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
        'accept', 'after', 'before', 'error', 'heading', 'help', 'inner',
    ];

    /**
     * Flag indicating if these labels should be translated or not.
     * @var bool
     */
    public $translate = true;

    /**
     * Label factory.
     *
     * @return Labels
     */
    static public function build() : Labels
    {
        return new Labels();
    }

    /**
     * Map a property to a class.
     * @param string $property The current class property name.
     * @param mixed $value The value to be stored in the property, made available for inspection.
     * @return mixed An object containing a class name and key, or false
     * @codeCoverageIgnore
     */
    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'confirm' => ['className' => self::class],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    protected function configureInitialize(&$config, ...$context)
    {
        if (isset($this->configureOptions['_schema'])) {
            $this->schema = $this->configureOptions['_schema'];
        }
        if (\is_string($config)) {
            // Convert to a class with heading
            $obj = new \stdClass;
            $obj->heading = $config;
            $config = $obj;
        }
        return true;
    }

    protected function configureComplete()
    {
        if ($this->confirm) {
            if ($this->confirm->confirm !== null) {
                $this->configureLogError(
                    "Label confirm strings can't be nested."
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Get the labels merged for a confirm context.
     *
     * @return \Abivia\NextForm\Data\Labels
     */
    public function forConfirm() {
        $newLabels = clone $this;
        if ($newLabels->confirm !== null) {
            foreach (self::$textProperties as $prop) {
                if ($newLabels->confirm->has($prop)) {
                    $newLabels->$prop = $newLabels->confirm->get($prop);
                }
            }
            $newLabels->confirm = null;
        }
        return $newLabels;
    }

    /**
     * Get a label by name.
     *
     * @param string $labelName
     * @param bool $asConfirm When set, get the "confirm" version (if any).
     * @return ?string
     * @throws \RuntimeException
     */
    public function get($labelName, $asConfirm = false)
    {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        if ($asConfirm && $this->confirm && $this->confirm->$labelName != null) {
            return $this->confirm->$labelName;
        }
        return $this->$labelName;
    }

    /**
     * Determine if a label type has been set or not.
     * @param string $labelName
     * @param bool $asConfirm When set, check the "confirm" version.
     * @return bool
     * @throws \RuntimeException
     */
    public function has($labelName, $asConfirm = false) : bool
    {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        if ($asConfirm) {
            if (!$this->confirm) {
                return false;
            }
            return $this->confirm->$labelName !== null;
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
     * If we can represent this field in JSON as a string, return a string
     * otherwise $this.
     */
    public function jsonCollapse()
    {
        if ($this->confirm !== null) {
            return $this;
        }
        $collapsable = true;
        foreach (self::$textProperties as $prop) {
            if ($prop === 'heading') {
                continue;
            }
            if ($this->$prop !== null) {
                $collapsable = false;
                break;
            }
        }
        if (!$collapsable) {
            return $this;
        }
        return $this->heading;
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
            if ($newLabels->confirm === null) {
                $newLabels->confirm = $merge->confirm;
            } else {
                $newLabels->confirm = $newLabels->confirm->merge(
                    $merge->confirm
                );
            }
            $newLabels->translate = $newLabels->translate || $merge->translate;
        }
        return $newLabels;
    }

    /**
     * Set a label by name
     * @param string $labelName
     * @param string $text
     * @return $this
     * @throws \RuntimeException
     */
    public function set($labelName, $text, $asConfirm = false)
    {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException($labelName . ' isn\'t a valid label property.');
        }
        if ($asConfirm) {
            if ($this->confirm === null) {
                $this->confirm = new Labels();
            }
            $this->confirm->set($labelName, $text);
        } else {
            $this->$labelName = $text;
        }

        return $this;
    }

    /**
     * Create a translated version of the labels.
     * @param Translator $translator The translation facility.
     * @return \Abivia\NextForm\Data\Labels
     */
    public function translate(?Translator $translator = null) : Labels
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
                if (is_array($newLabels->$prop)) {
                    foreach($newLabels->$prop as &$entry) {
                        $entry = $translator->get($entry);
                    }
                } elseif ($newLabels->$prop !== null) {
                    $newLabels->$prop = $translator->get($newLabels->$prop);
                }
            }
            if ($this->confirm) {
                $newLabels->confirm = $this->confirm->translate($translator);
            }
        }
        $newLabels->translate = false;
        return $newLabels;
    }

}
