<?php

use Abivia\NextForm;

class NextFormTest extends \PHPUnit\Framework\TestCase {

    public function testFormInstantiation() {
        $obj = new NextForm();
		$this->assertInstanceOf('\Abivia\NextForm', $obj);
    }

	public function testNextFormConfiguration() {
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/form.json'));
        $this->assertTrue(false != $config, 'JSON error!');
        $obj = new NextForm();
        $obj->configure($config, ['strict' => true]);
        $dump = print_r($obj, true);
        $dump = str_replace(" \n", "\n", $dump);
        file_put_contents(dirname(__FILE__) . '/form-dump-actual.txt', $dump);
        $this->assertEquals(sha1_file(dirname(__FILE__) . '/form-dump-expect.txt'), sha1($dump));
        $this->assertTrue(true);
	}

}
