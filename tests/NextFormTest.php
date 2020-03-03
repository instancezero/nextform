<?php

use Abivia\NextForm\NextForm;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Form;

/**
 * @covers Abivia\NextForm\NextForm
 */
class NextFormTest extends \PHPUnit\Framework\TestCase {

    public function testInstantiation() {
        $obj = new NextForm();
		$this->assertInstanceOf('\Abivia\NextForm\NextForm', $obj);
    }

    /**
     * This test belongs somewhere else
     */
    public function testBinding()
    {
       $schema = Schema::fromFile(__DIR__ . '/test-data/test-schema.json');
       $form = Form::fromFile(__DIR__ . '/test-data/newform.json');
       $manager = new NextForm();
       $manager->addSchema($schema)->addForm($form);
       $manager->bind();
       $data = $manager->getData();
       $this->assertTrue(true);
    }

}
