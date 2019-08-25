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
    static protected function addLabels($eBase, $prefix = '', $namePrefix = '') {
        $cases = [];

        // No changes
        $cases[$prefix . 'label-none'] = [$eBase, [], $namePrefix . 'label none'];

        $e1 = $eBase -> copy();
        $e1 -> setLabel('inner', 'inner');
        $cases[$prefix . 'label-inner'] = [$e1, [], $namePrefix . 'label inner'];

        // A before label
        $e2 = $eBase -> copy();
        $e2 -> setLabel('before', 'prefix');
        $cases[$prefix . 'label-before'] = [$e2, [], $namePrefix . 'label before'];

        // Some text after
        $e3 = $eBase -> copy();
        $e3 -> setLabel('after', 'suffix');
        $cases[$prefix . 'label-after'] = [$e3, [], $namePrefix . 'label after'];

        // A heading
        $e4 = $eBase -> copy();
        $e4 -> setLabel('heading', 'Header');
        $cases[$prefix . 'label-head'] = [$e4, [], $namePrefix . 'label heading'];

        // Help
        $e5 = $eBase -> copy();
        $e5 -> setLabel('help', 'Helpful');
        $cases[$prefix . 'label-help'] = [$e5, [], $namePrefix . 'label help'];

        // All the labels
        $e6 = $eBase -> copy();
        $e6 -> setLabel('inner', 'inner');
        $e6 -> setLabel('heading', 'Header');
        $e6 -> setLabel('help', 'Helpful');
        $e6 -> setLabel('before', 'prefix');
        $e6 -> setLabel('after', 'suffix');
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

        $eBase = new ButtonElement();
        $eBase -> configure($config);
        $e1 = $eBase -> copy();
        $e1 -> setShow('purpose:success');
        $cases['bda'] = [$e1, [], 'button default access'];
        $cases['bwa'] = [$e1, ['access' => 'write'], 'button write access'];

        // Make it a reset
        $e2 = $eBase -> copy();
        $e2 -> set('function', 'reset');
        $cases['rbda'] = [$e2, [], 'reset button default access'];

        // Make it a submit
        $e3 = $eBase -> copy();
        $e3 -> set('function', 'submit');
        $cases['sbda'] = [$e3, [], 'submit button default access'];

        // Set it back to button
        $e4 = $eBase -> copy();
        $e4 -> set('function', 'button');
        $cases['bda2'] = [$e4, [], 'button default access #2'];

        // Test view access
        $cases['bva'] = [$eBase, ['access' => 'view'], 'button view access'];

        // Test read (less than view) access
        $cases['bra'] = [$eBase, ['access' => 'read'], 'button read access'];
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
        $expect = [];
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
        $cases['write'] = [$element, ['access' => 'write'], 'write access'];

        // Test view access
        $cases['view'] = [$element, ['access' => 'view'], 'view access'];

        // Test read access
        $cases['read'] = [$element, ['access' => 'read'], 'read acceess'];

        //
        // Set a value
        //
        $e2 = $element -> copy();
        $e2 -> setValue(3);
        $cases['value'] = [$e2];

        // Test headings
        $cases = array_merge($cases, self::addLabels($e2));

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
