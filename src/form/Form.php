<?php

namespace Abivia\NextForm\Form;

use Abivia\NextForm;

use Abivia\NextForm\Element\Element;
/**
 *
 */
class Form implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\JsonEncoder;

    protected $elements;
    static protected $jsonEncodeMethod = [
        'name' => [],
        'useSegment' => ['drop:blank'],
        'elements' => [],
    ];
    protected $name;
    /**
     * Maps schema objects to form elements/
     * @var array
     */
    protected $objectMap;
    protected $schemaIsLinked;
    protected $useSegment = '';

    protected function configureInitialize() {
        // Pass an instance of the form down in Configurable's options so we can
        // access the form directly from deep within the data structures.
        $this -> configureOptions['_form'] = &$this;
    }

    protected function configureClassMap($property, $value) {
        $result = false;
        if ($property == 'elements') {
            $result = new \stdClass;
            $result -> key = '';
            $result -> className = [Element::class, 'classFromType'];
        }
        return $result;
    }

    /**
     * Generate a form object from a file
     * @param string $formFile
     * @return \Abivia\NextForm\Form\Form
     * @throws RuntimeException
     */
    static public function fromFile($formFile) {
        $form = new Form;
        if (!$form -> configure(json_decode(file_get_contents($formFile)), true)) {
            throw new RuntimeException(
                'Failed to load ' . $formFile . "\n"
                . implode("\n", $schema -> configureErrors)
            );
        }
        return $form;
    }

    /**
     * Get all the data objects from the form.
     * @return array Data elements indexed by object name
     */
    public function getData() {
        $data = [];
        // The first element should have the value... there should only be one value.
        foreach ($this -> objectMap as $objectName => $list) {
            $data[$objectName] = $list[0] -> getValue();
        }
        return $data;
    }

    public function getName() {
        return $this -> name;
    }

    public function getSegment() {
        return $this -> useSegment;
    }

    /**
     * Get all the data objects in the specified segment from the form.
     * @param type $segment
     * @return array Data elements indexed by object name
     */
    public function getSegmentData($segment) {
        $prefix = $segment . NextForm::SEGMENT_DELIM;
        $prefixLen = strlen($segment . NextForm::SEGMENT_DELIM);
        $data = [];
        // The first element should have the value... there should only be one value.
        foreach ($this -> objectMap as $objectName => $list) {
            if (substr($objectName, 0, $prefixLen) == $prefix) {
                $data[substr($objectName, $prefixLen)] = $list[0] -> getValue();
            }
        }
        return $data;
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     * @return $this
     */
    public function linkSchema($schema) {
        $this -> objectMap = [];
        foreach ($this -> elements as $element) {
            $element -> linkSchema($schema);
        }
        $this -> schemaIsLinked = true;
        return $this;
    }

    /**
     * Populate form elements.
     * @param array $data Values indexed by schema object ID.
     * @param string $segment Optional segment prefix.
     * @throws LogicException
     * @return $this
     */
    public function populate($data, $segment = '') {
        if (!$this -> schemaIsLinked) {
            throw new LogicException('Form not linked to schema.');
        }
        foreach ($data as $field => $value) {
            if ($segment !== '') {
                $field = $segment . NextForm::SEGMENT_DELIM . $field;
            }
            if (!isset($this -> objectMap[$field])) {
                continue;
            }
            foreach ($this -> objectMap[$field] as $element) {
                $element -> setValue($value);
            }
        }
        return $this;
    }

    /**
     * Add an element in the form to the object map.
     * @param Element $element
     * @return $this
     */
    public function registerObject($element) {
        $object = $element -> getObject();
        if (!isset($this -> objectMap[$object])) {
            $this -> objectMap[$object] = [];
        }
        $this -> objectMap[$object][] = $element;
        return $this;
    }

}
