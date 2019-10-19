<?php

use Abivia\NextForm\Manager;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Form;

class ManagerTest extends \PHPUnit\Framework\TestCase {

    public function testInstantiation() {
        $obj = new Manager();
		$this->assertInstanceOf('\Abivia\NextForm\Manager', $obj);
    }

    /**
     * This test belongs somewhere else
     */
    public function testBinding()
    {
       $schema = Schema::fromFile(__DIR__ . '/test-data/test-schema.json');
       $form = Form::fromFile(__DIR__ . '/test-data/newform.json');
       $manager = new Manager();
       $manager->bind($form, $schema);
       $data = $manager->getData();
       $this->assertTrue(true);
    }

}
