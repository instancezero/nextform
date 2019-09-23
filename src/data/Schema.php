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

    /**
     * Default characteristics for properties in this schema.
     * @var \stdClass
     */
    protected $defaultRepo;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'defaultRepo' => 'map:default',
        'segments' => 'array',
    ];

    /**
     * List of data segments in this schema.
     * @var \Abivia\NextForm\Data\Segment[]
     */
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

    /**
     * Configure elements set in the default property.
     */
    protected function configureComplete() {
        if ($this -> defaultRepo !== null) {
            // If the source was YAML, we have an array instead of an object.
            if (is_array($this -> defaultRepo)) {
                $this -> defaultRepo = (object) $this -> defaultRepo;
            }

            // Convert a labels property into a Labels data structure
            if (isset($this -> defaultRepo -> labels)) {
                $obj = new Labels;
                if (!$obj -> configure($this -> defaultRepo -> labels, $this -> configureOptions)) {
                    $this -> configureErrors = array_merge(
                        $this -> configureErrors, $obj -> configureGetErrors()
                    );
                    return false;
                }
                $this -> defaultRepo -> labels = $obj;
            }
        }
        return true;
    }

    /**
     * Change "default" to "defaultRepo" to work around PHP keyword issue.
     * @param string $property
     * @return string
     */
    protected function configurePropertyMap($property): string {
        if ($property == 'default') {
            $property = 'defaultRepo';
        }
        return $property;
    }

    /**
     * Create a deep clone of this schema, copying all connected objects.
     * @return \Abivia\NextForm\Data\Schema
     */
    public function copy() : Schema {
        return deep_copy($this);
    }

    /**
     * Load a schema from a file.
     * @param string $schemaFile Path to the schema file.
     * @param string $format Format of the source file (json or yaml). If blank,
     *      files ending in .yaml or .yml are processed as YAML. Anything else is
     *      assumed to be JSON
     * @return \Abivia\NextForm\Data\Schema
     * @throws \RuntimeException
     */
    static public function fromFile($schemaFile, $format = '') {
        $schema = new Schema;
        if (!file_exists($schemaFile)) {
            throw new \RuntimeException(
                'Failed to load ' . $schemaFile . ", file does not exist\n"
            );
        }

        // Determine which format we have
        if (
            $format === ''
            && in_array(pathinfo($schemaFile, PATHINFO_EXTENSION), ['yaml', 'yml'])
        ) {
            $format = 'yaml';
        }

        // Read and parse the raw configuration
        if (strtolower($format) === 'yaml') {
            $rawConfig = yaml_parse_file($schemaFile);
            if ($rawConfig === false) {
                throw new \RuntimeException(
                    'Failed parsing ' . $schemaFile . " as YAML.\n"
                );
            }
        } else {
            $rawConfig = json_decode(file_get_contents($schemaFile));
            if ($rawConfig === false) {
                throw new \RuntimeException(
                    json_last_error_msg() .  ' decoding ' . $schemaFile . "\n"
                );
            }
        }

        // Convert the configuration into our data structures.
        if (!$schema -> configure($rawConfig, true)) {
            throw new \RuntimeException(
                'Failed to load ' . $schemaFile . "\n"
                . implode("\n", $schema -> configureErrors)
            );
        }

        return $schema;
    }

    /**
     * Convenience function to fetch a property from a segment.
     * @param string $segProp Either a segment name or a segment/property.
     * @param string $name Property name. Only required if $segProp is just a segment name.
     * @return \Abivia\NextForm\Data\Property|null Null if the property doesn't exist.
     */
    public function getProperty($segProp, $name = '') : ?Property {
        if (strpos($segProp, NextForm::SEGMENT_DELIM) !== false) {
            list($segProp, $name) = explode(NextForm::SEGMENT_DELIM, $segProp);
        }
        if (!isset($this -> segments[$segProp])) {
            return null;
        }
        return $this -> segments[$segProp] -> getProperty($name);
    }

    /**
     * Get a segment by name.
     * @param string $segName Name of the segment to retrieve
     * @return \Abivia\NextForm\Data\Segment|null Null if the segment does not exist.
     */
    public function getSegment($segName) : ?Segment {
        if (!isset($this -> segments[$segName])) {
            return null;
        }
        return $this -> segments[$segName];
    }

    /**
     * Set a segment in the schema.
     * @param string $segName Name of the segment.
     * @param \Abivia\NextForm\Data\Segment $segment Segment contents.
     * @return \self
     */
    public function setSegment($segName, Segment $segment) : self {
        $this -> segments[$segName] = $segment;
        return $this;
    }

}
