<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\Manager;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Representation of a form element that accepts user input (including a button).
 */
class FieldBinding extends Binding
{
    /**
     * The list of possible values for radio/drop-down types.
     * @var array
     */
    protected $dataList;

    /**
     * A translated list of possible values for radio/drop-down types.
     * @var array
     */
    protected $dataListTranslated;

    /**
     * A data property in the form's schema.
     * @var \Abivia\NextForm\Data\Property
     */
    protected $dataProperty;

    protected $objectRef;

    /**
     * Connect data elements in a schema
     * @param Schema $schema
     * @return \self
     */
    public function bindSchema(Schema $schema) : self
    {
        $objectName = $this->getElement()->getObject();
        if (strpos($objectName, Manager::SEGMENT_DELIM) !== false) {
            list($segmentName, $objectName) = explode(Manager::SEGMENT_DELIM, $objectName);
        } elseif ($this->manager) {
            $segmentName = $this->manager->getSegment();
        }
        $this->dataProperty = $schema->getProperty($segmentName, $objectName);
        if ($this->dataProperty) {
            $this->objectRef = [$segmentName, $objectName];

            // Give the data property the ability to signal us.
            $this->dataProperty->linkBinding($this);
            if ($this->manager) {
                $this->manager->registerBinding($this);
            }

            // Get default labels from the schema, if any.
            $labels = $schema->getDefault('labels');

            // Merge or get the labels from the property.
            if ($labels) {
                $labels = $labels->merge($this->dataProperty->getLabels());
            } else {
                $labels = $this->dataProperty->getLabels();
            }

            // Merge in any overrides from the element.
            $this->labels = $labels->merge($this->element->getLabels());
            $this->labelsTranslated = $this->labels;

            // Make a copy of the data list so we can translate labels
            $this->dataList = $this->dataProperty->getPopulation()->getList();
            $this->dataListTranslated = $this->dataList;
        }
        return $this;
    }

    /**
     * Get the connected schema object, if any
     * @return Abivia\NextForm\Data\Property
     */
    public function getDataProperty() : Property
    {
        if ($this->dataProperty === null) {
            throw new \RuntimeException(
                'Attempt to get missing schema information, object ' . $this->getObject()
            );
        }
        return $this->dataProperty;
    }

    /**
     * Get the of Population/Option objects associated with the field with no hierarchy.
     * @param bool $translated Returns the translated texts, if available
     * @return \Abivia\NextForm\Data\Population\Option[]
     */
    public function getFlatList($translated = false)
    {
        if ($translated && $this->hasTranslation) {
            $source = $this->dataListTranslated;
        } else {
            $source = $this->dataList;
        }
        // Lists can only nest one level deep, so this is straightforward.
        $list = [];
        foreach ($source as $option) {
            if ($option->isNested()) {
                foreach ($option->getList() as $item) {
                    $list[] = $item;
                }
            } else {
                $list[] = $option;
            }
        }
        return $list;
    }

    /**
     * Get a hierarchical list of Population/Option objects associated with the field
     * @param bool $translated Returns the translated texts, if available
     * @return Abivia\NextForm\Data\Population\Option[]
     */
    public function getList($translated = false)
    {
        if ($translated && $this->hasTranslation) {
            $list = $this->dataListTranslated;
        } else {
            $list = $this->dataList;
        }
        return $list;
    }

    /**
     * Get the name of a bound object ("segmentName/objectName")
     * @return ?string
     */
    public function getObject() : ?string
    {
        if ($this->objectRef === null) {
            return null;
        }
        return implode(Manager::SEGMENT_DELIM, $this->objectRef);
    }

    /**
     * Translate the texts in this element.
     *
     * @param Translator $translate
     * @return \Abivia\NextForm\Form\Binding\Binding
     */
    public function translate(Translator $translate) : Binding
    {
        // Translate the data list, if any
        if ($this->dataProperty) {
            $this->dataListTranslated = $this->dataList;
            if ($this->dataProperty->getPopulation()->getTranslate()) {
                foreach ($this->dataListTranslated as $option) {
                    $option->translate($translate);
                }
            }
        }
        // Translate the labels.
        return parent::translate($translate);
    }

}