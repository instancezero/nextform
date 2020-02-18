<?php

namespace Abivia\NextForm\Form\Element;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * The static element puts static text into the form
 */
class StaticElement Extends SimpleElement
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * Text is HTML encoded flag. If set, the contents are assumed to be safe and escaped.
     * @var boolean
     */
    protected $html = false;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [];

    /**
     * Local rules for the JsonEncoder
     * @var array
     */
    static protected $jsonLocalMethod = [
        'html' => ['drop:false', 'order:500'],
        'value' => ['order:500'],
    ];

    public function __construct()
    {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = self::getJsonEncodings();
        }
        $this->type = 'static';
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

    protected function configureValidate($property, &$value)
    {
        return parent::configureValidate($property, $value);
    }

    /**
     * Get the text is HTML flag.
     * @return bool
     */
    public function getHtml() : bool
    {
        return $this->html;
    }

    /**
     * Get the JSON Encoding rules.
     *
     * @return array JSON encoding rules.
     */
    static public function getJsonEncodings() {
        $jsonEncoding = array_merge(parent::getJsonEncodings(), self::$jsonLocalMethod);
        return $jsonEncoding;
    }

    /**
     * Set the text is HTML flag.
     * @param bool $isHtml
     * @return $this
     */
    public function setHtml(bool $isHtml)
    {
        $this->html = $isHtml;
        return $this;
    }

}