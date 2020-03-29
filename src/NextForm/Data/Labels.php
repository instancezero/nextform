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
    protected $accept = null;

    /**
     * Text to display after the body of an item.
     * @var string
     */
    protected $after = null;

    /**
     * Text to display before the body of an item.
     * @var string
     */
    protected $before = null;

    /**
     * Labels to use when asking for a confirmation.
     * @var Labels
     */
    protected $confirm = null;

    /**
     * Text to display when there is an error.
     * @var string
     */
    protected $error = null;

    /**
     * Text to display before as the item header.
     * @var string
     */
    protected $heading = null;

    /**
     * Text to display for item help.
     * @var string
     */
    protected $help = null;

    /**
     * Text to display "inside" an item: placeholder or label on a check/radio.
     * @var string
     */
    protected $inner = null;

    /**
     * Flags indicating if label elements are escaped HTML or not.
     * @var array
     */
    protected $isHtml = [];

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'translate' => ['drop:true'],
        'accept' => ['drop:null'],
        'accept.html' => ['method:jsonEscape', 'drop:false'],
        'after' => ['drop:null'],
        'after.html' => ['method:jsonEscape', 'drop:false'],
        'before' => ['drop:null'],
        'before.html' => ['method:jsonEscape', 'drop:false'],
        'confirm' => ['drop:null'],
        'error' => ['drop:null'],
        'error.html' => ['method:jsonEscape', 'drop:false'],
        'heading' => ['drop:null'],
        'heading.html' => ['method:jsonEscape', 'drop:false'],
        'help' => ['drop:null'],
        'help.html' => ['method:jsonEscape', 'drop:false'],
        'inner' => ['drop:null'],
        'inner.html' => ['method:jsonEscape', 'drop:false'],
    ];

    /**
     * Substitution parameters, indexed by match string.
     * @var array
     */
    protected $replacements = [];

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
    protected $translate = true;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Label factory.
     *
     * @return Labels
     */
    static public function build() : Labels
    {
        return new Labels();
    }

    protected function checkProperty($labelName)
    {
        if (!in_array($labelName, self::$textProperties)) {
            throw new \RuntimeException(
                "$labelName isn't a valid label property."
            );
        }
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
        $this->init();
        if (isset($this->configureOptions['_schema'])) {
            $this->schema = $this->configureOptions['_schema'];
        }

        // Convert a simple string to a class with heading
        if (\is_string($config)) {
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

    protected function configurePropertyMap($property)
    {
        $parts = explode('.', $property);
        if (count($parts) === 1) {
            return $property;
        }
        return ['isHtml', $parts[0]];
    }

    /**
     * Get the labels merged for a confirm context.
     *
     * @return Labels
     */
    public function forConfirm() : Labels
    {
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
        $this->checkProperty($labelName);
        if ($asConfirm && $this->confirm && $this->confirm->$labelName != null) {
            return $this->confirm->$labelName;
        }
        return $this->$labelName;
    }

    /**
     * Get the confirm labels object, if any
     *
     * @return ?Labels
     */
    public function getConfirm() : ?Labels
    {
        return $this->confirm;
    }

    /**
     * Get a HTML escaped label by name.
     *
     * @param string $labelName
     * @param bool $asConfirm When set, get the "confirm" version (if any).
     * @return ?string
     * @throws \RuntimeException
     */
    public function getEscaped($labelName, $asConfirm = false)
    {
        $this->checkProperty($labelName);
        if ($asConfirm) {
            return $this->confirm->getEscaped($labelName);
        }
        $label = $this->$labelName;
        if (!$this->isHtml[$labelName]) {
            if (is_array($label)) {
                foreach ($label as &$item) {
                    $item = \htmlspecialchars($item);
                }
            } else {
                $label = \htmlspecialchars($label);
            }
        }
        return $label;
    }

    public function getReplacements() {
        return $this->replacements;
    }

    public function getTranslate() : bool
    {
        return $this->translate;
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
        $this->checkProperty($labelName);
        if ($asConfirm) {
            if (!$this->confirm) {
                return false;
            }
            return $this->confirm->has($labelName);
        }
        return $this->$labelName !== null;
    }

    protected function init()
    {
        $this->confirm = null;
        $this->isHtml = array_fill_keys(self::$textProperties, false);
        $this->replacements = [];
        $this->schema = null;
        foreach (self::$textProperties as $prop) {
            $this->$prop = null;
        }
        $this->translate = true;
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
        if ($this->confirm !== null) {
            return $this->confirm->isEmpty();
        }
        return true;
    }

    /**
     * Determine if a label type is escaped HTML.
     * @param string $labelName
     * @param bool $asConfirm When set, check the "confirm" version.
     * @return bool
     * @throws \RuntimeException
     */
    public function isEscaped($labelName, $asConfirm = false) : bool
    {
        if (!$this->has($labelName, $asConfirm)) {
            return false;
        }
        if ($asConfirm) {
            if (!$this->confirm) {
                return false;
            }
            return $this->confirm->isEscaped($labelName);
        }
        return $this->isHtml[$labelName];
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

    protected function jsonEscape(&$property, &$value)
    {
        $parts = explode('.', $value);
        $value = $this->isHtml[$parts[0]];
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
                if ($merge->has($prop)) {
                    $newLabels->set(
                        $prop,
                        $merge->get($prop),
                        [
                            'replacements' => $merge->getReplacements(),
                            'escaped' => $merge->isEscaped($prop)
                        ]
                    );
                }
            }
            $confirmLabels = $merge->getConfirm();
            if (($newConfirm = $newLabels->getConfirm()) !== null) {
                $confirmLabels = $newConfirm->merge($confirmLabels);
            }
            $newLabels->setConfirm($confirmLabels);
            $newLabels->setTranslate(
                $newLabels->getTranslate() || $merge->getTranslate()
            );
        }
        return $newLabels;
    }

    /**
     * Set a label by name.
     *
     * @param string $labelName
     * @param string $text
     * @param array $options Options are asConfirm:bool, replacements:[],
     *          escaped:bool
     * @return $this
     * @throws \RuntimeException
     */
    public function set(
        $labelName,
        $text,
        $options = []
    ) : Labels {
        $this->checkProperty($labelName);
        $asConfirm = $options['asConfirm'] ?? false;
        if ($asConfirm) {
            if ($this->confirm === null) {
                $this->confirm = new Labels();
            }
            $subOptions = $options;
            $subOptions['asConfirm'] = false;
            $this->confirm->set($labelName, $text, $subOptions);
        } else {
            $this->$labelName = $text;
            $this->isHtml[$labelName] = $options['escaped'] ?? false;
        }
        if (isset($options['replacements'])) {
            $this->replacements = array_merge(
                $this->replacements,
                $options['replacements']
            );
        }

        return $this;
    }

    public function setConfirm(Labels $labels = null) : Labels
    {
        $this->confirm = $labels;
        return $this;
    }

    public function setTranslate($translate) : Labels
    {
        $this->translate = $translate;
        return $this;
    }

    /**
     * Create a translated version of the labels.
     * @param Translator $translator The translation facility.
     * @return \Abivia\NextForm\Data\Labels
     */
    public function translate(
        ?Translator $translator = null,
        $replacements = []
    ) : Labels {
        // Create a copy for the translated strings
        $newLabels = clone $this;

        // Merge in any schema defaults
        if ($this->schema) {
            $newLabels = $newLabels->merge($this->schema->getDefault('labels'));
        }

        // Perform the translation
        if ($newLabels->translate && $translator !== null) {
            $replacements = array_merge($this->replacements, $replacements);
            foreach (self::$textProperties as $prop) {
                if (is_array($newLabels->$prop)) {
                    foreach($newLabels->$prop as &$entry) {
                        $entry = $translator->get($entry, $replacements);
                    }
                } elseif ($newLabels->$prop !== null) {
                    $newLabels->$prop = $translator->get(
                        $newLabels->$prop,
                        $replacements
                    );
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
