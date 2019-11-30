<?php

use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\ButtonElement;
use Abivia\NextForm\Form\Element\CellElement;
use Abivia\NextForm\Form\Element\FieldElement;
use Abivia\NextForm\Form\Element\HtmlElement;
use Abivia\NextForm\Form\Element\SectionElement;
use Abivia\NextForm\Form\Element\StaticElement;

/**
 * Generate standard test cases for use in testing multiple render classes.
 */
class RenderCaseGenerator {

    /**
     * Iterate through various label permutations
     * @param Binding $bBase Base element used to generate cases
     * @param string $prefix Test case prefix
     * @param string $namePrefix Test label prefix
     * @return array label-none|label-inner|label-before|label-after|label-head|label-help|label-all
     */
    static protected function addLabels(Binding $bBase, $prefix = '', $namePrefix = '', $skip = []) {
        $cases = [];

        // No changes
        $cases[$prefix . 'label-none'] = [$bBase, [], $namePrefix . 'label none'];

        if (!in_array('inner', $skip)) {
            $b1 = $bBase->copy();
            $b1->setLabel('inner', 'inner');
            $cases[$prefix . 'label-inner'] = [$b1, [], $namePrefix . 'label inner'];
        }

        // A before label
        if (!in_array('before', $skip)) {
            $b2 = $bBase->copy();
            $b2->setLabel('before', 'prefix');
            $cases[$prefix . 'label-before'] = [$b2, [], $namePrefix . 'label before'];
        }

        // Some text after
        if (!in_array('after', $skip)) {
            $b3 = $bBase->copy();
            $b3->setLabel('after', 'suffix');
            $cases[$prefix . 'label-after'] = [$b3, [], $namePrefix . 'label after'];
        }

        // A heading
        if (!in_array('heading', $skip)) {
            $b4 = $bBase->copy();
            $b4->setLabel('heading', 'Header');
            $cases[$prefix . 'label-head'] = [$b4, [], $namePrefix . 'label heading'];
        }

        // Help
        if (!in_array('help', $skip)) {
            $b5 = $bBase->copy();
            $b5->setLabel('help', 'Helpful');
            $cases[$prefix . 'label-help'] = [$b5, [], $namePrefix . 'label help'];
        }

        // All the labels
        $b6 = $bBase->copy();
        if (!in_array('inner', $skip)) {
            $b6->setLabel('inner', 'inner');
        }
        if (!in_array('heading', $skip)) {
            $b6->setLabel('heading', 'Header');
        }
        if (!in_array('help', $skip)) {
            $b6->setLabel('help', 'Helpful');
        }
        if (!in_array('before', $skip)) {
            $b6->setLabel('before', 'prefix');
        }
        if (!in_array('after', $skip)) {
            $b6->setLabel('after', 'suffix');
        }
        $cases[$prefix . 'label-all'] = [$b6, [], $namePrefix . 'all labels'];

        return self::normalizeCases($cases);
    }

    /**
     * Button test cases
     */
	static public function html_Button() {
        $cases = [];
        $config = json_decode(
            '{"type":"button","labels":{"inner":"I am Button!"}}'
        );

        $baseButton = new ButtonElement();
        $baseButton->configure($config);
        $baseBinding = Binding::fromElement($baseButton);
        $e1 = $baseButton->copy()->setShow('purpose:success');
        $b1 = Binding::fromElement($e1);
        $cases['bda'] = [$b1, [], 'button default access'];
        $cases['bwa'] = [$b1, ['access' => 'write'], 'button write access'];

        // Make it a reset
        $b2 = Binding::fromElement($baseButton->copy()->setFunction('reset'));
        $cases['rbda'] = [$b2, [], 'reset button default access'];

        // Make it a submit
        $e3 = $baseButton->copy()->setFunction('submit');
        $b3 = Binding::fromElement($e3);
        $cases['sbda'] = [$b3, [], 'submit button default access'];

        // Set it back to button
        $b4 = Binding::fromElement($baseButton->copy()->setFunction('button'));
        $cases['bda2'] = [$b4, [], 'button default access #2'];

        // Test view access
        $cases['bva'] = [$baseBinding, ['access' => 'view'], 'button view access'];

        // Test hidden access
        $cases['bra'] = [$baseBinding, ['access' => 'hide'], 'button hidden access'];

        // Test success with smaller size
        $b5 = Binding::fromElement($e1->copy()->addShow('size:small'));
        $cases['small'] = [$b5];

        // Test submit with larger size
        $b6 = Binding::fromElement($e3->copy()->addShow('size:large'));
        $cases['large'] = [$b6];

        // How about a large outline warning?
        $b7 = Binding::fromElement(
            $baseButton->copy()->setShow('purpose:warning|size:large|fill:outline')
        );
        $cases['lg-warn-out'] = [$b7, [], 'button large warning outline'];

        // Disabled button
        $cases['disabled'] = Binding::fromElement(
            $baseButton->copy()->setEnabled(false)
        );

        // Hidden button
        $cases['hidden'] = Binding::fromElement(
            $baseButton->copy()->setDisplay(false)
        );

        return self::normalizeCases($cases);
    }

    /**
     * Iterate through labels on a button
     * @return array
     */
	static public function html_ButtonLabels() {
        $config = json_decode('{"type":"button"}');
        $eBase = new ButtonElement();
        $eBase->configure($config);
        $bBase = Binding::fromElement($eBase);
        $cases = self::addLabels($bBase, '', 'Button ');
        return self::normalizeCases($cases);
    }

    static public function html_Cell() {
        $cases = [];
        $element = new CellElement();
        $binding = Binding::fromElement($element);
        $cases['basic'] = $binding;
        return self::normalizeCases($cases);
    }

    static public function html_FieldButton() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        //
        // Modify the schema to change test/text to a button
        //
        $schema->getProperty('test/text')->getPresentation()->setType('button');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $e1 = new FieldElement();
        $e1->configure($config);
        $b1 = Binding::fromElement($e1);
        $b1->bindSchema($schema);
        $b1->setValue('Ok Bob');

        $cases['value'] = [$b1, [], 'with value'];

        $s2 = $schema->copy();
        $e2 = new FieldElement();
        $e2->configure($config);
        $b2 = Binding::fromElement($e2);
        $b2->bindSchema($s2);
        $b2->setValue('Ok Bob');
        $s2->getProperty('test/text')->getPresentation()->setType('reset');
        $cases['reset'] = [$b2];

        $s3 = $schema->copy();
        $e3 = new FieldElement();
        $e3->configure($config);
        $b3 = Binding::fromElement($e3);
        $b3->bindSchema($s3);
        $b3->setValue('Ok Bob');
        $s3->getProperty('test/text')->getPresentation()->setType('submit');
        $cases['submit'] = [$b3];

        return self::normalizeCases($cases);
    }

    static public function html_FieldCheckbox() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        //
        // Modify the schema to change test/text to a checkbox
        //
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);
        //
        // Give the binding a label
        //
        $binding->setLabel('inner', '<Stand-alone> checkbox');

        // No access specification assumes write access
        $cases['basic'] = [$binding];

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        // Set a value (e2 gets used below)
        $b2 = $binding->copy();
        $b2->setValue(3);
        $b2b = $b2->copy();
        $b2b->getDataProperty()->getPopulation()->sidecar = 'foo';
        $cases['value'] = [$b2b];
        $cases['value-view'] = [$b2b, ['access' => 'view']];
        $cases['value-hide'] = [$b2b, ['access' => 'hide']];

        // Set the default to the same as the value to render it as checked
        $b2c = $b2->copy();
        $b2c->getElement()->setDefault(3);
        $cases['checked'] = [$b2c];

        // Render inline
        $b3 = $binding->copy();
        $b3->getElement()->addShow('layout:inline');
        $cases['inline'] = [$b3];

        // Render inline with no labels
        $b4 = $b3->copy();
        $b4->getElement()->addShow('appearance:no-label');
        $cases['inline-nolabel'] = [$b4];

        // Test headings
        $cases = array_merge($cases, self::addLabels($b2));

        return self::normalizeCases($cases);
    }

    static public function html_FieldCheckboxButton() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        //
        // Modify the schema to change test/text to a checkbox
        //
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // Give the binding a label
        $binding->setLabel('inner', 'CheckButton!');

        // Make one show as a toggle
        $b1 = $binding->copy();
        $b1->getElement()->addShow('appearance:toggle');
        $cases['toggle'] = $b1;
        $cases = array_merge($cases, self::addLabels($b1, '', '', ['inner']));

        return self::normalizeCases($cases);
    }

    static public function html_FieldCheckboxButtonList() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change textWithList to a checkbox, mix up item attributes
        $schema->getProperty('test/textWithList')->getPresentation()->setType('checkbox');

        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        $list = $binding->getList(true);

        // Disable the second item
        $list[1]->setEnabled(false);

        // Set appearance on the last list item
        $list[3]->setShow('purpose:danger');
        // Make the list show as a toggle
        $b2 = $binding->copy();
        $b2->getElement()->addShow('appearance:toggle');
        $cases['toggle-list'] = $b2;

        $cases = array_merge($cases, self::addLabels($b2, 'list-', 'list-', ['inner']));

        return self::normalizeCases($cases);
    }

    static public function html_FieldCheckboxList() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        //
        // Modify the schema to change textWithList to a checkbox
        //
        $schema->getProperty('test/textWithList')->getPresentation()->setType('checkbox');

        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        $list = $binding->getList(true);

        // Disable the second item
        $list[1]->setEnabled(false);

        // No access specification assumes write access
        $cases['basic'] = [$binding];

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        // Set a value to trigger the checked option
        $b2 = $binding->copy()->setValue('textlist 4');
        $cases['single-value'] = [$b2];

        // Test hidden access
        $cases['single-value-hide'] = [$b2, ['access' => 'hide']];

        // Set a second value to trigger the checked option
        $b3 = $binding->copy()->setValue(['textlist 1', 'textlist 4']);
        $cases['dual-value'] = [$b3];

        // Test hidden access
        $cases['dual-value-view'] = [$b3, ['access' => 'view']];

        // Test hidden access
        $cases['dual-value-hide'] = [$b3, ['access' => 'hide']];

        // Render inline
        $b4 = $binding->copy();
        $b4->getElement()->addShow('layout:inline');
        $cases['inline'] = [$b4];

        // Render inline with no labels
        $b5 = $b4->copy();
        $b5->getElement()->addShow('appearance:no-label');
        $cases['inline-nolabel'] = [$b5];

        return self::normalizeCases($cases);
    }

    static public function html_FieldColor() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('color');
        $config = json_decode('{"type": "field","object": "test/text"}');

        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        $cases['default'] = $binding;

        // Set a value
        //
        $b1 = $binding->copy();
        $b1->setValue('#F0F0F0');
        $cases['value'] = $b1;

        // Same result with explicit write access
        $cases['value-write'] = [$b1, ['access' => 'write']];

        // Now with view access
        $cases['value-view'] = [$b1, ['access' => 'view']];

        //
        // Hidden access
        $cases['value-hide'] = [$b1, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldDate() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('date');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Set a value
        $b1 = $binding->copy();
        $b1->setValue('2010-10-10');
        $cases['value'] = $b1;

        // Same result with explicit write access
        //
        $cases['write'] = [$b1, ['access' => 'write']];

        // Now test validation
        $b2 = $b1->copy();
        $validation = $b2->getDataProperty()->getValidation();
        $validation->set('minValue', '1957-10-08');
        $validation->set('maxValue', 'Nov 6th 2099');
        $cases['minmax'] = $b2;

        // Now with view access
        $cases['view'] = [$b2, ['access' => 'view']];

        // Convert to hidden access
        //
        $cases['hide'] = [$b2, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldDatetimeLocal() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('datetime-local');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Set a value
        $b1 = $binding->copy();
        $b1->setValue('2010-10-10');
        $cases['value'] = $b1;

        // Same result with explicit write access
        $cases['write'] = [$b1, ['access' => 'write']];

        // Now test validation
        $b2 = $b1->copy();
        $validation = $b2->getDataProperty()->getValidation();
        $validation->set('minValue', '1957-10-08');
        $validation->set('maxValue', '2:15 pm Nov 6th 2099');
        $cases['minmax'] = $b2;

        // Now with view access
        $cases['view'] = [$b2, ['access' => 'view']];

        // Convert to hidden access
        $cases['hide'] = [$b2, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldEmail() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('email');
        $config = json_decode('{"type": "field","object": "test/text"}');

        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Now test validation
        $b1 = $binding->copy();
        $validation = $b1->getDataProperty()->getValidation();
        $validation->set('multiple', true);
        $cases['multiple'] = $b1;

        // Turn confirmation on and set some test labels
        $s2 = $schema->copy();
        $e2 = new FieldElement();
        $e2->configure($config);
        $b2 = Binding::fromElement($e2);
        $b2->bindSchema($s2);
        $s2->getProperty('test/text')->getPresentation()->setConfirm(true);
        $b2->setLabel('heading', 'Yer email');
        $b2->setLabel('confirm', 'Confirm yer email');
        $cases['confirm'] = $b2;

        // Test view access
        $b3 = $b2->copy();
        $b3->setValue('snafu@fub.ar');
        $cases['view'] = [$b3, ['access' => 'view']];

        // Hidden access
        $cases['hide'] = [$b3, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldFile() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('file');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Now test validation
        //
        $b1 = $binding->copy();
        $validation = $b1->getDataProperty()->getValidation();
        $validation->set('accept', '*.png,*.jpg');
        $validation->set('multiple', true);
        $cases['valid'] = $b1;

        // Test view access
        $cases['view'] = [$b1, ['access' => 'view']];

        // Test view with a value
        $b2 = $b1->copy();
        $b2->setValue(['file1.png', 'file2.jpg']);
        $cases['value-view'] = [$b2, ['access' => 'view']];

        // Test hidden access
        $cases['value-hide'] = [$b2, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldHidden() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Same result with view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Same result for hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        // Scalar valued
        $b2 = $binding->copy();
        $b2->setValue(3);
        $cases['scalar'] = $b2;

        // Array valued
        $b3 = $binding->copy();
        $b3->setValue([3, 4]);
        $cases['array'] = $b3;

        // Scalar with sidecar
        $b4 = $b2->copy();
        $b4->getDataProperty()->getPopulation()->sidecar = 'foo';
        $cases['sidecar'] = $b4;

        return self::normalizeCases($cases);
    }

    static public function html_FieldHiddenLabels() {
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);
        $binding->setValue('the value');

        $cases = self::addLabels($binding);

        return self::normalizeCases($cases);
    }

    static public function html_FieldMonth() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('month');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Set a value
        $b1 = $binding->copy();
        $b1->setValue('2010-10');
        $cases['value'] = $b1;

        // Same result with explicit write access
        $cases['value-write'] = [$b1, ['access' => 'write']];

        // Now test validation
        $b2 = $b1->copy();
        $validation = $b2->getDataProperty()->getValidation();
        $validation->set('minValue', '1957-10');
        $validation->set('maxValue', 'Nov 2099');
        $cases['minmax'] = $b2;

        // Now with view access
        $cases['minmax-view'] = [$b2, ['access' => 'view']];

        // Convert to hidden access
        $cases['hide'] = [$b2, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldNumber() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        //
        // Modify the schema to change test/text to a number
        //
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('number');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);
        $binding->setValue('200');

        $cases['basic'] = $binding;

        // Make the field required
        $b1 = $binding->copy();
        $validation = $b1->getDataProperty()->getValidation();
        $validation->set('required', true);
        $cases['required'] = $b1->copy();

        // Set minimum/maximum values
        $validation->set('minValue', -1000);
        $validation->set('maxValue', 999.45);
        $cases['minmax'] = $b1->copy();

        // Add a step
        $validation->set('step', 1.23);
        $cases['step'] = $b1->copy();

        // Settng a pattern should have no effect!
        $validation->set('pattern', '/[+\-]?[0-9]+/');
        $cases['pattern'] = $b1;

        // Test view access
        $cases['view'] = [$b1, ['access' => 'view']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldPassword() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a password
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('password');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test view with a value
        $b1 = $binding->copy();
        $b1->setValue('secret');
        $cases['value-view'] = [$b1, ['access' => 'view']];

        // Test hidden access
        $cases['value-hide'] = [$b1, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldRadio() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a radio
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('radio');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // Give the element a label
        $binding->setLabel('inner', '<Stand-alone> radio');
        //
        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Set a value
        $b1 = $binding->copy();
        $b1->setValue(3);
        $cases['value'] = $b1;

        // Test view access
        $cases['value-view'] = [$b1, ['access' => 'view']];

        // Test hidden access
        $cases['value-hide'] = [$b1, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldRadioLabels() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a radio
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('radio');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // Give the element some labels and a value
        $binding->setLabel('before', 'No need to fear');
        $binding->setLabel('heading', 'Very Important Choice');
        $binding->setLabel('inner', '<Stand-alone> radio');
        $binding->setLabel('after', 'See? No problem!');
        $binding->setValue(3);
        $cases['labels-value'] = $binding;

        // Test view access
        $cases['labels-value-view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['labels-value-hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldRadioList() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change textWithList to a radio
        $schema->getProperty('test/textWithList')->getPresentation()->setType('radio');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding->copy();

        // Same result with explicit write access
        $cases['write'] = [$binding->copy(), ['access' => 'write']];

        // Set a value to trigger the checked option
        $binding->setValue('textlist 3');
        $cases['value'] = $binding->copy();

        //
        // Test view access
        $cases['value-view'] = [$binding->copy(), ['access' => 'view']];

        // Test hidden access
        $cases['value-hide'] = [$binding->copy(), ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldRadioListLabels() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a radio
        $presentation = $schema->getProperty('test/textWithList')->getPresentation();
        $presentation->setType('radio');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);
        //
        // Give the element some labels and a value
        //
        $binding->setLabel('before', 'No need to fear');
        $binding->setLabel('heading', 'Very Important Choice');
        $binding->setLabel('inner', '<Stand-alone> radio');
        $binding->setLabel('after', 'See? No problem!');
        $binding->setValue('textlist 3');
        $cases['labels-value'] = $binding;

        // Test view access
        $cases['labels-value-view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['labels-value-hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldRange() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a range
        //
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('range');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);
        $binding->setValue('200');

        $cases['basic'] = $binding->copy();

        // Making the field required should have no effect
        $validation = $binding->getDataProperty()->getValidation();
        $validation->set('required', true);
        $cases['required'] = $binding->copy();

        // Set minimum/maximum values
        $validation->set('minValue', -1000);
        $validation->set('maxValue', 999.45);
        $cases['minmax'] = $binding->copy();

        // Add a step
        //
        $validation->set('step', 20);
        $cases['step'] = $binding->copy();

        // Setting a pattern should have no effect!
        $validation->set('pattern', '/[+\-]?[0-9]+/');
        $cases['pattern'] = $binding->copy();

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldSearch() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a search
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('search');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldSelect() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a select
        $presentation = $schema->getProperty('test/textWithList')->getPresentation();
        $presentation->setType('select');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding->copy();

        // Same result with explicit write access
        $cases['write'] = $cases['basic'];

        // Test view access
        $cases['view'] = [$binding->copy(), ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding->copy(), ['access' => 'hide']];

        // Now let's give it a value...
        $binding->setValue('textlist 2');
        $cases['value'] = $binding->copy();

        // Test view access
        $cases['value-view'] = [$binding->copy(), ['access' => 'view']];

        // Test hidden access
        $cases['value-hide'] = [$binding->copy(), ['access' => 'hide']];

        // Test the BS custom presentation
        $b2 = $binding->copy();
        $b2->getElement()->setShow('appearance:custom');
        $cases['value-bs4custom'] = $b2;

        // Set multiple and give it two values
        $validation = $binding->getDataProperty()->getValidation();
        $validation->set('multiple', true);
        $binding->setValue(['textlist 2', 'textlist 4']);
        $cases['multivalue'] = $binding->copy();


        // Test view access
        $cases['multivalue-view'] = [$binding->copy(), ['access' => 'view']];

        // Test hidden access
        $cases['multivalue-hide'] = [$binding->copy(), ['access' => 'hide']];

        // Set the presentation to six rows
        $presentation->setRows(6);
        $cases['sixrow'] = $binding;

        return self::normalizeCases($cases);
    }

    static public function html_FieldSelectNested() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a select
        $presentation = $schema->getProperty('test/textWithNestedList')->getPresentation();
        $presentation->setType('select');
        $config = json_decode('{"type": "field","object": "test/textWithNestedList"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding->copy();

        // Same result with explicit write access
        $cases['write'] = [$binding->copy(), ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding->copy(), ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding->copy(), ['access' => 'hide']];

        // Now let's give it a value...
        $binding->setValue('S2I1');
        $cases['value'] = $binding->copy();

        // Test the BS custom presentation
        $b2 = $binding->copy();
        $b2->getElement()->setShow('appearance:custom');
        $cases['value-bs4custom'] = $b2;

        // Test view access
        $cases['value-view'] = [$binding->copy(), ['access' => 'view']];

        // Test hidden access
        $cases['value-hide'] = [$binding->copy(), ['access' => 'hide']];

        // Set multiple and give it two values
        $validation = $binding->getDataProperty()->getValidation();
        $validation->set('multiple', true);
        $binding->setValue(['S2I1', 'Sub One Item One']);
        $cases['multivalue'] = $binding->copy();

        // Test view access
        $cases['multivalue-view'] = [$binding->copy(), ['access' => 'view']];

        // Test hidden access
        $cases['multivalue-hide'] = [$binding->copy(), ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldTel() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a tel
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('tel');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldText() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        $cases['default'] = [$binding, [], 'default access'];
        $cases['write'] = [$binding, ['access' => 'write'], 'explicit write access'];
        $cases['view'] = [$binding, ['access' => 'view'], 'explicit view access'];
        $cases['hide'] = [$binding, ['access' => 'hide'], 'explicit hidden access'];

        return self::normalizeCases($cases);
    }

    static public function html_FieldTextDataList() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access assumes write access
        $cases['basic'] = $binding;

        // Test view access: No list is required
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test read  access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    /**
     * Iterate through labels on a button
     * @return array
     */
	static public function html_FieldTextLabels() {
        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);
        $binding->setValue('the value');
        $cases = self::addLabels($binding, '', 'Text ');

        return self::normalizeCases($cases);
    }

    static public function html_FieldTextValidation() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);
        $validation = $binding->getDataProperty()->getValidation();

        // Make the field required
        $validation->set('required', true);
        $cases['required'] = $binding->copy();

        // Set a maximum length
        $validation->set('maxLength', 10);
        $cases['max'] = $binding->copy();

        // Set a minimum length
        $validation->set('minLength', 3);
        $cases['minmax'] = $binding->copy();

        // Make it match a postal code
        $validation->set('pattern', '/[a-z][0-9][a-z] ?[0-9][a-z][0-9]/');
        $cases['pattern'] = $binding->copy();

        return self::normalizeCases($cases);
    }

    static public function html_FieldTextarea() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');

        // Modify the schema to change test/text to a textarea
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('textarea');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        //
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldTime() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('time');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding->copy();

        // Set a value
        $binding->setValue('20:10');
        $cases['value'] = $binding->copy();

        // Same result with explicit write access
        //
        $cases['value-write'] = [$binding->copy(), ['access' => 'write']];

        // Now test validation
        $validation = $binding->getDataProperty()->getValidation();
        $validation->set('minValue', '19:57');
        $validation->set('maxValue', '20:19');
        $cases['minmax'] = $binding->copy();

        // Now with view access
        $cases['minmax-view'] = [$binding->copy(), ['access' => 'view']];

        // Convert to hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldUrl() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        // Modify the schema to change test/text to a search
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('url');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_FieldWeek() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../../test-data/test-schema.json');
        $presentation = $schema->getProperty('test/text')->getPresentation();
        $presentation->setType('week');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);
        $binding->bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $binding->copy();

        // Set a value
        $binding->setValue('2010-W37');
        $cases['value'] = $binding->copy();

        // Same result with explicit write access
        $cases['value-write'] = [$binding->copy(), ['access' => 'write']];

        // Now test validation
        $validation = $binding->getDataProperty()->getValidation();
        $validation->set('minValue', '1957-W30');
        $validation->set('maxValue', '2099-W42');
        $cases['minmax'] = $binding->copy();

        // Now with view access
        $cases['minmax-view'] = [$binding->copy(), ['access' => 'view']];

        // Convert to hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_Html() {
        $cases = [];

        $config = json_decode('{"type":"html","value":"<p>This is some raw html &amp;<\/p>"}');
        $element = new HtmlElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);

        // No access specification assumes write access
        $cases['basic'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_Section() {
        $cases = [];

        $element = new SectionElement();
        $binding = Binding::fromElement($element);

        // Start with an empty section
        $cases['empty'] = $binding->copy();

        // Add a label
        $binding->setLabel('heading', 'This is legendary');
        $cases['label'] = $binding->copy();

        // Same for view access
        $cases['label-view'] = [$binding->copy(), ['access' => 'view']];

        // Same for hidden access
        $cases['label-hide'] = [$binding->copy(), ['access' => 'hide']];

        return self::normalizeCases($cases);
    }

    static public function html_Static() {
        $cases = [];

        $config = json_decode('{"type":"static","value":"This is unescaped text with <stuff>!"}');
        $element = new StaticElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);

        // No access specification assumes write access
        $cases['basic'] = $binding->copy();

        // Add a heading
        $binding->setLabel('heading', 'Header');
        $cases['head'] = $binding;

        // Same result with explicit write access
        $cases['write'] = [$binding, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$binding, ['access' => 'view']];

        // Test hidden access
        $cases['hide'] = [$binding, ['access' => 'hide']];

        // Now with raw HTML
        $config = json_decode('{"type":"static","value":"This is <strong>raw html</strong>!","html":true}');
        $element = new StaticElement();
        $element->configure($config);
        $binding = Binding::fromElement($element);

        $cases['raw'] = $binding->copy();

        // Add a heading
        $binding->setLabel('heading', 'Header');
        $cases['raw-head'] = $binding;

        return self::normalizeCases($cases);
    }

    public static function normalizeCases($cases) {
        foreach ($cases as $key => &$info) {
            if (!is_array($info)) {
                $info = [$info];
            }
            if (!isset($info[1])) {
                $info[1] = [];
            }
            if (!isset($info[2])) {
                $info[2] = $key;
            }
        }
        return $cases;
    }

}
