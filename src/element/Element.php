<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm;
use DeepCopy\DeepCopy;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\PropertyNameMatcher;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 *
 */
abstract class Element implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    /**
     * System-assigned element ID
     * @var string
     */
    protected $autoId;
    protected $enabled = true;
    /**
     * The form this element belongs to
     * @var \Abivia\NextForm
     */
    protected $form;
    protected $group;
    /**
     * User-specified element id, overrides auto ID
     * @var string
     */
    protected $id = '';
    static protected $jsonEncodeMethod = [
        'type' => [],
        'name' => ['drop:blank'],
        'id' => ['drop:blank'],
        'group' => ['drop:null', 'map:memberOf'],
        'enabled' => ['drop:true'],
        'readonly' => ['drop:false', 'drop:null'],
        'visible' => ['drop:true'],
        'show' => ['drop:blank'],
    ];
    protected $name = '';
    protected $readonly;
    protected $show = '';
    protected $type;
    protected $visible = true;

    public function __construct() {

    }

    public function addShow($show) : self {
        if ($this -> show === '') {
            $this -> show = trim($show);
        } else {
            $this -> show .= '|' . trim($show);
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

    protected function configureInitialize() {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
            $this -> form -> registerElement($this);
        }
    }

    protected function configurePropertyIgnore($property) {
        return $property == 'type';
    }

    protected function configurePropertyMap($property) {
        if ($property == 'memberOf') {
            $property = 'group';
        }
        return $property;
    }

    protected function configureValidate($property, &$value) {
        return true;
    }

    /**
     * Make a copy of this element, cloning/preserving selected properties
     * @return \Abivia\NextForm\Element\Element
     */
    public function copy() : Element {
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

    public function generate($renderer, $access, Translator $translate) {
        $this -> translate($translate);
        //$readOnly = false; // $access -> hasAccess(...)
        $options = ['access' => 'write'];
        $pageData = $renderer -> render($this, $options);
        return $pageData;
    }

    public function getEnabled() {
        return $this -> enabled;
    }

    public function getGroup() {
        return $this -> group;
    }

    public function getId() {
        if ($this -> id != '') {
            return $this -> id;
        }
        if ($this -> autoId == '') {
            $this -> autoId = NextForm::htmlIdentifier($this -> type, true);
        }
        return $this -> autoId;
    }

    public function getName() {
        return $this -> name;
    }

    public function getReadonly() {
        return $this -> readonly;
    }

    public function getShow() {
        return $this -> show;
    }

    public function getType() {
        return $this -> type;
    }

    public function getVisible() {
        return $this -> visible;
    }

    public function setEnabled($enabled) {
        $this -> enabled = $enabled;
        return $this;
    }

    public function setGroup($group) {
        $this -> group = $group;
        return $this;
    }

    public function setId($id) {
        $this -> id = $id;
        return $this;
    }

    public function setName($name) {
        $this -> name = $name;
        return $this;
    }

    public function setReadonly($readonly) {
        $this -> readonly = $readonly;
        return $this;
    }

    public function setShow($show) {
        $this -> show = trim($show);
        return $this;
    }

    public function setVisible($visible) {
        $this -> visible = $visible;
        return $this;
    }

    /**
     * Translate -- this method probably should be abstract...
     * @param Translator $translate
     * @codeCoverageIgnore
     */
    public function translate(Translator $translate) {

    }

}