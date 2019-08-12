<?php

namespace Abivia\NextForm\Element;

use Illuminate\Contracts\Translation\Translator as Translator;
use Abivia\NextForm\Data\Labels;

/**
 * Elements with a label attribute
 */
trait HasLabels {

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

    public function getLabels($translated = false) : ?\Abivia\NextForm\Data\Labels {
        if ($translated && $this -> hasTranslation) {
            return $this -> labelsTranslated;
        }
        return $this -> labels;
    }

    public function translate(Translator $translate) {
        $this -> labelsTranslated = $this -> labelsMerged -> translate($translate);
        $this -> hasTranslation = true;
    }

    public function setLabel($labelName, $text) {
        if ($this -> labels === null) {
            $this -> labels = new Labels;
        }
        if ($this -> labelsMerged === null) {
            $this -> labelsMerged = clone $this -> labels;
        }
        $this -> labels -> set($labelName, $text);
        $this -> labelsMerged -> set($labelName, $text);
    }

}