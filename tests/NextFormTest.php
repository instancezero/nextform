<?php

require_once __DIR__ . '/test-tools/MockBase.php';
require_once __DIR__ . '/test-tools/MockTranslate.php';

use Abivia\NextForm\Contracts\FormInterface;
use Abivia\NextForm\Contracts\SchemaInterface;
use Abivia\NextForm\NextForm;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Data\Segment;
use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Form\Form;
use PHPUnit\Framework\TestCase;

class NextForm_MockForm extends MockBase implements FormInterface
{

    public function bind(NextForm $manager)
    {
        self::_MockBase_log(__METHOD__, [$manager]);
        return $this;
    }

    static public function fromFile($path) : ?FormInterface
    {
        self::_MockBase_log(__METHOD__, [$path]);
        return new NextForm_MockForm();
    }

    /**
     * Get a list of top level elements in the form.
     * @return Element[]
     */
    public function getElements()
    {
        self::_MockBase_log(__METHOD__);
        return [];
    }

    public function getName()
    {
        self::_MockBase_log(__METHOD__);
        return 'someFormName';
    }

    public function getSegment() {
        self::_MockBase_log(__METHOD__);
        return 'someSegmentName';
    }
}

class NextForm_MockSchema extends MockBase implements SchemaInterface
{

    public function __construct(...$args) {
        self::_MockBase_log(__METHOD__, $args);
    }

    static public function fromFile($path) : ?SchemaInterface
    {
        self::_MockBase_log(__METHOD__, [$path]);
        return new NextForm_MockSchema();
    }

    public function getDefault($setting = null)
    {
        self::_MockBase_log(__METHOD__, [$setting]);
        return null;
    }

    public function getProperty($segProp, $name = '') : ?Property
    {
        self::_MockBase_log(__METHOD__, [$segProp, $name]);
    }

    public function getSegment($segName) : ?Segment
    {
        self::_MockBase_log(__METHOD__, [$segName]);
        return null;
    }

    public function getSegmentNames()
    {
        self::_MockBase_log(__METHOD__);
        return ['someSegment'];
    }

    public function setSegment($segName, Segment $segment) : SchemaInterface
    {
        self::_MockBase_log(__METHOD__, [$segName, $segment]);
        return $this;
    }

}

class FakeAccess
{
    static public $instances = 0;

    public function __construct()
    {
        ++self::$instances;
    }
}


/**
 * @covers Abivia\NextForm\NextForm
 */
class NextFormTest extends TestCase
{

    public function testInstantiation()
    {
        $obj = new NextForm();
		$this->assertInstanceOf('\Abivia\NextForm\NextForm', $obj);
    }

    public function testWiring() {
        NextForm_MockForm::_MockBase_reset();
        NextForm_MockSchema::_MockBase_reset();
        NextForm::wire([
            'Form' => NextForm_MockForm::class,
            'Schema' => NextForm_MockSchema::class
        ]);
        $obj = new NextForm();
        $linkedForm = $obj->addForm('foo.json');
        $this->assertInstanceOf('Abivia\NextForm\LinkedForm', $linkedForm);

        $obj->addSchema('foo.json');

        $this->expectException('\RuntimeException');
        NextForm::wire(['erroneous service' => 'something']);
    }

    public function testWiringCallable() {
        NextForm::wire([
            'Access' => function () { return new FakeAccess();}
        ]);
        $expect = FakeAccess::$instances + 1;
        new NextForm();
        $this->assertEquals($expect, FakeAccess::$instances);
    }

    public function testAddForm()
    {
        NextForm_MockForm::_MockBase_reset();
        NextForm_MockSchema::_MockBase_reset();
        $obj = new NextForm();
        $obj->setOptions(['wire' => ['Form' => NextForm_MockForm::class]]);
        $obj->addForm('foo.json');
        $this->assertEquals(
            [
                ['NextForm_MockForm::fromFile', ['foo.json']],
                ['NextForm_MockForm::getName', []]
            ],
            NextForm_MockForm::_MockBase_getLog()
        );
    }

    public function testAddSchema()
    {
        NextForm_MockForm::_MockBase_reset();
        NextForm_MockSchema::_MockBase_reset();
        $obj = new NextForm();
        $obj->setOptions(['wire' => ['Schema' => NextForm_MockSchema::class]]);
        $obj->addSchema('foo.json');
        $this->assertEquals(
            [
                ['NextForm_MockSchema::fromFile', ['foo.json']],
                ['NextForm_MockSchema::__construct', []],
                ['NextForm_MockSchema::getSegmentNames', []],
                ['NextForm_MockSchema::getSegmentNames', []],
            ],
            NextForm_MockSchema::_MockBase_getLog()
        );
    }

    public function testBinding()
    {
        $schema = Schema::fromFile(__DIR__ . '/test-data/test-schema.json');
        $form = Form::fromFile(__DIR__ . '/test-data/newform.json');
        $manager = new NextForm();
        $manager->addSchema($schema)->addForm($form);
        $manager->bind();
        $data = $manager->getData();
        $this->assertEquals(['test/text' => null], $data);
    }

    public function testBindingEmpty()
    {
        $manager = new NextForm();
        $this->expectException('\RuntimeException');
        $manager->bind();
    }

    public function testGenerate()
    {
        NextForm_MockForm::_MockBase_reset();
        NextForm_MockSchema::_MockBase_reset();
        $obj = new NextForm();
        $obj->setTranslator(new MockTranslate());
        $obj->setOptions(
            [
                'wire' => [
                    'Form' => NextForm_MockForm::class,
                    'Schema' => NextForm_MockSchema::class
                ]
            ]
        );
        $obj->addSchema('foo.json');
        $page = $obj->generate('foo2.json');
        $this->assertEquals(
            [
                ['NextForm_MockSchema::fromFile', ['foo.json']],
                ['NextForm_MockSchema::__construct', []],
                ['NextForm_MockSchema::getSegmentNames', []],
                ['NextForm_MockSchema::getSegmentNames', []],
            ],
            NextForm_MockSchema::_MockBase_getLog()
        );

        // Test retrieval functions
        $this->assertEquals($page, $obj->getBlock());
        $this->assertNull($obj->getBlock('doesNotExist'));
        $this->assertInstanceOf(
            '\Abivia\NextForm\Render\Block',
            $obj->getBlock('someFormName')
        );

        $this->assertEquals($page->head, $obj->getHead());
        $this->assertEquals($page->body, $obj->getBody());
        $this->assertEquals(\implode("\n", $page->linkedFiles), $obj->getLinks());
        $this->assertEquals($page->script, $obj->getScript());
        $this->assertEquals(\implode("\n", $page->scriptFiles), $obj->getScriptFiles());
        $this->assertEquals($page->styles, $obj->getStyles());
    }

    public function testHtmlIdentifier()
    {
        NextForm::boot();
        $this->assertEquals('nf_1', NextForm::htmlIdentifier());
        $this->assertEquals('nf_2', NextForm::htmlIdentifier());
        $this->assertEquals('foo', NextForm::htmlIdentifier('foo'));
        $this->assertEquals('foo_3', NextForm::htmlIdentifier('foo', true));
        $this->assertEquals('f_o_o', NextForm::htmlIdentifier('f#o*o'));
        NextForm::boot();
        $this->assertEquals('nf_1', NextForm::htmlIdentifier());
    }

}
