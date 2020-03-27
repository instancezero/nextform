<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Support for a captcha field
 */
class CaptchaElement Extends NamedElement
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Local rules for the JsonEncoder, merged into those of parent classes.
     * @var array
     */
    static protected $jsonLocalMethod = [];

    /**
     * Merge JSON encoding rules on first instantiation.
     */
    public function __construct()
    {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = array_merge(parent::$jsonEncodeMethod, self::$jsonLocalMethod);
        }
        $this->type = 'captcha';
    }

    protected function configureClassMap($property, $value)
    {
        return parent::configureClassMap($property, $value);
    }

    protected function configureComplete()
    {
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
     * Ensure that the button function is valid.
     * @param string $property Name of the property to validate.
     * @param mixed $value Current value of the property.
     * @return boolean
     */
    protected function configureValidate($property, &$value)
    {
        return parent::configureValidate($property, $value);
    }

}