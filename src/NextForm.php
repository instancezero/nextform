<?php

namespace Abivia;

use Abivia\NextForm\Contracts\Access as Access;
use Abivia\NextForm\Contracts\Renderer as Renderer;
use Abivia\NextForm\Element\ContainerElement;
use Abivia\NextForm\Element\Element;
use Abivia\NextForm\Element\FieldElement;

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 *
 */
class NextForm implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    public const SEGMENT_DELIM = '/';

    protected $access;
    /**
     * The list of all elements in the form
     * @var array
     */
    protected $allElements = [];
    protected $elements;
    /**
     * Counter used to assign HTML identifiers
     * @var int
     */
    static protected $htmlId = 0;
    static protected $jsonEncodeMethod = [
        'name' => [],
        'useSegment' => ['drop:blank'],
        'show' => ['method:jsonCollapseShow','drop:blank'],
        'elements' => [],
    ];
    protected $id;
    protected $name;
    /**
     * Maps form names to form elements/
     * @var array
     */
    protected $nameMap;
    /**
     * Maps schema objects to form elements/
     * @var array
     */
    protected $objectMap;
    protected $renderer;
    protected $schemaIsLinked;
    protected $translate;
    protected $useSegment = '';
    /**
     * Settings that affect how the form is displayed, set individually with show()
     * @var array
     */
    protected $show = [];

    public function __construct() {
        $this -> access = new \Abivia\NextForm\Access\BasicAccess;
        $this -> show = [];
    }

    protected function assignNames() {
        $this -> nameMap = [];
        $containerCount = 0;
        foreach ($this -> allElements as $element) {
            if ($element instanceof FieldElement) {
                $baseName = str_replace('/', '_', $element -> getObject());
                $name = $baseName;
                $confirmName = $baseName . '_confirm';
                $append = 0;
                while (isset($this -> nameMap[$name]) || isset($this -> nameMap[$confirmName])) {
                    $name = $baseName . '_' . ++$append;
                    $confirmName = $name . '_' . $append . '_confirm';
                }
                $this -> nameMap[$name] = $element;
                $element -> setFormName($name);
            } elseif ($element instanceof ContainerElement) {
                $baseName = 'container_';
                $name = $baseName;
                while (isset($this -> nameMap[$name])) {
                    $name = $baseName . ++$containerCount;
                }
                $this -> nameMap[$name] = $element;
                $element -> setFormName($name);
            }
        }
        $this -> schemaIsLinked = true;
        return $this;
    }

    /**
     * Connect data elements in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     * @return $this
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema) {
        $this -> objectMap = [];
        foreach ($this -> elements as $element) {
            $element -> bindSchema($schema);
        }
        $this -> schemaIsLinked = true;
        return $this;
    }

    /**
     * Reset the static context
     */
    static public function boot() {
        self::$htmlId = 0;
    }

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
     * @return \Abivia\NextForm
     * @throws RuntimeException
     */
    static public function fromFile($formFile) {
        $form = new NextForm;
        if (!$form -> configure(json_decode(file_get_contents($formFile)), true)) {
            throw new \RuntimeException(
                'Failed to load ' . $formFile . "\n"
                . implode("\n", $schema -> configureErrors)
            );
        }
        return $form;
    }

    public function generate($options) {
        $this -> options($options);
        $options['id'] = $this -> id;
        $options['name'] = $this -> name;
        $this -> assignNames();
        $this -> renderer -> setShow($this -> show);
        $pageData = $this -> renderer -> start($options);
        foreach ($this -> elements as $element) {
            $pageData -> merge($element -> generate($this -> renderer, $this -> access, $this -> translate));
        }
        $pageData -> close();
        return $pageData;
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

    public function getId() {
        return $this -> id;
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
     * Turn a string into a valid HTML identifier, or make one up
     * @param string $name
     * @return string
     */
    static public function htmlIdentifier($name = '', $appendId = false) {
        if ($name == '') {
            $name = 'nf-' . ++self::$htmlId;
        } else {
            if ($appendId) {
                $name .= '-' . ++self::$htmlId;
            }
            $name = preg_replace('/[^a-z0-9\-]/i', '-', $name);
            $name = preg_replace('/^[^a-z0-9\-]/i', 'nf-\1', $name);
        }
        return $name;
    }

    /**
     * Convert show settings to string.
     * @param array $show
     * @return string
     */
    static public function jsonCollapseShow($show, $defaultScope = 'form') {
        foreach ($show as $scope => $settings) {
            foreach ($settings as $setting => $info) {
                $info = implode(':', $info);
                $show[$setting] = ($scope == $defaultScope ? '' : $scope . '.')
                    . $setting . ':' . $info;
            }
        }
        $rules = implode('|', $show);
        return $rules;
    }

    protected function options($options) {
        if (isset($options['name'])) {
            $this -> name = $options['name'];
        }
        if (isset($options['id'])) {
            $this -> id = $options['id'];
        } elseif ($this -> name != '') {
            $this -> id = self::htmlIdentifier($this -> name);
        } else {
            $this -> id = 'form' . ++self::$htmlId;
        }
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
     * Add a form element to the list of all elements.
     * @param Element $element
     * @return $this
     */
    public function registerElement($element) {
        if (!in_array($element, $this -> allElements)) {
            $this -> allElements[] = $element;
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

    public function setAccess(Access $access) {
        $this -> access = $access;
    }

    public function setRenderer(Renderer $renderer) {
        $this -> renderer = $renderer;
    }

    public function setShow($settings) {
        // Show settings are just saved and passed to the renderer
        $this -> show = $settings;
    }

    public function setTranslator(Translator $translate) {
        $this -> translate = $translate;
    }

    public function setUser($user) {
        $this -> access -> setUser($user);
    }

    public function show($key, $value) {
        // Visual settings are just saved and passed to the renderer
        $this -> show[$key] = $value;
    }

    /**
     * Break a "show" string down into a settings array, adding defaults if possible.
     * @param string $text String of the form scope1.setting1:p1:p2...|scope2.setting2:p1:p2...
     * @return array A list of argument arrays indexed by setting.
     */
    static public function tokenizeShow($text, $defaultScope = 'form') {
        $exprs = explode('|', $text);
        $settings = [];
        foreach ($exprs as $clause) {
            $parts = explode(':', $clause);
            $first = explode('.', array_shift($parts));
            if (count($first) == 1) {
                $scope = $defaultScope;
                $setting = $first[0];
            } else {
                $scope = array_shift($first);
                $setting = implode('.', $first);
            }
            if (!isset($settings[$scope])) {
                $settings[$scope] = [];
            }
            $settings[$scope][$setting] = $parts;
        }
        return $settings;
    }

}
