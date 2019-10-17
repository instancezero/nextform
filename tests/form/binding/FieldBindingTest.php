<?php

use Abivia\NextForm;
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
    /**
     *
     */
    public function testLabels()
    {
       $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
       $form = Form::fromFile(__DIR__ . '/../../test-data/newform.json');
       $manager = new NextForm();
       $manager -> setSchema($schema);
       $manager -> setForm($form);
       $callBind = function () { $this->bind(); };
       $callBind = $callBind->bindTo($manager, $manager);
       $callBind();
    }
}
