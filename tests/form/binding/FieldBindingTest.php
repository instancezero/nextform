<?php

use Abivia\NextForm\Manager;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Form;

use Abivia\NextForm\Form\Element\FieldElement;


/**
 * @covers Abivia\NextForm\Form\Binding\FieldBinding
 */
class FieldBindingTest extends \PHPUnit\Framework\TestCase
{
    public function testInstantiation() {
        $this->assertTrue(true);
    }
    /**
     * This test belongs somewhere else
     */
    public function debug_testBinding()
    {
       $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
       $form = Form::fromFile(__DIR__ . '/../../test-data/newform.json');
       $manager = new Manager();
       $manager->bind($form, $schema);
       $data = $manager->getData();
       $this->assertTrue(true);
    }
}
