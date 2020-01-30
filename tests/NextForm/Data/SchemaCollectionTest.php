<?php

use Abivia\NextForm\Contracts\SchemaInterface;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\SchemaCollection;
use Abivia\NextForm\Data\Segment;

class MockSchema implements SchemaInterface
{
    public $segments;
    protected $tag;

    public function __construct($tag) {
        $this->tag = $tag;
        $this->segments = [$this->tag . '_1', $this->tag . '_2'];
    }

    /**
     * Get the default schema settings
     * @param string $setting Gets the specified setting. If missing, all settings are returned.
     * @return mixed
     */
    public function getDefault($setting = null)
    {
        return $this->tag;
    }

    /**
     * Convenience function to fetch a property from a segment.
     * @param mixed $segProp A segment name, segment/property or [segment, property].
     * @param string $name Property name. Only required if $segProp is just a segment name.
     * @return \Abivia\NextForm\Data\Property|null Null if the property doesn't exist.
     */
    public function getProperty($segProp, $name = '') : ?Property
    {
        $composite = is_array($segProp) ? implode('/', $segProp) : $segProp;
        $composite = "{$this->tag}/$composite/$name";
        $result = new Property();
        $result->setName($composite);
        return $result;
    }

    /**
     * Get a segment by name.
     * @param string $segName Name of the segment to retrieve
     * @return \Abivia\NextForm\Data\Segment|null Null if the segment does not exist.
     */
    public function getSegment($segName) : ?Segment
    {
        if (in_array($segName, $this->segments)) {
            return new Segment();
        }
        return null;
    }

    /**
     * Get a list of segment names.
     * @return array A list of defined segment names.
     */
    public function getSegmentNames()
    {
        return $this->segments;
    }

    /**
     * Set a segment in the schema.
     * @param string $segName Name of the segment.
     * @param \Abivia\NextForm\Data\Segment $segment Segment contents.
     * @return \self
     */
    public function setSegment($segName, Segment $segment) : Segment
    {
        return $this;
    }

}

/**
 * @covers \Abivia\NextForm\Data\SchemaCollection
 */
class DataSchemaCollectionTest extends \PHPUnit\Framework\TestCase
{

	public function testInstantiation() {
        $obj = new SchemaCollection();
		$this->assertInstanceOf('\Abivia\NextForm\Data\SchemaCollection', $obj);
	}

    public function testAddOneSchema() {
        $obj = new SchemaCollection(new MockSchema('one'));
        $this->assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj->getSegment('one_1'));
        $this->assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj->getSegment('one_2'));
        $this->assertNull($obj->getSegment('foo'));
    }

    public function testAddTwoSchemas() {
        $obj = new SchemaCollection(new MockSchema('one'));
        $obj->addSchema(new MockSchema('two'));
        $this->assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj->getSegment('one_1'));
        $this->assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj->getSegment('one_2'));
        $this->assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj->getSegment('two_1'));
        $this->assertInstanceOf('\Abivia\NextForm\Data\Segment', $obj->getSegment('two_2'));
        $this->assertNull($obj->getSegment('foo'));
    }

    public function testAddSchemaConflict() {
        $obj = new SchemaCollection(new MockSchema('one'));
        $this->expectException('\RuntimeException');
        $obj->addSchema(new MockSchema('one'));
    }

    public function testDefaultTwoSchemas() {
        $obj = new SchemaCollection(new MockSchema('one'));
        $obj->addSchema(new MockSchema('two'));
        $this->assertEquals('one', $obj->getDefault('one_1'));
        $this->assertEquals('one', $obj->getDefault('one_2'));
        $this->assertEquals('two', $obj->getDefault('two_2'));
        $this->expectException('\RuntimeException');
        $obj->getDefault('foo');
    }

    public function testGetProperty() {
        $obj = new SchemaCollection(new MockSchema('one'));
        $prop = $obj->getProperty('one_1/p1');
        $this->assertInstanceOf('\Abivia\NextForm\Data\Property', $prop);
        $this->assertEquals('one/one_1/p1', $prop->getName());
        $this->assertNull($obj->getProperty('two_1/p1'));
    }

    public function testGetPropertyTwoSchemas() {
        $obj = new SchemaCollection(new MockSchema('one'));
        $obj->addSchema(new MockSchema('two'));
        $prop = $obj->getProperty('one_1/p1');
        $this->assertInstanceOf('\Abivia\NextForm\Data\Property', $prop);
        $this->assertEquals('one/one_1/p1', $prop->getName());
        $prop = $obj->getProperty('two_1/p9');
        $this->assertEquals('two/two_1/p9', $prop->getName());
    }

}
