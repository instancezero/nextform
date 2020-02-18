<?php

namespace Abivia\NextForm\Data;

use Abivia\NextForm\Contracts\SchemaInterface;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\NextForm;

use function DeepCopy\deep_copy;

/**
 * Contains a set of schemas.
 */
class SchemaCollection implements \IteratorAggregate
{

    /**
     * List of schemas, indexed by segment.
     * @var \Abivia\NextForm\Data\Schema[]
     */
    protected $segments;

    public function __construct(?SchemaInterface $schema = null)
    {
        if ($schema) {
            $this->addSchema($schema);
        }
    }

    /**
     * Add a schema to the collection. Segments in the schema must have
     * unique names. The add is an atomic operation, either all or no segments
     * are added.
     *
     * @param Schema $schema The schema to be added.
     * @return $this
     * @throws \RuntimeException If there is a name conflict.
     */
    public function addSchema(SchemaInterface $schema)
    {
        $segmentNames = $schema->getSegmentNames();
        foreach ($segmentNames as $segmentName) {
            if (isset($this->segments[$segmentName])) {
                throw new \RuntimeException(
                    "Segment $segmentName is already defined."
                );
            }
        }
        foreach ($segmentNames as $segmentName) {
            $this->segments[$segmentName] = $schema;
        }
        return $this;
    }

    /**
     * Create a deep clone of this schema collection, copying all connected
     * objects.
     * @return \Abivia\NextForm\Data\SchemaCollection
     */
    public function copy() : SchemaCollection
    {
        return deep_copy($this);
    }

    /**
     * Get the default schema settings
     * @param string $segmentName Name of the segment to query.
     * @param string $setting Gets the specified setting. If missing, all
     *  settings for the segment are returned.
     * @return mixed
     */
    public function getDefault($segmentName, $setting = null)
    {
        if (!isset($this->segments[$segmentName])) {
            throw new \RuntimeException("Unknown segment $segmentName");
        }
        return $this->segments[$segmentName]->getDefault($setting);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->segments);
    }

    /**
     * Convenience function to fetch a property from a segment.
     * @param mixed $segProp A segment name, segment/property or [segment, property].
     * @param string $name Property name. Only required if $segProp is just a segment name.
     * @return \Abivia\NextForm\Data\Property|null Null if the property doesn't exist.
     */
    public function getProperty($segProp, $name = '') : ?Property
    {
        if (is_array($segProp)) {
            $name = $segProp[1];
            $segment = $segProp[0];
        } elseif (strpos($segProp, NextForm::SEGMENT_DELIM) !== false) {
            list($segment, $name) = explode(NextForm::SEGMENT_DELIM, $segProp);
        } else {
            $segment = $segProp;
        }
        if (!isset($this->segments[$segment])) {
            return null;
        }
        return $this->segments[$segment]->getProperty($segment, $name);
    }

    /**
     * Get a segment by name.
     * @param string $segName Name of the segment to retrieve
     * @return \Abivia\NextForm\Data\Segment|null Null if the segment does not exist.
     */
    public function getSegment($segName) : ?Segment
    {
        if (!isset($this->segments[$segName])) {
            return null;
        }
        return $this->segments[$segName]->getSegment($segName);
    }

}
