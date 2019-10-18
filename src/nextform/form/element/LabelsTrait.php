<?php

namespace Abivia\NextForm\Form\Element;

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
     * Post-merge, post-translation text labels for this element
     * @var \Abivia\NextForm\Data\Labels
     */
    protected $labelsTranslated;

    /**
     * Translate the labels.
     * @param Translator $translate
     * @return \Abivia\NextForm\Form\Element\Element
     */
    public function translate(Translator $translate) : Element
    {
        $this->labelsTranslated = $this->labelsMerged->translate($translate);
        $this->hasTranslation = true;
        return $this;
    }

}