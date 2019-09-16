<?php

use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\ButtonElement;
use Abivia\NextForm\Element\CellElement;
use Abivia\NextForm\Element\FieldElement;
use Abivia\NextForm\Element\HtmlElement;
use Abivia\NextForm\Element\SectionElement;
use Abivia\NextForm\Element\StaticElement;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\SimpleHtml;

/**
 * Generate standard test cases for use in testing multiple render classes.
 */
class RendererCaseGenerator {

    /**
     * Iterate through various label permutations
     * @param Element $eBase Base element used to generate cases
     * @param string $prefix Test case prefix
     * @param string $namePrefix Test label prefix
     * @return array label-none|label-inner|label-before|label-after|label-head|label-help|label-all
     */
    static protected function addLabels($eBase, $prefix = '', $namePrefix = '', $skip = []) {
        $cases = [];

        // No changes
        $cases[$prefix . 'label-none'] = [$eBase, [], $namePrefix . 'label none'];

        if (!in_array('inner', $skip)) {
            $e1 = $eBase -> copy();
            $e1 -> setLabel('inner', 'inner');
            $cases[$prefix . 'label-inner'] = [$e1, [], $namePrefix . 'label inner'];
        }

        // A before label
        if (!in_array('before', $skip)) {
            $e2 = $eBase -> copy();
            $e2 -> setLabel('before', 'prefix');
            $cases[$prefix . 'label-before'] = [$e2, [], $namePrefix . 'label before'];
        }

        // Some text after
        if (!in_array('after', $skip)) {
            $e3 = $eBase -> copy();
            $e3 -> setLabel('after', 'suffix');
            $cases[$prefix . 'label-after'] = [$e3, [], $namePrefix . 'label after'];
        }

        // A heading
        if (!in_array('heading', $skip)) {
            $e4 = $eBase -> copy();
            $e4 -> setLabel('heading', 'Header');
            $cases[$prefix . 'label-head'] = [$e4, [], $namePrefix . 'label heading'];
        }

        // Help
        if (!in_array('help', $skip)) {
            $e5 = $eBase -> copy();
            $e5 -> setLabel('help', 'Helpful');
            $cases[$prefix . 'label-help'] = [$e5, [], $namePrefix . 'label help'];
        }

        // All the labels
        $e6 = $eBase -> copy();
        if (!in_array('inner', $skip)) {
            $e6 -> setLabel('inner', 'inner');
        }
        if (!in_array('heading', $skip)) {
            $e6 -> setLabel('heading', 'Header');
        }
        if (!in_array('help', $skip)) {
            $e6 -> setLabel('help', 'Helpful');
        }
        if (!in_array('before', $skip)) {
            $e6 -> setLabel('before', 'prefix');
        }
        if (!in_array('after', $skip)) {
            $e6 -> setLabel('after', 'suffix');
        }
        $cases[$prefix . 'label-all'] = [$e6, [], $namePrefix . 'all labels'];

        return $cases;
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
        $baseButton -> configure($config);
        $e1 = $baseButton -> copy() -> setShow('purpose:success');
        $cases['bda'] = [$e1, [], 'button default access'];
        $cases['bwa'] = [$e1, ['access' => 'write'], 'button write access'];

        // Make it a reset
        $e2 = $baseButton -> copy() -> set('function', 'reset');
        $cases['rbda'] = [$e2, [], 'reset button default access'];

        // Make it a submit
        $e3 = $baseButton -> copy() -> set('function', 'submit');
        $cases['sbda'] = [$e3, [], 'submit button default access'];

        // Set it back to button
        $e4 = $baseButton -> copy() -> set('function', 'button');
        $cases['bda2'] = [$e4, [], 'button default access #2'];

        // Test view access
        $cases['bva'] = [$baseButton, ['access' => 'view'], 'button view access'];

        // Test read access
        $cases['bra'] = [$baseButton, ['access' => 'read'], 'button read access'];

        // Test success with smaller size
        $e5 = $e1 -> copy() -> addShow('size:small');
        $cases['small'] = [$e5];

        // Test submit with larger size
        $e6 = $e3 -> copy() -> addShow('size:large');
        $cases['large'] = [$e6];

        // How about a large outline warning?
        $e7 = $baseButton -> copy() -> setShow('purpose:warning|size:large|fill:outline');
        $cases['lg-warn-out'] = [$e7, [], 'button large warning outline'];

        return $cases;
    }

    /**
     * Iterate through labels on a button
     * @return array
     */
	static public function html_ButtonLabels() {
        $config = json_decode('{"type":"button"}');
        $eBase = new ButtonElement();
        $eBase -> configure($config);
        $cases = self::addLabels($eBase, '', 'Button ');
        return $cases;
    }

    static public function html_Cell() {
        $cases = [];
        $element = new CellElement();
        $cases['basic'] = $element;
        return $cases;
    }

    static public function html_FieldButton() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a button
        //
        $schema -> getProperty('test/text') -> getPresentation() -> setType('button');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $e1 = new FieldElement();
        $e1 -> configure($config);
        $e1 -> bindSchema($schema);
        $e1 -> setValue('Ok Bob');

        $cases['value'] = [$e1, [], 'with value'];

        $s2 = $schema -> copy();
        $e2 = new FieldElement();
        $e2 -> configure($config);
        $e2 -> bindSchema($s2);
        $e2 -> setValue('Ok Bob');
        $s2 -> getProperty('test/text') -> getPresentation() -> setType('reset');
        $cases['reset'] = [$e2];

        $s3 = $schema -> copy();
        $e3 = new FieldElement();
        $e3 -> configure($config);
        $e3 -> bindSchema($s3);
        $e3 -> setValue('Ok Bob');
        $s3 -> getProperty('test/text') -> getPresentation() -> setType('submit');
        $cases['submit'] = [$e3];

        return $cases;
    }

    static public function html_FieldCheckbox() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a checkbox
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // Give the element a label
        //
        $element -> setLabel('inner', '<Stand-alone> checkbox');

        // No access specification assumes write access
        $cases['basic'] = [$element];

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read']];

        // Set a value
        $e2 = $element -> copy();
        $e2 -> setValue(3);
        $e2b = $e2 -> copy();
        $e2b -> getDataProperty() -> getPopulation() -> sidecar = 'foo';
        $cases['value'] = [$e2b];
        $cases['value-view'] = [$e2b, ['access' => 'view']];
        $cases['value-read'] = [$e2b, ['access' => 'read']];

        // Render inline
        $e3 = $element -> copy();
        $e3 -> addShow('layout:inline');
        $cases['inline'] = [$e3];

        // Render inline with no labels
        $e4 = $e3 -> copy();
        $e4 -> addShow('appearance:no-label');
        $cases['inline-nolabel'] = [$e4];

        // Test headings
        $cases = array_merge($cases, self::addLabels($e2));

        return $cases;
    }

    static public function html_FieldCheckboxButton() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a checkbox
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('checkbox');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // Give the element a label
        $element -> setLabel('inner', 'CheckButton!');

        // Make one show as a toggle
        $e1 = $element -> copy();
        $e1 -> addShow('appearance:toggle');
        $cases['toggle'] = $e1;
        $cases = array_merge($cases, self::addLabels($e1, '', '', ['inner']));

        return $cases;
    }

    static public function html_FieldCheckboxButtonList() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change textWithList to a checkbox, style the last item
        $schema -> getProperty('test/textWithList') -> getPresentation() -> setType('checkbox');

        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        $list = $element -> getList(true);
        // Set appearance on the last list item
        $list[3] -> setShow('purpose:danger');
        // Make the list show as a toggle
        $e2 = $element -> copy();
        $e2 -> addShow('appearance:toggle');
        $cases['toggle-list'] = $e2;

        $cases = array_merge($cases, self::addLabels($e2, 'list-', 'list-', ['inner']));

        return $cases;
    }

    static public function html_FieldCheckboxList() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change textWithList to a checkbox
        //
        $schema -> getProperty('test/textWithList') -> getPresentation() -> setType('checkbox');

        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = [$element];

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read']];

        // Set a value to trigger the checked option
        $e2 = $element -> copy() -> setValue('textlist 4');
        $cases['single-value'] = [$e2];

        // Test read access
        $cases['single-value-read'] = [$e2, ['access' => 'read']];

        // Set a second value to trigger the checked option
        $e3 = $element -> copy() -> setValue(['textlist 1', 'textlist 4']);
        $cases['dual-value'] = [$e3];

        // Test read access
        $cases['dual-value-view'] = [$e3, ['access' => 'view']];

        // Test read access
        $cases['dual-value-read'] = [$e3, ['access' => 'read']];

        // Render inline
        $e4 = $element -> copy();
        $e4 -> addShow('layout:inline');
        $cases['inline'] = [$e4];

        // Render inline with no labels
        $e5 = $e4 -> copy();
        $e5 -> addShow('appearance:no-label');
        $cases['inline-nolabel'] = [$e5];

        return $cases;
    }

    static public function html_FieldColor() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('color');
        $config = json_decode('{"type": "field","object": "test/text"}');

        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        $cases['default'] = $element;

        // Set a value
        //
        $e1 = $element -> copy();
        $e1 -> setValue('#F0F0F0');
        $cases['value'] = $e1;

        // Same result with explicit write access
        $cases['value-write'] = [$e1, ['access' => 'write']];

        // Now with view access
        $cases['value-view'] = [$e1, ['access' => 'view']];

        //
        // Read access
        $cases['value-read'] = [$e1, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldDate() {
        $cases = [];
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('date');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Set a value
        $e1 = $element -> copy();
        $e1 -> setValue('2010-10-10');
        $cases['value'] = $e1;

        // Same result with explicit write access
        //
        $cases['write'] = [$e1, ['access' => 'write']];

        // Now test validation
        $e2 = $e1 -> copy();
        $validation = $e2 -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10-08');
        $validation -> set('maxValue', 'Nov 6th 2099');
        $cases['minmax'] = $e2;

        // Now with view access
        $cases['view'] = [$e2, ['access' => 'view']];

        // Convert to hidden for read access
        //
        $cases['read'] = [$e2, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldDatetimeLocal() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('datetime-local');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Set a value
        $e1 = $element -> copy();
        $e1 -> setValue('2010-10-10');
        $cases['value'] = $e1;

        // Same result with explicit write access
        $cases['write'] = [$e1, ['access' => 'write']];

        // Now test validation
        $e2 = $e1 -> copy();
        $validation = $e2 -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10-08');
        $validation -> set('maxValue', '2:15 pm Nov 6th 2099');
        $cases['minmax'] = $e2;

        // Now with view access
        $cases['view'] = [$e2, ['access' => 'view']];

        // Convert to hidden for read access
        $cases['read'] = [$e2, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldEmail() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('email');
        $config = json_decode('{"type": "field","object": "test/text"}');

        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Now test validation
        $e1 = $element -> copy();
        $validation = $e1 -> getDataProperty() -> getValidation();
        $validation -> set('multiple', true);
        $cases['multiple'] = $e1;

        // Turn confirmation on and set some test labels
        $s2 = $schema -> copy();
        $e2 = new FieldElement();
        $e2 -> configure($config);
        $e2 -> bindSchema($s2);
        $s2 -> getProperty('test/text') -> getPresentation() -> setConfirm(true);
        $e2 -> setLabel('heading', 'Yer email');
        $e2 -> setLabel('confirm', 'Confirm yer email');
        $cases['confirm'] = $e2;

        // Test view access
        $e3 = $e2 -> copy();
        $e3 -> setValue('snafu@fub.ar');
        $cases['view'] = [$e3, ['access' => 'view']];

        // Read access
        //
        $cases['read'] = [$e3, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldFile() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('file');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Now test validation
        //
        $e1 = $element -> copy();
        $validation = $e1 -> getDataProperty() -> getValidation();
        $validation -> set('accept', '*.png,*.jpg');
        $validation -> set('multiple', true);
        $cases['valid'] = $e1;

        // Test view access
        $cases['view'] = [$e1, ['access' => 'view']];

        // Test view with a value
        $e2 = $e1 -> copy();
        $e2 -> setValue(['file1.png', 'file2.jpg']);
        $cases['value-view'] = [$e2, ['access' => 'view']];

        // Test read access
        $cases['value-read'] = [$e2, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldHidden() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Same result with view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Same result with read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldHiddenLabels() {
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('hidden');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $element -> setValue('the value');

        $cases = self::addLabels($element);

        return $cases;
    }

    static public function html_FieldMonth() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('month');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Set a value
        $e1 = $element -> copy();
        $e1 -> setValue('2010-10');
        $cases['value'] = $e1;

        // Same result with explicit write access
        $cases['value-write'] = [$e1, ['access' => 'write']];

        // Now test validation
        $e2 = $e1 -> copy();
        $validation = $e2 -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-10');
        $validation -> set('maxValue', 'Nov 2099');
        $cases['minmax'] = $e2;

        // Now with view access
        $cases['minmax-view'] = [$e2, ['access' => 'view']];

        // Convert to hidden for read access
        $cases['read'] = [$e2, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldNumber() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        //
        // Modify the schema to change test/text to a number
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('number');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $element -> setValue('200');

        $cases['basic'] = $element;

        // Make the field required
        $e1 = $element -> copy();
        $validation = $e1 -> getDataProperty() -> getValidation();
        $validation -> set('required', true);
        $cases['required'] = $e1 -> copy();

        // Set minimum/maximum values
        $validation -> set('minValue', -1000);
        $validation -> set('maxValue', 999.45);
        $cases['minmax'] = $e1 -> copy();

        // Add a step
        $validation -> set('step', 1.23);
        $cases['step'] = $e1 -> copy();

        // Settng a pattern should have no effect!
        $validation -> set('pattern', '/[+\-]?[0-9]+/');
        $cases['pattern'] = $e1;

        // Test view access
        $cases['view'] = [$e1, ['access' => 'view']];

        return $cases;
    }

    static public function html_FieldPassword() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a password
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('password');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test view with a value
        $e1 = $element -> copy();
        $e1 -> setValue('secret');
        $cases['value-view'] = [$e1, ['access' => 'view']];

        // Test read access
        $cases['value-read'] = [$e1, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldRadio() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a radio
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // Give the element a label
        $element -> setLabel('inner', '<Stand-alone> radio');
        //
        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Set a value
        $e1 = $element -> copy();
        $e1 -> setValue(3);
        $cases['value'] = $e1;

        // Test view access
        $cases['value-view'] = [$e1, ['access' => 'view']];

        // Test read access
        $cases['value-read'] = [$e1, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldRadioLabels() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a radio
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // Give the element some labels and a value
        $element -> setLabel('before', 'No need to fear');
        $element -> setLabel('heading', 'Very Important Choice');
        $element -> setLabel('inner', '<Stand-alone> radio');
        $element -> setLabel('after', 'See? No problem!');
        $element -> setValue(3);
        $cases['labels-value'] = $element;

        // Test view access
        $cases['labels-value-view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['labels-value-read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldRadioList() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change textWithList to a radio
        $schema -> getProperty('test/textWithList') -> getPresentation() -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element -> copy();

        // Same result with explicit write access
        $cases['write'] = [$element -> copy(), ['access' => 'write']];

        // Set a value to trigger the checked option
        $element -> setValue('textlist 3');
        $cases['value'] = $element -> copy();

        //
        // Test view access
        $cases['value-view'] = [$element -> copy(), ['access' => 'view']];

        // Test read access
        $cases['value-read'] = [$element -> copy(), ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldRadioListLabels() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a radio
        $presentation = $schema -> getProperty('test/textWithList') -> getPresentation();
        $presentation -> setType('radio');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        //
        // Give the element some labels and a value
        //
        $element -> setLabel('before', 'No need to fear');
        $element -> setLabel('heading', 'Very Important Choice');
        $element -> setLabel('inner', '<Stand-alone> radio');
        $element -> setLabel('after', 'See? No problem!');
        $element -> setValue('textlist 3');
        $cases['labels-value'] = $element;

        // Test view access
        $cases['labels-value-view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['labels-value-read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldRange() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a range
        //
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('range');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $element -> setValue('200');

        $cases['basic'] = $element -> copy();

        // Making the field required should have no effect
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('required', true);
        $cases['required'] = $element -> copy();

        // Set minimum/maximum values
        $validation -> set('minValue', -1000);
        $validation -> set('maxValue', 999.45);
        $cases['minmax'] = $element -> copy();

        // Add a step
        //
        $validation -> set('step', 20);
        $cases['step'] = $element -> copy();

        // Setting a pattern should have no effect!
        $validation -> set('pattern', '/[+\-]?[0-9]+/');
        $cases['pattern'] = $element -> copy();

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        return $cases;
    }

    static public function html_FieldSearch() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a search
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('search');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldSelect() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a select
        $presentation = $schema -> getProperty('test/textWithList') -> getPresentation();
        $presentation -> setType('select');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element -> copy();

        // Same result with explicit write access
        $cases['write'] = $cases['basic'];

        // Test view access
        $cases['view'] = [$element -> copy(), ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element -> copy(), ['access' => 'read']];

        // Now let's give it a value...
        $element -> setValue('textlist 2');
        $cases['value'] = $element -> copy();

        // Test view access
        $cases['value-view'] = [$element -> copy(), ['access' => 'view']];

        // Test read access
        $cases['value-read'] = [$element -> copy(), ['access' => 'read']];

        // Test the BS custom presentation
        $cases['value-bs4custom'] = $element -> copy() -> setShow('appearance:custom');

        // Set multiple and give it two values
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('multiple', true);
        $element -> setValue(['textlist 2', 'textlist 4']);
        $cases['multivalue'] = $element -> copy();


        // Test view access
        $cases['multivalue-view'] = [$element -> copy(), ['access' => 'view']];

        // Test read access
        $cases['multivalue-read'] = [$element -> copy(), ['access' => 'read']];

        // Set the presentation to six rows
        $presentation -> setRows(6);
        $cases['sixrow'] = $element;

        return $cases;
    }

    static public function html_FieldSelectNested() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a select
        $presentation = $schema -> getProperty('test/textWithNestedList') -> getPresentation();
        $presentation -> setType('select');
        $config = json_decode('{"type": "field","object": "test/textWithNestedList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element -> copy();

        // Same result with explicit write access
        $cases['write'] = [$element -> copy(), ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element -> copy(), ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element -> copy(), ['access' => 'read']];

        // Now let's give it a value...
        $element -> setValue('S2I1');
        $cases['value'] = $element -> copy();

        // Test the BS custom presentation
        $cases['value-bs4custom'] = $element -> copy() -> setShow('appearance:custom');

        // Test view access
        $cases['value-view'] = [$element -> copy(), ['access' => 'view']];

        // Test read access
        $cases['value-read'] = [$element -> copy(), ['access' => 'read']];

        // Set multiple and give it two values
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('multiple', true);
        $element -> setValue(['S2I1', 'Sub One Item One']);
        $cases['multivalue'] = $element -> copy();

        // Test view access
        $cases['multivalue-view'] = [$element -> copy(), ['access' => 'view']];

        // Test read access
        $cases['multivalue-read'] = [$element -> copy(), ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldTel() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a tel
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('tel');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldText() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        $cases['default'] = [$element, [], 'default access'];
        $cases['write'] = [$element, ['access' => 'write'], 'explicit write access'];
        $cases['view'] = [$element, ['access' => 'view'], 'explicit view access'];
        $cases['read'] = [$element, ['access' => 'read'], 'explicit read access'];

        return $cases;
    }

    static public function html_FieldTextDataList() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/textWithList"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access assumes write access
        $cases['basic'] = $element;

        // Test view access: No list is required
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read  access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    /**
     * Iterate through labels on a button
     * @return array
     */
	static public function html_FieldTextLabels() {
        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $element -> setValue('the value');
        $cases = self::addLabels($element, '', 'Text ');

        return $cases;
    }

    static public function html_FieldTextValidation() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);
        $validation = $element -> getDataProperty() -> getValidation();

        // Make the field required
        $validation -> set('required', true);
        $cases['required'] = $element -> copy();

        // Set a maximum length
        $validation -> set('maxLength', 10);
        $cases['max'] = $element -> copy();

        // Set a minimum length
        $validation -> set('minLength', 3);
        $cases['minmax'] = $element -> copy();

        // Make it match a postal code
        $validation -> set('pattern', '/[a-z][0-9][a-z] ?[0-9][a-z][0-9]/');
        $cases['pattern'] = $element -> copy();

        return $cases;
    }

    static public function html_FieldTextarea() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');

        // Modify the schema to change test/text to a textarea
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('textarea');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        //
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldTime() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('time');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element -> copy();

        // Set a value
        $element -> setValue('20:10');
        $cases['value'] = $element -> copy();

        // Same result with explicit write access
        //
        $cases['value-write'] = [$element -> copy(), ['access' => 'write']];

        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '19:57');
        $validation -> set('maxValue', '20:19');
        $cases['minmax'] = $element -> copy();

        // Now with view access
        $cases['minmax-view'] = [$element -> copy(), ['access' => 'view']];

        // Convert to hidden for read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldUrl() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        // Modify the schema to change test/text to a search
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('url');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_FieldWeek() {
        $cases = [];

        $schema = Schema::fromFile(__DIR__ . '/../test-schema.json');
        $presentation = $schema -> getProperty('test/text') -> getPresentation();
        $presentation -> setType('week');
        $config = json_decode('{"type": "field","object": "test/text"}');
        $element = new FieldElement();
        $element -> configure($config);
        $element -> bindSchema($schema);

        // No access specification assumes write access
        $cases['basic'] = $element -> copy();

        // Set a value
        $element -> setValue('2010-W37');
        $cases['value'] = $element -> copy();

        // Same result with explicit write access
        $cases['value-write'] = [$element -> copy(), ['access' => 'write']];

        // Now test validation
        $validation = $element -> getDataProperty() -> getValidation();
        $validation -> set('minValue', '1957-W30');
        $validation -> set('maxValue', '2099-W42');
        $cases['minmax'] = $element -> copy();

        // Now with view access
        $cases['minmax-view'] = [$element -> copy(), ['access' => 'view']];

        // Convert to hidden for read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_Html() {
        $cases = [];

        $config = json_decode('{"type":"html","value":"<p>This is some escaped html &amp;<\/p>"}');
        $element = new HtmlElement();
        $element -> configure($config);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

    static public function html_Section() {
        $cases = [];

        $element = new SectionElement();

        // Start with an empty section
        $cases['empty'] = $element -> copy();

        // Add a label
        $element -> setLabel('heading', 'This is legendary');
        $cases['label'] = $element -> copy();

        // Same for view access
        $cases['label-view'] = [$element -> copy(), ['access' => 'view']];

        // Same for read access
        $cases['label-read'] = [$element -> copy(), ['access' => 'read']];

        return $cases;
    }

    static public function html_Static() {
        $cases = [];

        $config = json_decode('{"type":"static","value":"This is unescaped text with <stuff>!"}');
        $element = new StaticElement();
        $element -> configure($config);

        // No access specification assumes write access
        $cases['basic'] = $element;

        // Same result with explicit write access
        $cases['write'] = [$element, ['access' => 'write']];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view']];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read']];

        return $cases;
    }

}
