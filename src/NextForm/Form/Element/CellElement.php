<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Representation of a cell, a list of adjacent form elements.
 */
class CellElement Extends ContainerElement
{
    use Configurable;
    use JsonEncoderTrait;

    protected $elements = [];

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Initialize JSON encoder tables on the first instantiation.
     */
    public function __construct() {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = parent::$jsonEncodeMethod;
        }
        $this->type = 'cell';
    }

    /**
     * Add (append) an element to this cell
     * @param \Abivia\NextForm\Form\Element\Element $element An element to be added to this cell.
     * @return \self
     * @throws \RuntimeException
     */
    public function addElement(Element $element) : self
    {
        if ($element instanceof ContainerElement) {
            throw new \RuntimeException('Cells can\'t contain containers (sections or cells).');
        }
        $this->elements[] = $element;
        return $this;
    }

    protected function configureClassMap($property, $value)
    {
        return parent::configureClassMap($property, $value);
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize(&$config, ...$context)
    {
        $this->expandElements($config);
        $this->registerElement($this->configureOptions);
        return true;
    }

    protected function configureComplete()
    {
        return parent::configureComplete();
    }

    protected function configurePropertyIgnore($property)
    {
        return parent::configurePropertyIgnore($property);
    }

    protected function configurePropertyMap($property)
    {
        return parent::configurePropertyMap($property);
    }

    protected function configureValidate($property, &$value)
    {
        return parent::configureValidate($property, $value);
    }

    /**
     * Get the data segment associated with this cell, if any.
     * @return \Abivia\NextForm\Data\Segment|null
     */
    public function findSegment() : ?\Abivia\NextForm\Data\Segment
    {
        return isset($this->configureOptions['parent'])
            ? $this->configureOptions['parent']->getSegment() : null;
    }

}