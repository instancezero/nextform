<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm;
use Abivia\NextForm\Contracts\Access;
use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Renderer\Block;
use DeepCopy\DeepCopy;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\PropertyNameMatcher;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Any element that can appear on a form.
 */
abstract class Element implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;
    use \Abivia\NextForm\Traits\Showable;

    /**
     * System-assigned element ID
     * @var string
     */
    protected $autoId;

    /**
     * Set if this element is enabled on the form.
     * @var bool
     */
    protected $enabled = true;

    /**
     * The form this element belongs to
     * @var \Abivia\NextForm
     */
    protected $form;

    /**
     * A list of groups that this element belongs to.
     * @var string[]
     */
    protected $groups = [];

    /**
     * User-specified element id, overrides auto ID
     * @var string
     */
    protected $id = '';

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'type' => [],
        'name' => ['drop:blank'],
        'id' => ['drop:blank'],
        'groups' => ['drop:null', 'drop:empty', 'scalarize', 'map:memberOf'],
        'enabled' => ['drop:true'],
        'readonly' => ['drop:false', 'drop:null'],
        'visible' => ['drop:true'],
        'show' => ['drop:blank'],
    ];

    /**
     * The name of this element.
     * @var string
     */
    protected $name = '';

    /**
     * The read-only state for this element.
     * @var bool
     */
    protected $readonly = false;

    /**
     * The element type.
     * @var string
     */
    protected $type;

    /**
     * The visibility state for this element.
     * @var bool
     */
    protected $visible = true;

    public function __construct() {

    }

    /**
     * Add this element to the named group.
     * @param type $groupName Name of the group to be added.
     * @return \self
     */
    public function addGroup($groupName) : self {
        if (!in_array($groupName, $this -> groups)) {
            $this -> groups[] = $groupName;
            $this -> configureValidate('groups', $this -> groups);
        }
        return $this;
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     * @codeCoverageIgnore
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema) {
        // Non-data elements do nothing. This just simplifies walking the tree
    }

    /**
     * Generate a class name based on the element type.
     * @param stdClass $obj The object being configured.
     * @return string
     * @throws \InvalidArgumentException
     */
    static public function classFromType($obj) {
        $result = 'Abivia\NextForm\Element\\' . ucfirst(strtolower($obj -> type)) . 'Element';
        if (!class_exists($result)) {
            throw new \InvalidArgumentException($obj -> type . ' is not a valid element type.');
        }
        return $result;
    }

    protected function configureComplete() {
        return true;
    }

    /**
     * If the element is created as part of a form, register it as such.
     */
    protected function configureInitialize() {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
            $this -> form -> registerElement($this);
        }
    }

    /**
     * Configuration files can't specify the element type.
     * @param string $property Name of the property.
     * @return bool
     */
    protected function configurePropertyIgnore($property) {
        return $property == 'type';
    }

    /**
     * Map the config file's "memberOf" to "groups".
     * @param string $property Name of the property.
     * @return string
     */
    protected function configurePropertyMap($property) {
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
    protected function configureValidate($property, &$value) {
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
     * Make a copy of this element, cloning/preserving selected properties
     * @return \self
     */
    public function copy() : self {
        static $cloner = null;

        if ($cloner === null) {
            $cloner = new DeepCopy;
            // Don't copy the form ID
            $cloner -> addFilter(
                new SetNullFilter(),
                new PropertyNameMatcher('\Abivia\NextForm\Element\Element', 'autoId')
            );
            // Don't clone the linked data
            $cloner -> addFilter(
                new KeepFilter(),
                new PropertyNameMatcher('\Abivia\NextForm\Element\Element', 'dataProperty')
            );
        }
        return $cloner -> copy($this);
    }

    /**
     * Use a renderer to turn this element into part of the form.
     * @param Renderer $renderer Any Renderer object.
     * @param Access $access Any access control object
     * @param Translator $translate Any translation object.
     * @return Block
     */
    public function generate(Renderer $renderer, Access $access, Translator $translate) : Block {
        $this -> translate($translate);
        //$readOnly = false; // $access -> hasAccess(...)
        $options = ['access' => 'write'];
        $pageData = $renderer -> render($this, $options);
        return $pageData;
    }

    /**
     * Get the enabled state of this element.
     * @return bool
     */
    public function getEnabled() {
        return $this -> enabled;
    }

    /**
     * Get the list of groups this element is a member of.
     * @return string[]
     */
    public function getGroups() {
        return $this -> groups;
    }

    /**
     * Get the form ID for this element.
     * @return string
     */
    public function getId() {
        if ($this -> id != '') {
            return $this -> id;
        }
        if ($this -> autoId == '') {
            $this -> autoId = NextForm::htmlIdentifier($this -> type, true);
        }
        return $this -> autoId;
    }

    /**
     * Get the name of this element.
     * @return string
     */
    public function getName() {
        return $this -> name;
    }

    /**
     * Get the read-only state of this element.
     * @return bool
     */
    public function getReadonly() {
        return $this -> readonly;
    }

    /**
     * Get this element's type.
     * @return string
     */
    public function getType() {
        return $this -> type;
    }

    /**
     * Get the visible state of this element.
     * @return bool
     */
    public function getVisible() {
        return $this -> visible;
    }

    /**
     * Remove this element from the named group.
     * @param type $groupName Name of the group to be added.
     * @return \self
     */
    public function removeGroup($groupName) : self {
        if (($key = array_search($groupName, $this -> groups)) !== false) {
            unset($this -> groups[$key]);
            $this -> groups = array_values($this -> groups);
        }
        return $this;
    }

    /**
     * Set the enabled state for this element.
     * @param bool $enabled
     * @return \self
     */
    public function setEnabled($enabled) : self {
        $this -> enabled = $enabled;
        return $this;
    }

    /**
     * Set the groups this element is a member of
     * @param string|string[] $groups The group or groups.
     * @return \self
     */
    public function setGroups($groups) : self {
        $this -> configureValidate('groups', $groups);
        $this -> groups = $groups;
        return $this;
    }

    /**
     * Set the form ID for this element.
     * @param string $id
     * @return \self
     */
    public function setId($id) : self {
        $this -> id = $id;
        return $this;
    }

    /**
     * Set the name of this element.
     * @param string $name
     * @return \self
     */
    public function setName($name) : self {
        $this -> name = $name;
        return $this;
    }

    /**
     * Set this element's read-only state.
     * @param bool $readonly
     * @return \self
     */
    public function setReadonly($readonly) : self {
        $this -> readonly = $readonly;
        return $this;
    }

    /**
     * Set this element's visible state.
     * @param bool $visible
     * @return \self
     */
    public function setVisible($visible) : self {
        $this -> visible = $visible;
        return $this;
    }

    /**
     * Translate the texts in this element.
     * @param Translator $translate
     * @return \Abivia\NextForm\Element\Element
     */
    abstract public function translate(Translator $translate) : Element;

}