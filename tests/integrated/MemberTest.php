<?php
include_once __DIR__ . '/../test-tools/JsonComparison.php';
include_once __DIR__ . '/../test-tools/MockTranslate.php';
include_once __DIR__ . '/../test-tools/Page.php';

use Abivia\NextForm\Manager;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\ContainerBinding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\SimpleHtml;
use Abivia\NextForm\Renderer\Bootstrap4;

class FlatRenderer implements Abivia\NextForm\Contracts\RendererInterface {

    public function __construct($options = []) {

    }

    public function popContext() {
    }

    public function pushContext() {

    }

    public function render(Binding $binding, $options = []) : Block {
        $result = new Block();
        $type = $binding->getElement()->getType();
        $result->body = $type;
        $name = $binding->getFormName();
        if ($name) {
            $result->body .= ' (' . $name . ')';
        }
        if ($binding instanceof FieldBinding) {
            $result->body .= ' object = ' . $binding->getObject();
        }
        $result->body .= "\n";
        if ($binding instanceof ContainerBinding) {
            $result->post = 'Close ' . $type . "\n";
        }
        return $result;
    }

    public function setOptions($options = []) {

    }

    public function setShow($settings) {

    }

    public function start($options = []) : Block {
        $result = new Block();
        $result->body = "Form\n";
        $result->post = "End form\n";
        return $result;
    }

}

class MemberTest extends \PHPUnit\Framework\TestCase {
    use JsonComparison;

    public $memberForm;
    public $memberSchema;

    public function setUp() : void {
        $this->memberForm  = Form::fromFile(__DIR__ . '/member-form.json');
        $this->memberSchema = Schema::fromFile(__DIR__ . '/member-schema.json');
    }

    /**
     * Integration test for schema read/write.
     * @coversNothing
     */
    public function testSchemaLoad() {
        $obj = new Schema();
        $jsonFile = __DIR__ . '/member-schema.json';
        $config = json_decode(file_get_contents($jsonFile));
        $this->assertTrue(false !== $config, 'Error JSON decoding schema.');
        $populate = $obj->configure($config, true);
        if ($populate) {
            $errors = '';
        } else {
            $errors = $obj->configureGetErrors();
            $errors = 'Schema load:' . "\n" . implode("\n", $errors) . "\n";
        }
        $this->assertTrue($populate, $errors);
        // Save the result as JSON so we can compare
        $resultJson = json_encode($obj, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/member-schema-out.json', $resultJson);
        // Stock JSON to stdClass for comparison
        $result = json_decode($resultJson);
        // Reload the original configuration
        $config = json_decode(file_get_contents($jsonFile));
        $this->assertTrue($this->jsonCompare($config, $result));
    }

    /**
     * Integration test for form read/write.
     * @coversNothing
     */
    public function testFormLoad() {
        Manager::boot();
        $obj = new Form();
        $jsonFile = __DIR__ . '/member-form.json';
        $config = json_decode(file_get_contents($jsonFile));
        $this->assertTrue(false != $config, 'Error JSON decoding form.');
        $populate = $obj->configure($config, true);
        if ($populate) {
            $errors = '';
        } else {
            $errors = $obj->configureGetErrors();
            $errors = 'Form load:' . "\n" . implode("\n", $errors) . "\n";
        }
        $this->assertTrue($populate, $errors);
        // Save the result as JSON so we can compare
        $resultJson = json_encode($obj, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/member-form-out.json', $resultJson);

        // Stock JSON to stdClass for comparison; reload the config
        $result = json_decode($resultJson);
        $jsonFile = __DIR__ . '/member-form-baseline.json';
        $config = json_decode(file_get_contents($jsonFile));
        $this->assertTrue($this->jsonCompare($config, $result));
    }

    /**
     * Integration test for form generation
     * @coversNothing
     */
    public function testGenerateUnpopulated() {
        Manager::boot();
        $render = new FlatRenderer();

        $manager = new Manager();
        $manager->setForm($this->memberForm);
        $manager->setSchema($this->memberSchema);
        $manager->setRenderer($render);
        $manager->setTranslator(new MockTranslate());
        $page = $manager->generate(['action' => 'myform.php']);
        $this->assertTrue(true);
    }

    /**
     * Integration test for form generation
     * @coversNothing
     */
    public function testGeneratePopulated() {
        Manager::boot();
        $manager = new Manager();
        $manager->bind($this->memberForm, $this->memberSchema);
        $data = [
            'id' => 0,
        ];
        $manager->populate($data, 'members');
        $manager->setRenderer(new FlatRenderer());
        $manager->setTranslator(new MockTranslate());
        $manager->generate(['action' => 'myform.php']);
        $this->assertTrue(true);
    }

    public function testSimpleHtmlRenderUnpopulated() {
        Manager::boot();
        $manager = new Manager();
        $manager->setForm($this->memberForm);
        $manager->setSchema($this->memberSchema);
        $manager->setRenderer(new SimpleHtml());
        $translator = new MockTranslate();
        $manager->setTranslator($translator);
        $html = $manager->generate(['action' => 'http://localhost/nextform/post.php']);
        file_put_contents(__DIR__ . '/' . __FUNCTION__ . '.html', Page::write(__FUNCTION__, $html));

        $this->assertTrue(true);
    }

    public function testBootstrap4RenderUnpopulated() {
        Manager::boot();
        $manager = new Manager();
        $manager->setForm($this->memberForm);
        $manager->setSchema($this->memberSchema);
        $manager->setRenderer(new Bootstrap4());
        $translator = new MockTranslate();
        $manager->setTranslator($translator);

        $html = $manager->generate(['action' => 'http://localhost/nextform/post.php']);
        $html->script .= file_get_contents(__DIR__ . '/memberform.js');
        file_put_contents(__DIR__ . '/' . __FUNCTION__ . '.html', Page::write(__FUNCTION__, $html));

        $translator->append = ' (lang2)';
        $html = $manager->generate(['action' => 'http://localhost/nextform/post.php']);
        $html->script .= file_get_contents(__DIR__ . '/memberform.js');
        file_put_contents(__DIR__ . '/' . __FUNCTION__ . '_lang2.html', Page::write(__FUNCTION__, $html));

        $this->assertTrue(true);
    }

}
