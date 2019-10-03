<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Traits\JsonEncoderTrait;
use Abivia\NextForm\Trigger\Trigger;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Representation of a form element that accepts user input (including a button).
 */
class FieldElement extends NamedElement
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * A list of possible values for radio/drop-down types.
     * @var array
     */
    protected $dataList;

    /**
     * A translated list of possible values for radio/drop-down types.
     * @var array
     */
    protected $dataListTranslated;

    /**
     * A data property in the form's schema.
     * @var \Abivia\NextForm\Data\Property
     */
    protected $dataProperty;

    /**
     * Default value to use
     * @var string
     */
    protected $default;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Local rules for the JsonEncoder
     * @var array
     */
    static protected $jsonLocalMethod = [
        'object' => ['method:removeScope'],
        'default' => ['drop:null'],
        'triggers' => ['drop:empty', 'drop:null'],
    ];

    /**
     * The name of an associated schema object
     * @var string
     */
    protected $object;

    /**
     * List of triggers associated with this element.
     * @var \Abivia\Trigger\Trigger[]
     */
    protected $triggers = [];

    /**
     * The current field value.
     * @var string
     */
    protected $value;

    /**
     * Configure the JSON encoder on first instantiation.
     */
    public function __construct()
    {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = array_merge(
                parent::$jsonEncodeMethod, self::$jsonLocalMethod
            );
        }
        $this->type = 'field';
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     * @return \self
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema) : self
    {
        $this->dataProperty = $schema->getProperty($this->object);
        if ($this->dataProperty) {
            // Give the data property the ability to signal us.
            $this->dataProperty->linkElement($this);
            if ($this->form) {
                $this->form->registerObject($this);
            }

            // Merge a copy of the data labels so we can use them with translation
            $this->labelsMerged = $this->dataProperty->getLabels()
->combine($this->labels);

            // Make a copy of the data list so we can translate labels
            $this->dataList = $this->dataProperty
->getPopulation()->getList();
            $this->dataListTranslated = $this->dataList;
        }
        return $this;
    }

    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'triggers' => ['className' => Trigger::class],
        ];
        $result = false;
        if (isset($classMap[$property])) {
            $result = (object) $classMap[$property];
        }
        return $result;
    }

    /**
     * Pass the completeness check up so we have a label structure.
     * @return boolean
     */
    protected function configureComplete()
    {
        // The NamedElement class initializes the labelsMerged property
        return parent::configureComplete();
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize(&$config)
    {
        if (isset($this->configureOptions['_form'])) {
            $this->form = $this->configureOptions['_form'];
            $this->form->registerElement($this);
        }
    }

    protected function configurePropertyIgnore($property)
    {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property)
    {
        return parent::configurePropertyMap($property);
    }

    /**
     * Make sure the object property has a scope.
     * @param string $property
     * @param mixed $value
     * @return boolean
     */
    protected function configureValidate($property, &$value)
    {
        if ($property == 'object') {
            if ($value === '') {
                return true;
            }
            if (strpos($value, NextForm::SEGMENT_DELIM) === false && $this->form) {
                $value = $this->form->getSegment()
                    . NextForm::SEGMENT_DELIM . $value;
            }
            return true;
        } elseif (in_array($property, array_keys(self::$jsonLocalMethod))) {
            return true;
        }
        return parent::configureValidate($property, $value);
    }

    /**
     * Get the connected schema object, if any
     * @return Abivia\NextForm\Data\Property
     */
    public function getDataProperty() : Property
    {
        if ($this->dataProperty === null) {
            throw new \RuntimeException(
                'Attempt to get missing schema information, object ' . $this->getObject()
            );
        }
        return $this->dataProperty;
    }

    /**
     * Get the default value for this field.
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get the of Population/Option objects associated with the field with no hierarchy.
     * @param bool $translated Returns the translated texts, if available
     * @return \Abivia\NextForm\Data\Population\Option[]
     */
    public function getFlatList($translated = false)
    {
        if ($translated && $this->hasTranslation) {
            $source = $this->dataListTranslated;
        } else {
            $source = $this->dataList;
        }
        // Lists can only nest one level deep, so this is straightforward.
        $list = [];
        foreach ($source as $option) {
            if ($option->isNested()) {
                foreach ($option->getList() as $item) {
                    $list[] = $item;
                }
            } else {
                $list[] = $option;
            }
        }
        return $list;
    }

    /**
     * Get native or translated scope-resolved labels for this element.
     * @param bool $translated
     * @return \Abivia\NextForm\Data\Labels
     */
    public function getLabels($translated = false) : Labels
    {
        if ($translated && $this->hasTranslation) {
            return $this->labelsTranslated;
        } else {
            $labels = $this->labelsMerged;
        }
        return $labels;
    }

    /**
     * Get a hierarchical list of Population/Option objects associated with the field
     * @param bool $translated Returns the translated texts, if available
     * @return Abivia\NextForm\Data\Population\Option[]
     */
    public function getList($translated = false)
    {
        if ($translated && $this->hasTranslation) {
            $list = $this->dataListTranslated;
        } else {
            $list = $this->dataList;
        }
        return $list;
    }

    /**
     * Get the name of an associated schema object.
     * @return string
     */
    public function getObject() {
        return $this->object;
    }

    /**
     * Get the current value of the field.
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * If we can represent this field in JSON as a string, return a string otherwise $this.
     */
    public function jsonCollapse()
    {
        if ($this->default !== null) {
            return $this;
        }
        if (!empty($this->triggers)) {
            return $this;
        }
        if (!$this->enabled || $this->readonly || !$this->visible) {
            return $this;
        }
        if ($this->show !== '') {
            return $this;
        }
        $collapsed = $this->removeScope($this->object);
        if (!empty($this->groups)) {
            $collapsed .= NextForm::GROUP_DELIM
                . implode(NextForm::GROUP_DELIM, $this->groups);
        }
        return $collapsed;
    }

    /**
     * Used by the JSON encoder. Remove the scope if the form has a matching default segment.
     * @param type $value
     * @return type
     */
    protected function removeScope($value)
    {
        if (!$this->form) {
            return $value;
        }
        $segment = $this->form->getSegment();
        if ($segment !== '') {
            if (strpos($value, $segment . NextForm::SEGMENT_DELIM) === 0) {
                $value = substr($value, strlen($segment) +1);
            }
        }
        return $value;
    }

    /**
     * Set the default value for this field
     * @param $value The new default value.
     * @return \self
     */
    public function setDefault($value) : self
    {
        $this->default = $value;
        return $this;
    }

    /**
     * Set the current value for this field
     * @param $value The new default value.
     * @return \self
     */
    public function setValue($value) : self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Translate the texts in this element.
     * @param Translator $translate
     * @return \Abivia\NextForm\Form\Element\Element
     */
    public function translate(Translator $translate) : Element
    {
        // Translate the data list, if any
        if ($this->dataProperty) {
            $this->dataListTranslated = $this->dataList;
            if ($this->dataProperty->getPopulation()->getTranslate()) {
                foreach ($this->dataListTranslated as $option) {
                    $option->translate($translate);
                }
            }
        }
        // Translate the labels.
        return parent::translate($translate);
    }

}