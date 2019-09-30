<?php

namespace Abivia\NextForm\Element;

/**
 * The static element puts static text into the form
 */
class StaticElement Extends SimpleElement {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Element\HasLabels;
    use \Abivia\NextForm\Traits\JsonEncoder;

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
        'html' => ['drop:false'],
        'value' => [],
    ];

    public function __construct() {
        parent::__construct();
        if (empty(self::$jsonEncodeMethod)) {
            self::$jsonEncodeMethod = array_merge(parent::$jsonEncodeMethod, self::$jsonLocalMethod);
            self::$jsonEncodeMethod['labels'] = ['drop:empty', 'drop:null'];
        }
        $this -> type = 'static';
    }

    protected function configureClassMap($property, $value) {
        return parent::configureClassMap($property, $value);
    }

    /**
     * Extract the form if we have one. Not so DRY because we need local options
     */
    protected function configureInitialize(&$config) {
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
     * Get the text is HTML flag.
     * @return bool
     */
    public function getHtml() : bool {
        return $this -> html;
    }

    /**
     * Set the text is HTML flag.
     * @param bool $isHtml
     * @return \self
     */
    public function setHtml(bool $isHtml) : self {
        $this -> html = $isHtml;
        return $this;
    }

}