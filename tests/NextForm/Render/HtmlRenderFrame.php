<?php
/**
 * Common support / logging for render tests
 */
use Abivia\NextForm\Renderer\Attributes;

define('NF_TEST_ROOT', dirname(dirname(__DIR__)) . '/');

include_once __DIR__ . '/RendererCaseGenerator.php';
include_once __DIR__ . '/RendererCaseRunner.php';
include_once NF_TEST_ROOT . 'test-tools/HtmlTestLogger.php';
include_once NF_TEST_ROOT . 'test-tools/Page.php';

/**
 * Provides support for all the rendering tests.
 */
class HtmlRenderFrame extends \PHPUnit\Framework\TestCase
{
    use HtmlTestLogger;
    use RendererCaseRunner;

    protected $defaultFormGroupClass = '';
    protected $testObj;

    protected function column1($text, $tag = 'label', $for = 'field_1')
    {
        if ($text === '') {
            return '';
        }
        $for = $for === '' ? '' : ' for="' . $for . '"';
        $text = '<' . $tag
            . ($tag === 'label' ? $for : '')
            . '>'
            . ($text === '' ? '&nbsp;' : $text) . '</' . $tag . '>' . "\n";
        return $text;
    }

    protected function column2($text)
    {
        return $text;
    }

    protected function formGroup($body, $options = []) {
        $attr = '';
        $id = $options['id'] ?? 'field_1';
        $attr .= ' id="' . $id . '_container' . '"';
        $class = isset($options['class'])
            ? $options['class']
            : $this->defaultFormGroupClass;
        $class = trim(
            ($options['classPrepend'] ?? '')
            . ' ' . $class
            . ' ' . ($options['classAppend'] ?? '')
        );
        $attr .= $class ? ' class="' . $class . '"' : '';
        $element = $options['element'] ?? 'div';
        $attr .= isset($options['style']) ? ' style="' . $options['style'] . '"' : '';
        $attr .= ' data-nf-for="' . $id . '"';
        $text = '<' . $element . $attr . '>' . "\n"
            . $body
            . '</' . $element . '>' . "\n";
        return $text;
    }

    public static function generatePage($forTestFile, $obj) : void {
        if (!file_exists(NF_TEST_ROOT . 'logs')) {
            return;
        }
        $attrs = new Attributes();
        $attrs->set('id', 'nfTestForm');
        $attrs->set('name', 'form_1');
        $data = $obj->start(
            [
                'action' => 'http://localhost/nextform/post.php',
                'attributes' => $attrs,
                'token' => 'not-such-a-random-token',
            ]
        );

        $data->body .= self::$allHtml;
        $data->close();
        $relDir = substr(dirname($forTestFile), strlen(NF_TEST_ROOT));
        $logFile = NF_TEST_ROOT . 'logs/' . $relDir . '/'
            . pathinfo($forTestFile, PATHINFO_FILENAME) . '-out.html';
        self::logPage($logFile, $data);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testNothing()
    {

    }

}