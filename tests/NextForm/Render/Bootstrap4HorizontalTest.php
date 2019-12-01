<?php

use Abivia\NextForm\Manager;
//use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Element\CellElement;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;
use Abivia\NextForm\Render\Bootstrap4;

include_once __DIR__ . '/Bootstrap4RenderFrame.php';

/**
 * @covers \Abivia\NextForm\Render\Bootstrap4
 */
class FormRenderBootstrap4HorizontalTest extends Bootstrap4RenderFrame
{
    use HtmlTestLogger;
    use RenderCaseRunner;

    protected $testObj;

    protected function setUp() : void
    {
        Manager::boot();
        $this->testObj = new Bootstrap4();
        $this->testObj->setShow('layout:horizontal:2:10');
    }

    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
        self::$defaultFormGroupClass = 'form-group row';
    }

    public static function tearDownAfterClass() : void
    {
        self::generatePage(__FILE__, new Bootstrap4());
    }

	public function testInstantiation()
    {
		$this->assertInstanceOf('\Abivia\NextForm\Render\Bootstrap4', $this->testObj);
	}

	public function testCell() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_Cell();

        $expect['basic'] = Block::fromString(
            '<div class="form-row col-sm-10">' . "\n",
            '</div>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

	public function testCellContext() {
        $this->logMethod(__METHOD__);
        $element = new CellElement();
        $binding = Binding::fromElement($element);
        $this->assertFalse($this->testObj->queryContext('inCell'));
        $this->testObj->render($binding);
        $this->assertTrue($this->testObj->queryContext('inCell'));
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="datetime-local"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="datetime-local"'
                    . ' class="form-control" value="2010-10-10"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="datetime-local"'
                    . ' class="form-control" value="2010-10-10"'
                    . ' min="1957-10-08T00:00" max="2099-11-06T14:15"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="datetime-local"'
                    . ' class="form-control" value="2010-10-10"'
                    . ' readonly/>' . "\n"
                )
            )
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="email" class="form-control"/>'
                    . "\n"
                )
            )
        );
        // Now test validation
        $expect['multiple'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1[]" type="email"'
                    . ' class="form-control" multiple/>' . "\n"
                )
            )
        );

        // Turn confirmation on and set some test labels
        $expect['confirm'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Yer email', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="email" class="form-control"/>'
                    . "\n"
                )
            )
            . $this->formGroup(
                $this->column1h('Confirm yer email', 'label', 'field_1_confirmation')
                . $this->column2h(
                    '<input id="field_1_confirmation" name="field_1_confirmation"'
                    . ' type="email" class="form-control"/>'
                   . "\n"
                ),
                ['id' => 'field_1_confirmation']
            )
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Yer email', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="email" class="form-control"'
                    . ' value="snafu@fub.ar" readonly/>'
                    . "\n"
                )
            )
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="file"'
                    . ' class="form-control-file"/>' . "\n"
                )
            )
        );

        // Now test validation
        $expect['valid'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1[]" type="file"'
                    . ' class="form-control-file"'
                    . ' accept="*.png,*.jpg" multiple/>' . "\n"
                )
            )
        );

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1[]" type="text"'
                    . ' class="form-control-file" readonly/>' . "\n"
                )
            )
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1[]" type="text"'
                    . ' class="form-control-file"'
                    . ' value="file1.png,file2.jpg" readonly/>' . "\n"
                )
            )
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

        // Same result with read access
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
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"/>' . "\n"
                )
            )
        );

        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"'
                    . ' value="2010-10"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"'
                    . ' value="2010-10" min="1957-10" max="2099-11"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="month" class="form-control"'
                    . ' value="2010-10"'
                    . ' readonly/>' . "\n"
                )
            )
        );

        // Hidden access
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
        $cases = RenderCaseGenerator::html_FieldNumber();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"/>' . "\n"
                )
            )
        );

        // Make the field required
        $expect['required'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"'
                    . ' required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"'
                    . ' min="-1000" max="999.45" required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200"'
                    . ' min="-1000" max="999.45" required step="1.23" data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="number"'
                    . ' class="form-control" value="200" readonly/>' . "\n"
                )
            )
        );

        $this->runCases($cases, $expect);
    }

	public function testFieldPassword() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldPassword();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
        );

        // Test view with a value
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="password"'
                    . ' class="form-control" value="secret" readonly/>' . "\n"
                )
            )
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
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n"
                    )
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n"
                    )
                )
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'div')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3" readonly/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n"
                    )
                )
            )
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
                $this->column1h('Very Important Choice', 'div', '', 'pt-0')
                . $this->column2h(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3"/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n"
                    )
                    . '<span>See? No problem!</span>' . "\n"
                )
            )
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('Very Important Choice', 'div')
                . $this->column2h(
                    '<span class="mr-1">No need to fear</span>'
                    . $this->formCheck(
                        '<input id="field_1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="3" readonly/>' . "\n"
                        . '<label for="field_1" class="form-check-label">'
                        . '&lt;Stand-alone&gt; radio</label>' . "\n"
                    )
                    . '<span>See? No problem!</span>' . "\n"
                )
            )
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
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Set a value to trigger the checked option
        $expect['value'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend', '', 'pt-0')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3" checked'
                        . ' data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4"'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('', 'legend')
                . $this->column2h(
                    $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1" readonly/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' readonly data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' checked readonly data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4" readonly'
                        . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">'
                        . 'textlist 4</label>' . "\n"
                    )
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden"'
            . ' value="textlist 3"/>' . "\n"
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
                '<div class="row">' . "\n"
                . $this->column1h('Very Important Choice', 'legend', '', 'pt-0')
                . $this->column2h(
                    '<div>No need to fear</div>'
                    . $this->formCheck(
                        '<input id="field_1_opt0" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 1"/>' . "\n"
                        . '<label for="field_1_opt0" class="form-check-label">'
                        . 'textlist 1</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt1" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 2"'
                        . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                        . '<label for="field_1_opt1" class="form-check-label">'
                        . 'textlist 2</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt2" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 3"'
                        . ' checked data-nf-name="tl3"/>' . "\n"
                        . '<label for="field_1_opt2" class="form-check-label">'
                        . 'textlist 3</label>' . "\n"
                    )
                    . $this->formCheck(
                        '<input id="field_1_opt3" name="field_1" type="radio"'
                        . ' class="form-check-input" value="textlist 4" data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                        . '<label for="field_1_opt3" class="form-check-label">textlist 4</label>' . "\n"
                    )
                    . '<div>See? No problem!</div>' . "\n"
                )
                . '</div>'. "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Test view access
        $expect['labels-value-view'] = Block::fromString(
            $this->formGroup(
                '<div class="row">' . "\n"
                . $this->column1h('Very Important Choice', 'legend')
                . $this->column2h(
                    '<div>No need to fear</div>'
                        . $this->formCheck(
                            '<input id="field_1_opt0" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 1"'
                            . ' readonly/>' . "\n"
                            . '<label for="field_1_opt0" class="form-check-label">'
                            . 'textlist 1</label>' . "\n"
                        )
                        . $this->formCheck(
                            '<input id="field_1_opt1" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 2"'
                            . ' readonly'
                            . ' data-nf-group="[&quot;grpX&quot;]"/>' . "\n"
                            . '<label for="field_1_opt1" class="form-check-label">'
                            . 'textlist 2</label>' . "\n"
                        )
                        . $this->formCheck(
                            '<input id="field_1_opt2" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 3"'
                            . ' checked readonly data-nf-name="tl3"/>' . "\n"
                            . '<label for="field_1_opt2" class="form-check-label">'
                            . 'textlist 3</label>' . "\n"
                        )
                        . $this->formCheck(
                            '<input id="field_1_opt3" name="field_1" type="radio"'
                            . ' class="form-check-input" value="textlist 4" readonly'
                            . ' data-nf-sidecar="[1,2,3,4]"/>' . "\n"
                            . '<label for="field_1_opt3" class="form-check-label">textlist 4</label>' . "\n"
                        )
                    . '<div>See? No problem!</div>' . "\n"
                )
                . '</div>' . "\n",
                ['class' => 'form-group', 'element' => 'fieldset']
            )
        );

        // Test hidden access
        $expect['labels-value-hide'] = Block::fromString(
            '<input id="field_1_opt2" name="field_1[]" type="hidden"'
            . ' value="textlist 3"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

    /**
     * Check a field as a range
     */
	public function testFieldRange() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldRange();

        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="range"'
                    . ' class="form-control" value="200"/>' . "\n"
                )
            )
        );

        // Making the field required should have no effect
        $expect['required'] = $expect['basic'];

        // Set minimum/maximum values
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="range"'
                    . ' class="form-control" value="200"'
                    . ' min="-1000" max="999.45"/>' . "\n"
                )
            )
        );

        // Add a step
        $expect['step'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="range"'
                    . ' class="form-control" value="200"'
                    . ' min="-1000" max="999.45" step="20"/>' . "\n"
                )
            )
        );

        // Settng a pattern should have no effect!
        $expect['pattern'] = $expect['step'];


        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="200" readonly/>' . "\n"
                )
            )
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
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="search"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="search"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
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
                $this->column1h('')
                . $this->column2h(
                    '<select id="field_1" name="field_1" class="form-control">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" data-nf-group="[&quot;grpX&quot;]">'
                    . 'textlist 2</option>' . "\n"
                    . '<option value="textlist 3" data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">'
                    . 'textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', '')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<select id="field_1" name="field_1" class="form-control">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected'
                    . ' data-nf-group="[&quot;grpX&quot;]">'
                    . 'textlist 2</option>' . "\n"
                    . '<option value="textlist 3" data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">'
                    . 'textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // BS4 custom
        $expect['value-bs4custom'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<select id="field_1" name="field_1" class="custom-select">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected'
                    . ' data-nf-group="[&quot;grpX&quot;]">'
                    . 'textlist 2</option>' . "\n"
                    . '<option value="textlist 3" data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" data-nf-sidecar="[1,2,3,4]">'
                    . 'textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', '')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="hidden"'
                    . ' value="textlist 2"/>' . "\n"
                    . '<span>textlist 2</span>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="textlist 2"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'field_1[]')
                . $this->column2h(
                    '<select id="field_1" name="field_1[]" class="form-control"'
                    . ' multiple>' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected'
                    . ' data-nf-group="[&quot;grpX&quot;]">'
                    . 'textlist 2</option>' . "\n"
                    . '<option value="textlist 3" data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" selected'
                    . ' data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', '')
                . $this->column2h(
                    '<input id="field_1_opt0" name="field_1[]"'
                    . ' type="hidden" value="textlist 2"/>' . "\n"
                    . '<span>textlist 2</span><br/>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]"'
                    . ' type="hidden" value="textlist 4"/>' . "\n"
                    . '<span>textlist 4</span><br/>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['multivalue-hide'] = Block::fromString(
            '<input id="field_1_opt0" name="field_1[0]" type="hidden"'
            . ' value="textlist 2"/>' . "\n"
            . '<input id="field_1_opt1" name="field_1[1]" type="hidden"'
            . ' value="textlist 4"/>' . "\n"
        );

        // Set the presentation to six rows
        $expect['sixrow'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'field_1[]')
                . $this->column2h(
                    '<select id="field_1" name="field_1[]" class="form-control"'
                    . ' multiple size="6">' . "\n"
                    . '<option value="textlist 1">textlist 1</option>' . "\n"
                    . '<option value="textlist 2" selected'
                    . ' data-nf-group="[&quot;grpX&quot;]">'
                    . 'textlist 2</option>' . "\n"
                    . '<option value="textlist 3" data-nf-name="tl3">'
                    . 'textlist 3</option>' . "\n"
                    . '<option value="textlist 4" selected'
                    . ' data-nf-sidecar="[1,2,3,4]">textlist 4</option>' . "\n"
                    . '</select>' . "\n"
                )
            )
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
                $this->column1h('')
                . $this->column2h(
                    '<select id="field_1" name="field_1" class="form-control">' . "\n"
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
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', '')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="hidden" value=""/>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden"/>' . "\n"
        );

        // Now let's give it a value...
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<select id="field_1" name="field_1" class="form-control">' . "\n"
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
            )
        );

        // Test the BS custom presentation
        $expect['value-bs4custom'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<select id="field_1" name="field_1" class="custom-select">' . "\n"
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
            )
        );

        // Test view access
        $expect['value-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', '')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
                    . '<span>Sub Two Item One</span>' . "\n"
                )
            )
        );

        // Test hidden access
        $expect['value-hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="S2I1"/>' . "\n"
        );

        // Set multiple and give it two values
        $expect['multivalue'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label', 'field_1[]')
                . $this->column2h(
                    '<select id="field_1" name="field_1[]" class="form-control" multiple>' . "\n"
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
            )
        );

        // Test view access
        $expect['multivalue-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('', 'label','')
                . $this->column2h(
                    '<input id="field_1_opt0" name="field_1[]" type="hidden" value="Sub One Item One"/>' . "\n"
                    . '<span>Sub One Item One</span><br/>' . "\n"
                    . '<input id="field_1_opt1" name="field_1[]" type="hidden" value="S2I1"/>' . "\n"
                    . '<span>Sub Two Item One</span><br/>' . "\n"
                )
            )
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
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="tel"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="tel"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
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
        $expect['default'] = new Block();
        $expect['default']->body = $this->formGroup(
            $this->column1h('', 'label')
            . $this->column2h(
                '<input id="field_1" name="field_1" type="text" class="form-control"/>'
                . "\n"
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['default'];

        // Test view access
        $expect['view'] = new Block();
        $expect['view']->body = $this->formGroup(
            $this->column1h('', 'label')
            . $this->column2h(
                '<input id="field_1" name="field_1" type="text" class="form-control" readonly/>'
                . "\n"
            )
        );

        // Test hidden access
        $expect['hide'] = new Block();
        $expect['hide']->body = '<input id="field_1" name="field_1" type="hidden"/>' . "\n";

        $this->runCases($cases, $expect);
    }

	public function testFieldTextDataList() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTextDataList();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
            $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" list="field_1_list"/>' . "\n"
                    . "<datalist id=\"field_1_list\">\n"
                    . "<option value=\"textlist 1\"/>\n"
                    . "<option value=\"textlist 2\" data-nf-group=\"[&quot;grpX&quot;]\"/>\n"
                    . "<option value=\"textlist 3\" data-nf-name=\"tl3\"/>\n"
                    . "<option value=\"textlist 4\" data-nf-sidecar=\"[1,2,3,4]\"/>\n"
                    . "</datalist>\n"
                )
            )
        );

        // Test view access: No list is required
        $expect['view'] = Block::fromString(
            $this->formGroup(
            $this->column1h('', 'label')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
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
        $tail = "\n";
        // no labels
        $expect['label-none'] = new Block();
        $expect['label-none']->body = $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . $tail
                )
            );

        // before
        $expect['label-before'] = new Block();
        $expect['label-before']->body = $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n",
                    'input-group'
                )
            );

        // After
        $expect['label-after'] = new Block();
        $expect['label-after']->body = $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n",
                    'input-group'
                )
            );

        // Heading
        $expect['label-head'] = new Block();
        $expect['label-head']->body = $this->formGroup(
                $this->column1h('Header')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"/>' . $tail
                )
            );

        // Help
        $expect['label-help'] = new Block();
        $expect['label-help']->body = $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' aria-describedby="field_1_help"/>' . $tail
                    . '<span class="w-100"></span>' . "\n"
                    . '<small id="field_1_help" class="form-text text-muted">Helpful</small>'
                    . $tail
                )
            );

        // Inner
        $expect['label-inner'] = new Block();
        $expect['label-inner']->body = $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value"'
                    . ' placeholder="inner"/>' . $tail
                )
            );

        // All
        $expect['label-all'] = new Block();
        $expect['label-all']->body = $this->formGroup(
                $this->column1h('Header')
                . $this->column2h(
                    '<div class="input-group-prepend">' . "\n"
                    . '<span class="input-group-text">prefix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" value="the value" placeholder="inner"'
                    . ' aria-describedby="field_1_help"/>' . "\n"
                    . '<div class="input-group-append">' . "\n"
                    . '<span class="input-group-text">suffix</span>' . "\n"
                    . '</div>' . "\n"
                    . '<span class="w-100"></span>' . "\n"
                    . '<small id="field_1_help" class="form-text text-muted">Helpful</small>' . "\n"
                    ,
                    'input-group'
                )
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
        $expect['required'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1"'
                    . ' type="text" class="form-control"'
                    . ' required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Set a maximum length
        $expect['max'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Set a minimum length
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3" required data-nf-req="1"/>' . "\n"
                )
            )
        );

        // Make it match a postal code
        $expect['pattern'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="text"'
                    . ' class="form-control" maxlength="10" minlength="3"'
                    . ' pattern="[a-z][0-9][a-z] ?[0-9][a-z][0-9]" required data-nf-req="1"/>' . "\n"
                )
            )
        );

        $this->runCases($cases, $expect);
    }

	public function testFieldTextarea() {
        $this->logMethod(__METHOD__);
        $cases = RenderCaseGenerator::html_FieldTextarea();
        $expect = [];

        $expect['basic'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<textarea id="field_1" name="field_1"></textarea>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<textarea id="field_1" name="field_1" readonly></textarea>' . "\n"
                )
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
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="time"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="time"'
                    . ' class="form-control" value="20:10"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="time"'
                    . ' class="form-control" value="20:10"'
                    . ' min="19:57" max="20:19"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="time"'
                    . ' class="form-control" value="20:10"'
                    . ' readonly/>' . "\n"
                )
            )
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
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="url"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['write'] = $expect['basic'];

        // Test view access
        $expect['view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="url"'
                    . ' class="form-control" readonly/>' . "\n"
                )
            )
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
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control"/>' . "\n"
                )
            )
        );

        // Set a value
        $expect['value'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"/>' . "\n"
                )
            )
        );

        // Same result with explicit write access
        $expect['value-write'] = $expect['value'];

        // Now test validation
        $expect['minmax'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' min="1957-W30" max="2099-W42"/>' . "\n"
                )
            )
        );

        // Now with view access
        $expect['minmax-view'] = Block::fromString(
            $this->formGroup(
                $this->column1h('')
                . $this->column2h(
                    '<input id="field_1" name="field_1" type="week"'
                    . ' class="form-control" value="2010-W37"'
                    . ' readonly/>' . "\n"
                )
            )
        );

        // Convert to hidden access
        $expect['hide'] = Block::fromString(
            '<input id="field_1" name="field_1" type="hidden" value="2010-W37"/>' . "\n"
        );

        $this->runCases($cases, $expect);
    }

}
