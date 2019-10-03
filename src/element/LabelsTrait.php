<?php

namespace Abivia\NextForm\Element;

use Abivia\NextForm\Data\Labels;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Elements with a label attribute
 */
trait LabelsTrait
{

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

    public function getLabels($translated = false)
    : ?\Abivia\NextForm\Data\Labels {
        if ($translated && $this->hasTranslation) {
            return $this->labelsTranslated;
        }
        return $this->labels;
    }

    /**
     * Set the value for a label element.
     * @param string $labelName Name of the text to be set.
     * @param string $text
     */
    public function setLabel($labelName, $text)
    {
        if ($this->labels === null) {
            $this->labels = new Labels();
        }
        if ($this->labelsMerged === null) {
            $this->labelsMerged = clone $this->labels;
        }
        $this->labels->set($labelName, $text);
        $this->labelsMerged->set($labelName, $text);
    }

    /**
     * Translate the labels.
     * @param Translator $translate
     * @return \Abivia\NextForm\Element\Element
     */
    public function translate(Translator $translate) : Element
    {
        $this->labelsTranslated = $this->labelsMerged->translate($translate);
        $this->hasTranslation = true;
        return $this;
    }

}