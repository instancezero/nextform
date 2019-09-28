<?php

use Abivia\NextForm\Renderer\Block;

/**
 * @covers \Abivia\NextForm\Renderer\Block
 */
class FormRendererBlockTest extends \PHPUnit\Framework\TestCase {

	public function testFormRendererBlockInstantiation() {
        $obj = new Block();
		$this -> assertInstanceOf('\Abivia\NextForm\Renderer\Block', $obj);
	}

	public function testFormRendererBlockClose() {
        $obj = new Block();
        $obj -> body = 'body';
        $obj -> post = 'post';
        $obj -> close();
		$this -> assertEquals('bodypost', $obj -> body);
		$this -> assertEquals('', $obj -> post);
    }

	public function testFormRendererBlockMerge() {
        $main = new Block();
        $main -> body = 'mainbody';
        $main -> post = 'mainpost';
        $main -> scriptFiles = $mainScripts = [
            'main' => 'somescript',
            'redundant' => 'redundant',
        ];
        $obj2 = new Block();
        $obj2 -> body = 'obj2body';
        $obj2 -> post = 'obj2post';
        $obj2 -> scriptFiles = $obj2Scripts = [
            'obj2' => 'anotherscript',
            'redundant' => 'redundant',
        ];
        $main -> merge ($obj2);
        $this -> assertEquals('mainbodyobj2body', $main -> body);
        $this -> assertEquals('obj2postmainpost', $main -> post);
        $this -> assertEquals(array_merge($mainScripts, $obj2Scripts), $main -> scriptFiles);
    }

}
