<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm\Data\Labels;

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Elements with a label attribute
 */
abstract class NamedElement Extends Element {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    static protected $jsonEncodeMethod = [];
    /**
     * Name on the rendered form.
     * @var string
     */
    protected $formName;
    /**
     * Set when an explicit translation has been performed
     * @var bool
     */
    protected $hasTranslation = false;
    /**
     * Text labels for this element as defined in the form
     * @var \Abivia\NextForm\Data\Labels
     */
    protected $labels;
    /**
     * Text labels for this element after merging with any data source
     * @var \Abivia\NextForm\Data\Labels
     */
    protected $labelsMerged;
    /**
     * Post-merge, post-translation text labels for this element
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
            self::$jsonEncodeMethod['labels'] = ['drop:empty', 'drop:null'];
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

    protected function configureComplete() {
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
        if ($this -> formName === null) {
            if ($this -> name != '') {
                $this -> formName = $this -> name;
            } elseif ($this -> autoId != '') {
                $this -> formName = $this -> autoId;
            }
        }
        return $this -> formName;
    }

    public function getLabels($translated = false) : \Abivia\NextForm\Data\Labels {
        if ($translated && $this -> hasTranslation) {
            return $this -> labelsTranslated;
        }
        return $this -> labels;
    }

    public function translate(Translator $translate) {
        $this -> labelsTranslated = $this -> labelsMerged -> translate($translate);
        $this -> hasTranslation = true;
    }

    public function setFormName($name) {
        $this -> formName = $name;
        return $this;
    }

    public function setLabel($labelName, $text) {
        $this -> labels -> set($labelName, $text);
        $this -> labelsMerged -> set($labelName, $text);
    }

}