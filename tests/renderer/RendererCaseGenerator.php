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
        $cases[$prefix . 'label-none'] = [$eBase, [], $namePrefix . 'none'];

        $e1 = $eBase -> copy();
        $e1 -> setLabel('inner', 'inner');
        $cases[$prefix . 'label-inner'] = [$e1, [], $namePrefix . 'inner'];

        // A before label
        $e2 = $eBase -> copy();
        $e2 -> setLabel('before', 'prefix');
        $cases[$prefix . 'label-before'] = [$e2, [], $namePrefix . 'before'];

        // Some text after
        $e3 = $eBase -> copy();
        $e3 -> setLabel('after', 'suffix');
        $cases[$prefix . 'label-after'] = [$e3, [], $namePrefix . 'after'];

        // A heading
        $e4 = $eBase -> copy();
        $e4 -> setLabel('heading', 'Header');
        $cases[$prefix . 'label-head'] = [$e4, [], $namePrefix . 'heading'];

        // Help
        $e5 = $eBase -> copy();
        $e5 -> setLabel('help', 'Helpful');
        $cases[$prefix . 'label-help'] = [$e5, [], $namePrefix . 'help'];

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
