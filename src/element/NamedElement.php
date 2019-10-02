<?php

namespace Abivia\NextForm\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Data\Labels;
use Abivia\NextForm\Element\LabelsTrait;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Elements with a name attribute on the form.
 */
abstract class NamedElement Extends Element
{
    use Configurable;
    use JsonEncoderTrait;
    use LabelsTrait;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Name on the rendered form.
     * @var string
     */
    protected $formName;

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
        if ($this -> labels === null) {
            $this -> labels  = new Labels;
        }
        // Default merge is nothing to merge with.
        $this -> labelsMerged = clone $this -> labels;
        return parent::configureComplete();
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize(&$config)
    {
        if (isset($this -> configureOptions['_form'])) {
            $this -> form = $this -> configureOptions['_form'];
            $this -> form -> registerElement($this);
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

    protected function configureValidate($property, &$value)
    {
        return parent::configureValidate($property, $value);
    }

    /**
     * Get this element's name on the form. If not assigned, a name is generated.
     * @return string
     */
    public function getFormName()
    {
        if ($this -> formName === null) {
            if ($this -> name != '') {
                $this -> formName = $this -> name;
            } else {
                if ($this -> autoId == '') {
                    $this -> getId();
                }
                $this -> formName = $this -> autoId;
            }
        }
        return $this -> formName;
    }

    /**
     * Assign or override the current name of this element on a form.
     * @param string $name
     * @return $this
     */
    public function setFormName($name)
    {
        $this -> formName = $name;
        return $this;
    }

}