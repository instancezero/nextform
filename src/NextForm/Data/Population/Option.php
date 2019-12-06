<?php

namespace Abivia\NextForm\Data\Population;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Manager;
use Abivia\NextForm\Traits\JsonEncoderTrait;
use Abivia\NextForm\Traits\ShowableTrait;

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Describes a value or list of values in a user selectable form object.
 */
class Option implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;
    use ShowableTrait;

    /**
     * Whether or not this option is currently available.
     * @var bool
     */
    protected $enabled = true;

    /**
     * A list of groups that this option belongs to.
     * @var string[]
     */
    protected $groups = [];

    static protected $jsonEncodeMethod = [
        'label' => [],
        'value' => ['drop:null'],
        'groups' => ['drop:null', 'drop:empty', 'scalarize', 'map:memberOf'],
        'enabled' => ['drop:true'],
        'name' => ['drop:blank','drop:null'],
        'sidecar' => ['drop:null'],
    ];

    /**
     * The user-visible text associated with this option.
     * @var string
     */
    protected $label;
    protected $name;
    protected $selected = false;

    /**
     * Arbitrary data associated with this field.
     * @var mixed
     */
    public $sidecar;
    protected $value;

    public function __construct()
    {
        self::$showDefaultScope = 'option';
    }

    /**
     * Add this option to the named group.
     * @param type $groupName Name of the group to be added.
     * @return \self
     */
    public function addGroup($groupName) : self
    {
        if (!in_array($groupName, $this->groups)) {
            $this->groups[] = $groupName;
            $this->configureValidate('groups', $this->groups);
        }
        return $this;
    }

    protected function configureClassMap($property, $value)
    {
        $result = false;
        if ($property == 'value' && is_array($value)) {
            $result = self::class;
        }
        return $result;
    }

    protected function configureComplete(): bool
    {
        if (\is_array($this->value)) {
            foreach($this->value as $option) {
                if (\is_array($option->getList())) {
                    throw new \OutOfBoundsException('Options can\'t be nested more than two levels deep.');
                }
            }
        }
        if ($this->label == '') {
            throw new \OutOfBoundsException('Options must have a label.');
        }
        if ($this->value == '') {
            $this->value = $this->label;
        }
        return true;
    }

    /**
     * Facilitates the use of simple string values of the form "label[:value]"
     * in the configuration by converting them to classes.
     *
     * @param \stdClass $config
     */
    protected function configureInitialize(&$config)
    {
        // If the configuration is a string, treat it as label[:value]
        if (\is_string($config)) {
            $obj = new \stdClass();
            if (($posn = \strrpos($config, Manager::GROUP_DELIM)) !== false) {
                $obj->label = \substr($config, 0, $posn);
                $obj->value = \substr($config, $posn + 1);

            } else {
                $obj->label = $config;
            }
            $config = $obj;
        } elseif (isset($config->value) && \is_array($config->value)) {
            // plain string labels are converted to objects with a label property
            foreach ($config->value as &$value) {
                if (is_string($value)) {
                    // Convert to a useful class
                    $obj = new \stdClass();
                    $obj->label = $value;
                    $value = $obj;
                }
            }
        }
    }

    /**
     * Map the config file's "memberOf" to "groups".
     * @param string $property Name of the property.
     * @return string
     */
    protected function configurePropertyMap($property)
    {
        if ($property == 'memberOf') {
            $property = 'groups';
        }
        return $property;
    }

    /**
     * Ensure groups is an array of valid names.
     * @param string $property Name of the property.
     * @param type $value Current value of the property.
     * @return boolean True when the property is valid.
     */
    protected function configureValidate($property, &$value)
    {
        if ($property === 'groups') {
            if (!is_array($value)) {
                $value = [$value];
            }
            foreach ($value as $key => &$item) {
                $item = trim($item);
                if (!preg_match('/^[a-z0-9\-_]+$/i', $item)) {
                    unset($value[$key]);
                }
            }
            $value = array_values(array_unique($value));
        }
        return true;
    }

    /**
     * Delete this element from the named group.
     * @param type $groupName Name of the group to be added.
     * @return \self
     */
    public function deleteGroup($groupName) : self
    {
        if (($key = array_search($groupName, $this->groups)) !== false) {
            unset($this->groups[$key]);
            $this->groups = array_values($this->groups);
        }
        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get the list of groups this element is a member of.
     * @return string[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getList()
    {
        if (!is_array($this->value)) {
            return null;
        }
        return $this->value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Get the sidecar data
     *
     * @return mixed
     */
    public function getSidecar()
    {
        return $this->sidecar;
    }

    public function getValue()
    {
        if (is_array($this->value)) {
            return null;
        }
        return $this->value;
    }

    /**
     * Determine if this option is empty (used by JsonEncoder).
     * @return bool
     */
    public function isEmpty() : bool
    {
        if ($this->enabled === false) {
            return false;
        }
        if (!empty($this->groups)) {
            return false;
        }
        if ($this->label !== null && $this->label !== '') {
            return false;
        }
        if ($this->name !== null && $this->name !== '') {
            return false;
        }
        if ($this->sidecar !== null) {
            return false;
        }
        if ($this->value !== null) {
            return false;
        }
        return true;
    }

    public function isNested()
    {
        return is_array($this->value);
    }

    public function jsonCollapse() {
        if ($this->enabled === false) {
            return $this;
        }
        if ($this->name !== null && $this->name !== '') {
            return $this;
        }
        if ($this->sidecar !== null) {
            return $this;
        }
        if (!empty($this->groups)) {
            return $this;
        }
        if ($this->label === $this->value) {
            $result = $this->label;
        } else {
            if (\is_bool($this->value)) {
                $strVal = $this->value ? 'true' : 'false';
            } else {
                $strVal = $this->value;
            }
            $result = $this->label . Manager::GROUP_DELIM . $strVal;
        }
        return $result;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set the groups this element is a member of
     * @param string|string[] $groups The group or groups.
     * @return \self
     */
    public function setGroups($groups) : self
    {
        $this->configureValidate('groups', $groups);
        $this->groups = $groups;
        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setSelected($selected)
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * Set the sidecar data.
     *
     * @param mixed $data
     * @return $this
     */
    public function setSidecar($data) : self
    {
        $this->sidecar = $data;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function translate(Translator $translator = null) : Option
    {
        $translated = clone $this;
        if ($translator !== null) {
            $translated->setLabel($translator->trans($this->label));
        }
        return $translated;
    }

}