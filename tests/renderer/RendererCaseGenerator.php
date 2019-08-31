<?php

use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Element\ButtonElement;
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
     * @return array
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

        // Test read (less than view) access
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
        $cases['value'] = [$e2];

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


        //
        // Modify the schema to change textWithList to a checkbox, style the last item
        //
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
        $e2 = $element -> copy() -> setValue('textlist 3');
        $cases['single-value'] = [$e2];

        // Test read access
        $cases['single-value-read'] = [$e2, ['access' => 'read']];

        // Set a second value to trigger the checked option
        $e3 = $element -> copy() -> setValue(['textlist 1', 'textlist 3']);
        $cases['dual-value'] = [$e3];

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

}
