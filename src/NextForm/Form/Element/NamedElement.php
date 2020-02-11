<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Elements with a label attribute.
 */
abstract class NamedElement Extends Element
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Flag indicating if the static context has been initialized.
     * @var bool
     */
    static private $staticInit;

    public function __construct()
    {
        parent::__construct();
        if (!self::$staticInit) {
            self::$jsonEncodeMethod = parent::$jsonEncodeMethod;
            self::$jsonEncodeMethod['labels'] = ['drop:empty', 'drop:null'];
            self::$staticInit = true;
        }
    }

    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'labels' => ['className' => Labels::class],
        ];
        $result = false;
        if (isset($classMap[$property])) {
            $result = (object) $classMap[$property];
        } else {
            $result = parent::configureClassMap($property, $value);
        }
        return $result;
    }

    protected function configureComplete()
    {
        if ($this->labels === null) {
            $this->labels  = new Labels();
        }
        // Default merge is nothing to merge with.
        $this->labelsMerged = clone $this->labels;
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

}