<?php

namespace Abivia\NextForm\Data;

use Abivia\NextForm;
use function DeepCopy\deep_copy;

/**
 * Describes the schema of a data set.
 */
class Schema implements \JsonSerializable {
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\Traits\JsonEncoder;

    protected $defaultRepo;
    static protected $jsonEncodeMethod = [
        'defaultRepo' => 'map:default',
        'segments' => 'array',
    ];
    protected $segments;

    /**
     * Map a property to a class.
     * @param string $property The current class property name.
     * @param mixed $value The value to be stored in the property, made available for inspection.
     * @return mixed An object containing a class name and key, or false
     * @codeCoverageIgnore
     */
    protected function configureClassMap($property, $value) {
        static $classMap = [
            'segments' => ['className' => '\Abivia\NextForm\Data\Segment', 'key' => 'getName', 'keyIsMethod' => true],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    protected function configurePropertyMap($property): string {
        if ($property == 'default') {
            $property = 'defaultRepo';
        }
        return $property;
    }

    public function copy() : Schema {
        return deep_copy($this);
    }

    static public function fromFile($schemaFile) {
        $schema = new Schema;
        if (!file_exists($schemaFile)) {
            throw new \RuntimeException(
                'Failed to load ' . $schemaFile . ", file does not exist\n"
            );
        }
        if (!$schema -> configure(json_decode(file_get_contents($schemaFile)), true)) {
            throw new \RuntimeException(
                'Failed to load ' . $schemaFile . "\n"
                . implode("\n", $schema -> configureErrors)
            );
        }
        return $schema;
    }

    public function getProperty($segName, $name = '') : ?Property {
        if (strpos($segName, NextForm::SEGMENT_DELIM) !== false) {
            list($segName, $name) = explode(NextForm::SEGMENT_DELIM, $segName);
        }
        if (!isset($this -> segments[$segName])) {
            return null;
        }
        return $this -> segments[$segName] -> getProperty($name);
    }

    public function loadDataSchema($path, $fileType = 'json') {
        if ($fileType == 'json') {
            $dataSchema = json_decode(file_get_contents($path));
            $this -> defaultRepo = new Labels;
            $this -> defaultRepo -> configure($dataSchema -> default -> labels);
            unset($dataSchema -> default);
        } elseif ($fileType == 'yaml') {
            $dataSchema = yaml_parse_file($path);
            $this -> defaultRepo = new Labels;
            $this -> defaultRepo -> configure($dataSchema['default']['labels']);
            unset($dataSchema['default']);
        }
        return $this -> configure($dataSchema, true);
    }
}
