<?php

use Abivia\NextForm\Manager;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\SimpleHtml;

include_once __DIR__ . '/SimpleHtmlRenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\SimpleHtml
 */
class FormRenderSimpleHtmlTest extends SimpleHtmlRenderFrame {

    protected function setUp() : void {
        Manager::boot();
        $this->testObj = new SimpleHtml();
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
         self::$defaultFormGroupClass = '';
   }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new SimpleHtml());
    }

	public function testInstantiation() {
		$this->assertInstanceOf('\Abivia\NextForm\Render\SimpleHtml', $this->testObj);
	}

	public function testStart() {
        $attrs = new Attributes();
        $attrs->set('id', 'form_1');
        $attrs->set('name', 'form_1');
        $data = $this->testObj->start(['attributes' => $attrs, 'token' => '']);
        $this->assertEquals("<form id=\"form_1\" name=\"form_1\" method=\"post\">\n", $data->body);
        $this->assertEquals("</form>\n", $data->post);

        $data = $this->testObj->start(['attributes' => $attrs, 'token' => 'foo']);
        $this->assertEquals(
            "<form id=\"form_1\" name=\"form_1\" method=\"post\">\n"
            . '<input id="nf_token" name="nf_token" type="hidden" value="foo">' . "\n",
            $data->body
        );

        $data = $this->testObj->start(['attributes' => $attrs, 'token' => 'foo', 'tokenName' => 'george']);
        $this->assertEquals(
            "<form id=\"form_1\" name=\"form_1\" method=\"post\">\n"
            . '<input id="george" name="george" type="hidden" value="foo">' . "\n",
            $data->body
        );

        $data = $this->testObj->start(['attributes' => $attrs, 'method' => 'put', 'token' => '']);
        $this->assertEquals("<form id=\"form_1\" name=\"form_1\" method=\"put\">\n", $data->body);

        $data = $this->testObj->start(
            ['action' => 'https://localhost/some file.php', 'attributes' => $attrs, 'token' => '']
        );
        $this->assertEquals(
            "<form id=\"form_1\" name=\"form_1\" action=\"https://localhost/some file.php\" method=\"post\">\n",
            $data->body
        );

        $attrs->set('name', 'bad<name');
        $data = $this->testObj->start(
            ['attributes' => $attrs, 'token' => '']
        );
        $this->assertEquals("<form id=\"form_1\" name=\"bad&lt;name\" action=\"https://localhost/some file.php\" method=\"post\">\n", $data->body);

    }

    /**
     * @doesNotPerformAssertions
     */
	public function testSetOptions() {

        $this->testObj->setOptions();
    }

   /**
    * Check field as a datetime-local element
    */
	public function testFieldDatetimeLocal() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldDatetimeLocal();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="datetime-local"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="datetime-local"'
                . ' value="2010-10-10"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="datetime-local" value="2010-10-10"'
                . ' min="1957-10-08T00:00" max="2099-11-06T14:15"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="datetime-local" value="2010-10-10"'
                . ' readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Convert to hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-10-10"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

	public function testFieldEmail() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldEmail();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="email"/>' . "\n"
            )
            . '<br/>' . "\n"
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="email" multiple/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Yer email</label>' . "\n"
                . '<input id="field_1" name="field_1" type="email"/>' . "\n"
                )
            . '<br/>' . "\n"
            . $this->formGroup(
                '<label for="field_1_confirmation">Confirm yer email</label>' . "\n"
                . '<input id="field_1_confirmation" name="field_1_confirmation"'
                . ' type="email"/>' . "\n",
                ['id' => 'field_1_confirmation']
            )
            . '<br/>' . "\n"
        );

        // Test view access
        //
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Yer email</label>' . "\n"
                . '<input id="field_1" name="field_1" type="email"'
                . ' value="snafu@fub.ar" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="snafu@fub.ar"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

	public function testFieldFile() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldFile();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="file"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now test validation
        $expect['valid'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="file"'
                . ' accept="*.png,*.jpg" multiple/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1[]" type="text"'
                . ' value="file1.png,file2.jpg" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        //
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="file1.png"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="file2.jpg"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

   /**
    * Check field as a hidden element
    */
	public function testFieldHidden() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldHidden();

        $expect = [];

        // No access specification assumes write access
        $expect['basic'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Same result with view access
        $expect['view'] = $expect['basic'];

        // Same result with hidden access
        $expect['hide'] = $expect['basic'];

        // Scalar valued element
        $expect['scalar'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        // Array valued element
        $expect['array'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="3"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="4"/>' . "\n"
        );

        // Scalar element with sidecar
        $expect['sidecar'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"'
            . ' data-nf-sidecar="&quot;foo&quot;"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Test a hidden field with label options
     */
	public function testFieldHiddenLabels() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldHiddenLabels();

        $expect = [];

        $expect['label-none'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"'
            . ' value="the value"/>' . "\n"
        );

        $expect['label-inner'] = $expect['label-none'];
        $expect['label-before'] = $expect['label-none'];
        $expect['label-after'] = $expect['label-none'];
        $expect['label-head'] = $expect['label-none'];
        $expect['label-help'] = $expect['label-none'];
        $expect['label-all'] = $expect['label-none'];

        $this->runCases($cases, $expect);
    }

   /**
    * Check field as a month element
    */
	public function testFieldMonth() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldMonth();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="month" value="2010-10"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="month" value="2010-10"'
                . ' min="1957-10" max="2099-11"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="month" value="2010-10"'
                . ' readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Read access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-10"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a number
     */
	public function testFieldNumber() {
        $this->logMethod(__METHOD__);
        $tail = "<br/>\n";
        $cases = RenderCaseGenerator::html_FieldNumber();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="number" value="200"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200"'
                . ' required data-nf-req="1"/>' . "\n"
            )
            . $tail
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200"'
                . ' min="-1000" max="999.45" required data-nf-req="1"/>' . "\n"
            )
            . $tail
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200"'
                . ' min="-1000" max="999.45" required step="1.23" data-nf-req="1"/>' . "\n"
            )
            . $tail
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="number"'
                . ' value="200" readonly/>' . "\n"
            )
            . $tail
        );

        $this->runCases($cases, $expect);
    }

	public function testFieldPassword() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldPassword();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="password"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="password" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="password" value="secret" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="secret"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Test code generation for a radio element
     */
	public function testFieldRadio() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldRadio();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="radio" value="3" readonly/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Test code generation for single radio element with labels
     */
	public function testFieldRadioLabels() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldRadioLabels();

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio" value="3"/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>'
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<span>No need to fear</span>'
                . '<input id="field_1" name="field_1" type="radio" value="3" readonly/>' . "\n"
                . '<label for="field_1">&lt;Stand-alone&gt; radio</label>' . "\n"
                . '<span>See? No problem!</span>'
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="3"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Test code generation for a radio element with a list
     */
	public function testFieldRadioList() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldRadioList();

        $expect = [];
        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" data-nf-name="tl3"/>'
                . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"'
                . ' readonly/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" readonly'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked readonly'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Test code generation for a radio element with a list and labels
     */
	public function testFieldRadioListLabels() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldRadioListLabels();

        $expect = [];

        $expect['labels-value'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" data-nf-group="[&quot;grpX&quot;]"/>'
                . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                '<div>Very Important Choice</div>' . "\n"
                . '<div>No need to fear</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt0" name="field_1" type="radio" value="textlist 1"'
                . ' readonly/>' . "\n"
                . '<label for="field_1_opt0">textlist 1</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt1" name="field_1" type="radio"'
                . ' value="textlist 2" readonly'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<label for="field_1_opt1">textlist 2</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt2" name="field_1" type="radio"'
                . ' value="textlist 3" checked readonly'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<label for="field_1_opt2">textlist 3</label>' . "\n"
                . '</div>' . "\n"
                . '<div>' . "\n"
                . '<input id="field_1_opt3" name="field_1" type="radio" value="textlist 4"'
                . ' readonly data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '<label for="field_1_opt3">textlist 4</label>' . "\n"
                . '</div>' . "\n"
                . '<div>See? No problem!</div>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden" value="textlist 3"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a range
     */
	public function testFieldRange() {
        $this->logMethod(__METHOD__);
        $tail = "<br/>\n";
        $cases = RenderCaseGenerator::html_FieldRange();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="range" value="200"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Making the field required should have no effect
        $expect['required'] = $expect['basic'];

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="range"'
                . ' value="200"'
                . ' min="-1000" max="999.45"/>' . "\n"
            )
            . $tail
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="range"'
                . ' value="200"'
                . ' min="-1000" max="999.45" step="20"/>' . "\n"
            )
            . $tail
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];


        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' value="200" readonly/>' . "\n"
            )
            . $tail
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a search
     */
	public function testFieldSearch() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldSearch();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="search"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="search" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a simple select
     */
	public function testFieldSelect() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldSelect();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result for BS4 custom
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value="textlist 2"/>' . "\n"
                . '<span>textlist 2</span>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="textlist 2"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1[]" multiple>' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>'
                . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" selected data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1_opt0" name="field_1[]" type="hidden"'
                . ' value="textlist 2"/>' . "\n"
                . '<span>textlist 2</span><br/>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="hidden" value="textlist 4"/>' . "\n"
                . '<span>textlist 4</span><br/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['multivalue-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="textlist 2"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden" value="textlist 4"/>' . "\n"
        );

        // Set the presentation to six rows
        $expect['sixrow'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1[]" multiple size="6">' . "\n"
                . '<option value="textlist 1">textlist 1</option>' . "\n"
                . '<option value="textlist 2" selected'
                . ' data-nf-group="[&quot;grpX&quot;]">textlist 2</option>' . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3">textlist 3</option>'
                . "\n"
                . '<option value="textlist 4" selected data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a nested select
     */
	public function testFieldSelectNested() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldSelectNested();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1">' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One">Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" selected data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // No change for the BS custom presentation
        $expect['value-bs4custom'] = $expect['value'];

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
                . '<span>Sub Two Item One</span>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                '<select id="field_1" name="field_1[]" multiple>' . "\n"
                . '<option value="General">General</option>' . "\n"
                . '<optgroup label="Subgroup One" data-nf-sidecar="&quot;subgroup 1 sidecar&quot;">' . "\n"
                . '<option value="Sub One Item One" selected>Sub One Item One</option>' . "\n"
                . '<option value="Sub One Item Two">Sub One Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '<optgroup label="Subgroup Two">' . "\n"
                . '<option value="S2I1" selected data-nf-sidecar="&quot;s2i1 side&quot;">Sub Two Item One</option>' . "\n"
                . '<option value="S2I2" data-nf-sidecar="&quot;s2i2 side&quot;">Sub Two Item Two</option>' . "\n"
                . '</optgroup>' . "\n"
                . '</select>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1_opt0" name="field_1[]" type="hidden" value="Sub One Item One"/>' . "\n"
                . '<span>Sub One Item One</span><br/>' . "\n"
                . '<input id="field_1_opt1" name="field_1[]" type="hidden" value="S2I1"/>' . "\n"
                . '<span>Sub Two Item One</span><br/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['multivalue-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden" value="S2I1"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden"'
            . ' value="Sub One Item One"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a tel
     */
	public function testFieldTel() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTel();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="tel"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="tel" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

	public function testFieldText() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldText();

        $expect = [];

        // No access specification assumes write access
        $expect['default'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Text field with a data list
     */
	public function testFieldTextDataList() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTextDataList();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' list="field_1_list"/>' . "\n"
                . '<datalist id="field_1_list">' . "\n"
                . '<option value="textlist 1"/>' . "\n"
                . '<option value="textlist 2"'
                . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                . '<option value="textlist 3"'
                . ' data-nf-name="tl3"/>' . "\n"
                . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                . '</datalist>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Test a text field with label options
     */
	public function testFieldTextLabels() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTextLabels();
        $expect = [];
        $tail = "<br/>\n";

        // no labels
        $expect['label-none'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . $tail
        );

        // before
        $expect['label-before'] = Block::fromString(
            $this->formGroup(
                '<span>prefix</span>'
                . '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . $tail
        );

        // After
        $expect['label-after'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>'
                . '<span>suffix</span>' . "\n"
            )
            . $tail
        );

        // Heading
        $expect['label-head'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Header</label>' . "\n"
                . '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"/>' . "\n"
            )
            . $tail
        );

        // Help
        $expect['label-help'] = $expect['label-none'];

        // Inner
        $expect['label-inner'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' value="the value"'
                . ' placeholder="inner"/>' . "\n"
            )
            . $tail
        );

        // All
        $expect['label-all'] = Block::fromString(
            $this->formGroup(
                '<label for="field_1">Header</label>' . "\n"
                . '<span>prefix</span>'
                . '<input id="field_1" name="field_1" type="text"'
                . ' value="the value" placeholder="inner"/>'
                . '<span>suffix</span>' . "\n"
            )
            . $tail
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Test various validation options
     */
	public function testFieldTextValidation() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTextValidation();
        $expect = [];
        $tail = "<br/>\n";
        $expect['required'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="text"'
                . ' required data-nf-req="1"/>' . "\n"
            )
            . $tail
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' maxlength="10" required data-nf-req="1"/>' . "\n"
            )
            . $tail
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' maxlength="10" minlength="3" required data-nf-req="1"/>' . "\n"
            )
            . $tail
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="text"'
                . ' maxlength="10" minlength="3"'
                . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required data-nf-req="1"/>' . "\n"
            )
            . $tail
        );

        $this->runCases($cases, $expect);
    }

	public function testFieldTextarea() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTextarea();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<textarea id="field_1" name="field_1"></textarea>' . "\n"
            )

        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<textarea id="field_1" name="field_1" readonly></textarea>' . "\n"
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

   /**
    * Check field as a time element
    */
	public function testFieldTime() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTime();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="time"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="time" value="20:10"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="time" value="20:10"'
                . ' min="19:57" max="20:19"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="time" value="20:10"'
                . ' readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Convert to hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="20:10"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a url
     */
	public function testFieldUrl() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldUrl();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="url"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="url" readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

   /**
    * Check field as a week element
    */
	public function testFieldWeek() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldWeek();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="week"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1"'
                . ' type="week" value="2010-W37"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="week" value="2010-W37"'
                . ' min="1957-W30" max="2099-W42"/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                '<input id="field_1" name="field_1" type="week" value="2010-W37"'
                . ' readonly/>' . "\n"
            )
            . '<br/>' . "\n"
        );

        // Convert to hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-W37"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

}
