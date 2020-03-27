<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\NextForm;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Form\Form;
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

    /**
     * Add a trigger to the trigger list.
     *
     * @param Trigger $trigger
     * @return $this
     */
    public function addTrigger(Trigger $trigger)
    {
        $this->triggers[] = $trigger;
        return $this;
    }

    /**
     * FieldElement factory.
     *
     * @return FieldElement
     */
    static public function build()
    {
        return new FieldElement();
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
    protected function configureInitialize(&$config, ...$context)
    {
        if (\is_string($config)) {
            // Convert to a field/object
            $config = Form::expandString($config);
        }
        $this->registerElement($this->configureOptions);
        return true;
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
     * Get the default value for this field.
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get the name of an associated schema object.
     *
     * @return string
     */
    public function getObject() {
        return $this->object;
    }

    /**
     * Get the element triggers
     *
     * @return Trigger[]
     */
    public function getTriggers() {
        return $this->triggers;
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
        return $value;
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
     * @return $this
     */
    public function setDefault($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * Get the name of an associated schema object.
     *
     * @return string
     */
    public function setObject($objectName) {
        $this->object = $objectName;
        return $this;
    }

    /**
     * Set the element triggers
     *
     * @return $this
     */
    public function setTriggers($triggers) {
        $this->triggers = $triggers;
        return $this;
    }

}