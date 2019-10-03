<?php

namespace Abivia\NextForm\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Support for a form button
 */
class ButtonElement Extends NamedElement
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * The function this button performs on the form.
     * @var string
     */
    protected $function = 'button';

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Local rules for the JsonEncoder, merged into those of parent classes.
     * @var array
     */
    static protected $jsonLocalMethod = [
        'function' => ['drop:false'],
    ];

    /**
     * A list of valid button functions.
     * @var string[]
     */
    static protected $validFunctions = ['button', 'reset', 'submit'];

    /**
     * Merge JSON encoding rules on first instantiation.
     */
    public function __construct()
    {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = array_merge(parent::$jsonEncodeMethod, self::$jsonLocalMethod);
        }
        $this->type = 'button';
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
    protected function configureInitialize(&$config)
    {
        if (isset($this->configureOptions['_form'])) {
            $this->form = $this->configureOptions['_form'];
            $this->form->registerElement($this);
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
     * Ensure that the button function is valid.
     * @param string $property Name of the property to validate.
     * @param mixed $value Current value of the property.
     * @return boolean
     */
    protected function configureValidate($property, &$value)
    {
        if ($property === 'function') {
            if (!in_array($value, self::$validFunctions)) {
                $this->configureLogError(
                    $property . ' must be one of ' . implode(',', self::$validFunctions) . '.'
                );
                return false;
            }
            return true;
        }
        return parent::configureValidate($property, $value);
    }

    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set the button function
     * @param mixed $value The button function.
     * @return $this
     * @throws \RuntimeException If the value is not a valid button function.
     */
    public function setFunction($value)
    {
        $this->configureErrors = [];
        if (!$this->configureValidate('function', $value)) {
            throw new \RuntimeException(implode("\n", $this->configureErrors));
        }
        $this->function = $value;
        return $this;
    }

}