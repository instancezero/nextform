<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm\Contracts\Access;
use Abivia\NextForm\Contracts\Renderer;
use Abivia\NextForm\Renderer\Block;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Class for any element that contains a list of sub-elements.
 */
abstract class ContainerElement Extends NamedElement {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    /**
     * The list of elements contained by this instance.
     * @var Element[]
     */
    protected $elements = [];

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Build JSON encoder rules on the first instantiation.
     */
    public function __construct() {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = parent::$jsonEncodeMethod;
            self::$jsonEncodeMethod['labels'] = ['drop:empty', 'drop:null'];
            self::$jsonEncodeMethod['elements'] = [];
        }
    }

    abstract public function addElement(Element $element);

    /**
     * Sub-elements have a procedural instantiation.
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    protected function configureClassMap($property, $value) {
        $result = false;
        if ($property == 'elements') {
            $result = new \stdClass;
            $result -> key = [$this, 'addElement'];
            $result -> className = [Element::class, 'classFromType'];
        } else {
            $result = parent::configureClassMap($property, $value);
        }
        return $result;
    }

    protected function configureComplete() {
        return parent::configureComplete();
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize() {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
            $this -> form -> registerElement($this);
        }
    }

    protected function configurePropertyIgnore($property) {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property) {
        return parent::configurePropertyMap($property);
    }

    protected function configureValidate($property, &$value) {
        return parent::configureValidate($property, $value);
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
        $options = false; // $access -> hasAccess(...)
        $options = ['access' => 'write'];
        $containerData = $renderer -> render($this, $options);
        foreach ($this -> elements as $element) {
            $containerData -> merge($element -> generate($renderer, $access, $translate));
        }
        $containerData -> close();
        return $containerData;
    }

    /**
     * Get the elements in this container.
     * @return Element[]
     */
    public function getElements() {
        return $this -> elements;
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema) {
        foreach ($this -> elements as $element) {
            $element -> bindSchema($schema);
        }
    }

}