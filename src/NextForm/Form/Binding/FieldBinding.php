<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\Manager;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Render\Block;
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

    protected function bindProperty(Schema $schema, $segmentName, $objectName) {
        $resolvedName = $segmentName . Manager::SEGMENT_DELIM . $objectName;
        $this->dataProperty = $schema->getProperty($segmentName, $objectName);
        if ($this->dataProperty === null) {
            throw new \RuntimeException('Unable to bind '. $resolvedName .', property not found.');
        }
        $this->objectRef = [$segmentName, $objectName];

        // Give the data property the ability to signal us.
        $this->dataProperty->linkBinding($this);

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

    /**
     * Connect data elements in a schema
     * @param Schema $schema
     * @return \self
     */
    public function bindSchema(Schema $schema) : self
    {
        // Get the object from the element and add any default segment.
        $objectName = $this->getElement()->getObject();
        if (strpos($objectName, Manager::SEGMENT_DELIM) !== false) {
            list($segmentName, $objectName) = explode(Manager::SEGMENT_DELIM, $objectName);
        } elseif ($this->manager) {
            $segmentName = $this->manager->getSegment();
        }

        // Connect to the requested property in the schema.
        if ($objectName !== '') {
            $this->bindProperty($schema, $segmentName, $objectName);
        }
        if ($this->manager) {
            $this->manager->registerBinding($this);
        }
        return $this;
    }

    /**
     * Use a renderer to turn this element into part of the form.
     *
     * @param RenderInterface $renderer Any Render object.
     * @param AccessInterface $access Any access control object.
     * @param array $options Options: accessOverride to override default access.
     * @return Block
     */
    public function generate(
        RenderInterface $renderer,
        AccessInterface $access,
        $options = []
    ) : Block {
        $options = $this->checkAccess(
            $access, $this->objectRef[0], $this->objectRef[1], $options
        );
        $pageData = $renderer->render($this, $options);
        return $pageData;
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
    public function getFlatList($translated = true)
    {
        $source = $this->getList($translated);

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
     * Get this element's name on the form. If not assigned, a name is generated.
     *
     * @param $baseOnly If set, brackets are omitted.
     * @return string
     */
    public function getFormName($baseOnly = false)
    {
        $result = parent::getFormName($baseOnly);
        if ($baseOnly) {
            return $result;
        }
        $data = $this->getDataProperty();
        $type = $data->getPresentation()->getType();
        if ($type == 'checkbox') {
            if (!empty($this->dataList)) {
                $result .= '[]';
            }
        } elseif ($data->getValidation()->get('multiple')) {
            $result .= '[]';
        }
        return $result;
    }

    /**
     * Get a hierarchical list of Population/Option objects associated with the field
     * @param bool $translated Returns the translated texts, if available
     * @return Abivia\NextForm\Data\Population\Option[]
     */
    public function getList($translated = true)
    {
        if ($translated) {
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
     * Translate the texts in this binding.
     *
     * @param Translator $translator
     * @return $this
     */
    public function translate(Translator $translator = null) : Binding
    {
        parent::translate($translator);

        // Translate the data list, if any
        if ($this->dataProperty) {
            $this->dataListTranslated = [];
            if ($this->dataProperty->getPopulation()->getTranslate()) {
                foreach ($this->dataList as $option) {
                    $this->dataListTranslated[] = $option->translate($translator);
                }
            }
        }
        return $this;
    }

}