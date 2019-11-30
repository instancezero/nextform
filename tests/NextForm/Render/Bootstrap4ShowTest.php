<?php

use Abivia\NextForm\Manager;
use Abivia\NextForm\Renderer\Attributes;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\Bootstrap4;

/**
 * Test functionality related to show settings.
 *
 * @covers \Abivia\NextForm\Renderer\Bootstrap4
 */
class FormRendererBootstrap4ShowTest extends \PHPUnit\Framework\TestCase {

    protected $testObj;
    protected $getProperty;

    protected function setUp() : void
    {
        Manager::boot();
        $this->testObj = new Bootstrap4();
        $this->getProperty = function($prop) { return $this->$prop; };
    }

	public function testCellSpacingDefault()
    {
        $getProperty = $this->getProperty->bindTo($this->testObj, $this->testObj);
        $showState = $getProperty('showState');

        // First time: make sure default exists and that it contains Attributes
        $this->assertTrue(isset($showState['form']['cellspacing']));
        $this->assertInstanceOf(
            '\Abivia\NextForm\Renderer\Attributes',
            $showState['form']['cellspacing']
        );

        // Then check for the value we want
        $classes = implode(' ', $showState['form']['cellspacing']->get('class'));
        $this->assertEquals('ml-3', $classes);
    }

	public function testCellSpacingDigit()
    {
        $getProperty = $this->getProperty->bindTo($this->testObj, $this->testObj);

        // Change the value
        $this->testObj->setShow('cellspacing:1');
        $showState = $getProperty('showState');
        $classes = implode(' ', $showState['form']['cellspacing']->get('class'));
        $this->assertEquals('ml-1', $classes);

        // Change it again just to be sure
        $this->testObj->setShow('cellspacing:4');
        $showState = $getProperty('showState');
        $classes = implode(' ', $showState['form']['cellspacing']->get('class'));
        $this->assertEquals('ml-4', $classes);

        // Make sure a value for a different renderer changes nothing.
        $this->testObj->setShow('cellspacing:xx-2');
        $showState = $getProperty('showState');
        $classes = implode(' ', $showState['form']['cellspacing']->get('class'));
        $this->assertEquals('ml-4', $classes);

        // Test out of range value
        $this->expectException('\RuntimeException');
        $this->testObj->setShow('cellspacing:9');
    }

	public function testCellSpacingResponsive()
    {
        $getProperty = $this->getProperty->bindTo($this->testObj, $this->testObj);

        // Change the value
        $this->testObj->setShow('cellspacing:sm-1:md-3:xx-lg-4:b4-xl-2');
        $showState = $getProperty('showState');
        $classes = implode(' ', $showState['form']['cellspacing']->get('class'));
        $this->assertEquals('ml-sm-1 ml-md-3 ml-xl-2', $classes);

        // Test unrecognized value
        $this->testObj->setShow('cellspacing:b4-xx-3');
        $showState = $getProperty('showState');
        $classes = implode(' ', $showState['form']['cellspacing']->get('class'));
        $this->assertEquals('ml-sm-1 ml-md-3 ml-xl-2', $classes);
    }

}
