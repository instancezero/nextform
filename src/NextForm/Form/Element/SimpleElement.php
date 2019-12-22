<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * A simple element is any element with a value that is part of the form specification.
 */
abstract class SimpleElement Extends Element
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Indicates if the value has been translated or not.
     * @var bool
     */
    protected $hasTranslation = false;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Indicates if the value should be translated when generated.
     * @var bool
     */
    protected $translate = true;

    /**
     * The value of this element.
     * @var string
     */
    protected $value = '';

    /**
     * The translated value of this element.
     * @var string
     */
    protected $valueTranslated = '';

    public function __construct()
    {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = self::getJsonEncodings();
        }
    }

    protected function configureClassMap($property, $value)
    {
        return parent::configureClassMap($property, $value);
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

    protected function configureValidate($property, &$value)
    {
        return parent::configureValidate($property, $value);
    }

    /**
     * Get the JSON Encoding rules.
     *
     * @return array JSON encoding rules.
     */
    static public function getJsonEncodings() {
        $jsonEncoding = parent::getJsonEncodings();
        $jsonEncoding['value'] = ['order:500'];
        $jsonEncoding['translate'] = ['drop:true', 'order:2000'];
        return $jsonEncoding;
    }

    /**
     * Get the "translation required" state.
     * @return bool
     */
    public function getTranslate() : bool
    {
        return $this->translate;
    }

    /**
     * Get the element value, typically with translation (if available).
     * @param boolean $translated If true, any translated version is returned.
     * @return string
     */
    public function getValue($translated = true)
    {
        if ($translated and $this->hasTranslation) {
            return $this->valueTranslated;
        }
        return $this->value;
    }

    /**
     * Set the "translation required" state.
     * @param bool $translate True if this element's value should be translated.
     * @return \self
     */
    public function setTranslate(bool $translate) : self
    {
        $this->translate = $translate;
        return $this;
    }

    /**
     * Set the element value.
     * @param string $value
     * @return \self
     */
    public function setValue($value) : self
    {
        $this->value = $value;
        if ($this->translate) {
            $this->hasTranslation = false;
        } else {
            $this->valueTranslated = $this->value;
        }
        return $this;
    }

    /**
     * Generate a translated version of this element.
     * @param \Abivia\NextForm\Form\Element\Translator $translator
     * @return \Abivia\NextForm\Form\Element\Element
     */
    public function translate(Translator $translator = null) : Element
    {
        if ($translator !== null) {
            $this->valueTranslated = $translator->get($this->value);
        } else {
            $this->valueTranslated = $this->value;
        }
        $this->hasTranslation = true;
        return $this;
    }

}