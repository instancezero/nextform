<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Segment;

/**
 * Describes the schema of a data set.
 */
interface SchemaInterface
{

    /**
     * Get the default schema settings
     * @param string $setting Gets the specified setting. If missing, all settings are returned.
     * @return mixed
     */
    public function getDefault($setting = null);

    /**
     * Convenience function to fetch a property from a segment.
     * @param mixed $segProp A segment name, segment/property or [segment, property].
     * @param string $name Property name. Only required if $segProp is just a segment name.
     * @return \Abivia\NextForm\Data\Property|null Null if the property doesn't exist.
     */
    public function getProperty($segProp, $name = '') : ?Property;

    /**
     * Get a segment by name.
     * @param string $segName Name of the segment to retrieve
     * @return \Abivia\NextForm\Data\Segment|null Null if the segment does not exist.
     */
    public function getSegment($segName) : ?Segment;

    /**
     * Get a list of segment names.
     * @return array A list of defined segment names.
     */
    public function getSegmentNames();

    /**
     * Set a segment in the schema.
     * @param string $segName Name of the segment.
     * @param \Abivia\NextForm\Data\Segment $segment Segment contents.
     * @return $this
     */
    public function setSegment($segName, Segment $segment) : SchemaInterface;

}
