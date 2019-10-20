<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Manager;
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
        'object' => ['method:removeScope', 'order:250'],
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

    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'triggers' => ['className' => Trigger::class],
        ];
        $result = false;
        if (isset($classMap[$property])) {
            $result = (object) $classMap[$property];
        } else {
            $result = parent::configureClassMap($property, $value);
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
        if (is_string($config)) {
            // Convert to a useful class
            $config = self::expandString($config);
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
        if (in_array($property, array_keys(self::$jsonLocalMethod))) {
            return true;
        }
        return parent::configureValidate($property, $value);
    }

    /**
     * Convert a string of the form property:group:group to a configurable stdClass.
     *
     * @param string $value
     * @return \stdClass
     */
    static public function expandString(string $value)
    {
        $groupParts = explode(Manager::GROUP_DELIM, $value);
        // Convert to a useful class
        $obj = new \stdClass;
        $obj->type = 'field';
        $obj->object = array_shift($groupParts);
        if (!empty($groupParts)) {
            $obj->memberOf = $groupParts;
        }
        return $obj;
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
     * Get the name of an associated schema object.
     * @return string
     */
    public function getObject() {
        return $this->object;
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
        if (!$this->enabled || $this->readonly || !$this->display) {
            return $this;
        }
        if ($this->show !== '') {
            return $this;
        }
        $collapsed = $this->removeScope($this->object);
        if (!empty($this->groups)) {
            $collapsed .= Manager::GROUP_DELIM
                . implode(Manager::GROUP_DELIM, $this->groups);
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
            return $value;
        if (!$this->form) {
            return $value;
        }
        $segment = $this->form->getSegment();
        if ($segment !== '') {
            if (strpos($value, $segment . Manager::SEGMENT_DELIM) === 0) {
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
     * Translate the texts in this element.
     * @param Translator $translator
     * @return \Abivia\NextForm\Form\Element\Element
     */
    public function translate(Translator $translator = null) : Element
    {
        // Translate the data list, if any
        if ($this->dataProperty) {
            $this->dataListTranslated = $this->dataList;
            if ($this->dataProperty->getPopulation()->getTranslate()) {
                foreach ($this->dataListTranslated as $option) {
                    $option->translate($translator);
                }
            }
        }
        // Translate the labels.
        return parent::translate($translator);
    }

}