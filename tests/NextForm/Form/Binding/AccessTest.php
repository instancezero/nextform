<?php

use Abivia\NextForm\NextForm;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Form;

// render object that just records the form element names
require_once __DIR__ . '/../../../test-tools/LoggingRender.php';
require_once __DIR__ . '/../../../test-tools/MockTranslate.php';

class Access_MockAccess implements AccessInterface
{
    /**
     * Stock permissions
     * @var array
     */
    public $permissions = [
        ':button' => 'write',
        ':simpleCell' => 'write',
        'elementTest:cellOne' => 'write',
        'elementTest:cellTwo' => 'write',
        'elementTest:textOne' => 'write',
        ':htmlOne' => 'write',
        ':SectionOne' => 'write',
        'elementTest:textSectionOne' => 'write',
        ':cellInSectionOne' => 'write',
        'elementTest:cellThree' => 'write',
        'elementTest:cellFour' => 'write',
        'elementTest:cellFive' => 'write',
        ':staticOne' => 'write',
    ];

    public function allows($segment, $objectName, $operation, $user = null) : bool
    {
        return $this->permissions["$segment:$objectName"] === $operation;
    }

    public function setUser($user)
    {

    }

}

/**
 * @covers Abivia\NextForm\Form\Binding\Binding
 */
class AccessTest extends \PHPUnit\Framework\TestCase
{
    public $access;
    public $expect;
    public $manager;
    public $sectionParts;

    public function setUp() : void {
        NextForm::boot();

        $manager = new NextForm();
        $manager->setOptions(
            [
                'segmentNameMode' => 'off',
                'wire' => [
                    'Render' => LoggingRender::class,
                    'Translate' => MockTranslate::class,
                ]
            ]
        );
        $this->access = new Access_MockAccess();
        $manager->setAccess($this->access);
        $manager->addForm(
            Form::fromFile(__DIR__ . '/element-test-form.json'),
            ['action' => 'myform.php']
        );
        $manager->addSchema(Schema::fromFile(__DIR__ . '/element-test-schema.json'));
        $this->manager = $manager;

        $this->expect = [
            'button/button' => 'write',
            'container_1/simpleCell' => 'write',
            'elementTest_cellOne/cellFieldOne' => 'write',
            'elementTest_cellTwo/cellFieldTwo' => 'write',
            'elementTest_textOne/textOne' => 'write',
            'htmlOne/htmlOne' => 'write',
            'container_2/SectionOne' => 'write',
            'elementTest_textSectionOne/textInSectionOne' => 'write',
            'container_3/cellInSectionOne' => 'write',
            'elementTest_cellThree/cellFieldThree' => 'write',
            'elementTest_cellFour/cellFieldFour' => 'write',
            'elementTest_cellFive/cellFieldFive' => 'write',
            'staticOne/staticOne' => 'write',
        ];
        $this->sectionParts = [
            'container_2/SectionOne',
            'elementTest_textSectionOne/textInSectionOne',
            'container_3/cellInSectionOne',
            'elementTest_cellThree/cellFieldThree',
            'elementTest_cellFour/cellFieldFour',
            'elementTest_cellFive/cellFieldFive',
        ];
    }

    /**
     * Default access grants write access to everything.
     */
    public function testAllWrite()
    {
        $this->manager->generate();
        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * Hide access on a button.
     */
    public function testButtonHide()
    {
        $this->access->permissions[':button'] = 'hide';
        $this->manager->generate();
        $this->expect['button/button'] = 'hide';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * None access on a button.
     */
    public function testButtonNone()
    {
        $this->access->permissions[':button'] = 'none';
        $this->manager->generate();
        $this->expect['button/button'] = 'none';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * View access on a button.
     */
    public function testButtonView()
    {
        $this->access->permissions[':button'] = 'view';
        $this->manager->generate();
        $this->expect['button/button'] = 'view';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * Hide access on a cell inherited by contained elements
     */
    public function testCellHide()
    {
        $this->access->permissions[':simpleCell'] = 'hide';
        $this->manager->generate();
        $this->expect['container_1/simpleCell'] = 'hide';
        $this->expect['elementTest_cellOne/cellFieldOne'] = 'hide';
        $this->expect['elementTest_cellTwo/cellFieldTwo'] = 'hide';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * None access on a cell inherited by contained elements
     */
    public function testCellNone()
    {
        $this->access->permissions[':simpleCell'] = 'none';
        $this->manager->generate();
        $this->expect['container_1/simpleCell'] = 'none';
        $this->expect['elementTest_cellOne/cellFieldOne'] = 'none';
        $this->expect['elementTest_cellTwo/cellFieldTwo'] = 'none';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * View access on a cell inherited by contained elements
     */
    public function testCellView()
    {
        $this->access->permissions[':simpleCell'] = 'view';
        $this->manager->generate();
        $this->expect['container_1/simpleCell'] = 'view';
        $this->expect['elementTest_cellOne/cellFieldOne'] = 'view';
        $this->expect['elementTest_cellTwo/cellFieldTwo'] = 'view';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * Hide access on a simple text field.
     */
    public function testFieldHide()
    {
        $this->access->permissions['elementTest:textOne'] = 'hide';
        $this->manager->generate();
        $this->expect['elementTest_textOne/textOne'] = 'hide';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * None access on a simple text field.
     */
    public function testFieldNone()
    {
        $this->access->permissions['elementTest:textOne'] = 'none';
        $this->manager->generate();
        $this->expect['elementTest_textOne/textOne'] = 'none';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * View access on a simple text field.
     */
    public function testFieldView()
    {
        $this->access->permissions['elementTest:textOne'] = 'view';
        $this->manager->generate();
        $this->expect['elementTest_textOne/textOne'] = 'view';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * Hide access on HTML.
     */
    public function testHtmlHide()
    {
        $this->access->permissions[':htmlOne'] = 'hide';
        $this->manager->generate();
        $this->expect['htmlOne/htmlOne'] = 'hide';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * None access on HTML.
     */
    public function testHtmlNone()
    {
        $this->access->permissions[':htmlOne'] = 'none';
        $this->manager->generate();
        $this->expect['htmlOne/htmlOne'] = 'none';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * View access on HTML.
     */
    public function testHtmlView()
    {
        $this->access->permissions[':htmlOne'] = 'view';
        $this->manager->generate();
        $this->expect['htmlOne/htmlOne'] = 'view';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * Hide access on a nested text field.
     */
    public function testNestedFieldHide()
    {
        $this->access->permissions['elementTest:cellTwo'] = 'hide';
        $this->manager->generate();
        $this->expect['elementTest_cellTwo/cellFieldTwo'] = 'hide';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * None access on a nested text field.
     */
    public function testNestedFieldNone()
    {
        $this->access->permissions['elementTest:cellTwo'] = 'none';
        $this->manager->generate();
        $this->expect['elementTest_cellTwo/cellFieldTwo'] = 'none';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * View access on a nested text field.
     */
    public function testNestedFieldView()
    {
        $this->access->permissions['elementTest:cellTwo'] = 'view';
        $this->manager->generate();
        $this->expect['elementTest_cellTwo/cellFieldTwo'] = 'view';

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * Hide access on section.
     */
    public function testSectionHide()
    {
        $this->access->permissions[':SectionOne'] = 'hide';
        $this->manager->generate();
        foreach ($this->sectionParts as $part) {
            $this->expect[$part] = 'hide';
        }

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * None access on section.
     */
    public function testSectionNone()
    {
        $this->access->permissions[':SectionOne'] = 'none';
        $this->manager->generate();
        foreach ($this->sectionParts as $part) {
            $this->expect[$part] = 'none';
        }

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

    /**
     * View access on section.
     */
    public function testSectionView()
    {
        $this->access->permissions[':SectionOne'] = 'view';
        $this->manager->generate();
        foreach ($this->sectionParts as $part) {
            $this->expect[$part] = 'view';
        }

        $this->assertEquals($this->expect, LoggingRender::getLog());
    }

}