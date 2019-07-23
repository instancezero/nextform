<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm\Data\Labels;

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Elements with a label attribute
 */
abstract class NamedElement Extends Element {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\JsonEncoder;

    static protected $jsonEncodeMethod = [];
    /**
     * Name on the rendered form.
     * @var string
     */
    protected $formName;
    /**
     * Text labels for this element
     * @var \Abivia\NextForm\Data\Labels
     */
    protected $labels;
    /**
     * Post-translation text labels for this element
     * @var \Abivia\NextForm\Data\Labels
     */
    protected $labelsTranslated;
    /**
     * Flag indicating if the static context has been initialized.
     * @var bool
     */
    static private $staticInit;

    public function __construct() {
        parent::__construct();
        if (!self::$staticInit) {
            self::$jsonEncodeMethod = parent::$jsonEncodeMethod;
            self::$jsonEncodeMethod['labels'] = ['drop:null'];
            self::$staticInit = true;
        }
    }

    protected function configureClassMap($property, $value) {
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

    public function getFormName() {
        return $this -> formName;
    }

    public function getLabels($translated = false) : \Abivia\NextForm\Data\Labels {
        if ($translated) {
            return $this -> labelsTranslated === null ? new Labels : $this -> labelsTranslated;
        }
        return $this -> labels === null ? new Labels : $this -> labels;
    }

    public function translate(Translator $translate) {
        if ($this -> labels === null) {
            $this -> labelsTranslated = null;
        } else {
            $this -> labelsTranslated = $this -> labels -> translate($translate);
        }
    }

    public function setFormName($name) {
        $this -> formName = $name;
        return $this;
    }

}