<?php

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
     * Button test cases
     */
	static public function html_Button() {
        $cases = [];
        $config = json_decode(
            '{"type":"button","labels":{"inner":"I am Button!"}}'
        );

        $eBase = new ButtonElement();
        $eBase -> configure($config);
        $e1 = clone $eBase;
        $e1 -> setShow('purpose:success');
        $cases['bda'] = [$e1, [], 'button default access'];
        $cases['bwa'] = [$e1, ['access' => 'write'], 'button write access'];
        // Make it a reset
        $e2 = clone $eBase;
        $e2 -> set('function', 'reset');
        $cases['rbda'] = [$e2, [], 'reset button default access'];

        // Make it a submit
        $e3 = clone $eBase;
        $e3 -> set('function', 'submit');
        $cases['sbda'] = [$e3, [], 'submit button default access'];

        // Set it back to button
        $e4 = clone $e3;
        $e4 -> set('function', 'button');
        $cases['bda2'] = [$e4, [], 'button default access #2'];

        // Test view access
        $cases['bva'] = [$eBase, ['access' => 'view'], 'button view access'];

        // Test read (less than view) access
        $cases['bra'] = [$eBase, ['access' => 'read'], 'button read access'];
        return $cases;
    }


}
