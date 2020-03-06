<?php

use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Data\SchemaCollection;
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
     * @var Form
     */
    public $simpleForm;

    /**
     *
     * @var SchemaCollection
     */
    public $simpleSchema;

    public function setUp() : void {
        $simpleSchema = '{'
            . '"segments": ['
                . '{'
                    . '"name": "test",'
                    . '"objects": ['
                        . '{'
                            . '"name": "text",'
                            . '"presentation": {"type": "text"}'
                        . '}'
                    . ']'
                . '}'
            . ']'
        . '}';
        $this->simpleSchema = new SchemaCollection(Schema::fromJson($simpleSchema));

        $simpleForm = '{'
            . '"name":"registrationForm",'
            . '"elements":['
                . '"test.text",'
                . '"test.not-defined",'
                . '{'
                    . '"name": "intro",'
                    . '"type": "static",'
                    . '"value": "static text",'
                    . '"display": true'
                . '}'
            . ']'
        . '}';
        $this->simpleForm = Form::fromJson($simpleForm);
    }

    public function testBinding() {
        $element = new FieldElement();
        $element->configure('test.text');
        $binding = Binding::fromElement($element);
        $binding->bindSchema($this->simpleSchema);
        $prop = $this->simpleSchema->getProperty('test.text');
        $this->assertEquals($prop, $binding->getDataProperty());
    }

    public function testBindingNotDefined() {
        $element = new FieldElement();
        $element->configure('test.undefinedProperty');
        $binding = Binding::fromElement($element);
        $this->expectException('\RuntimeException');
        $binding->bindSchema($this->simpleSchema);
    }

}
