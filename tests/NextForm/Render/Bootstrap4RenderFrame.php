<?php

/**
 *
 */
include_once __DIR__ . '/HtmlRenderFrame.php';

/**
 * Provides support for SimpleHtml rendering tests.
 */
class Bootstrap4RenderFrame extends HtmlRenderFrame
{

    protected function column1h($text, $tag = 'label', $for = 'field_1', $moreClass = '')
    {
        if ($for !== '') {
            $for = ' for="' . $for . '"';
        }
        $tagClass = trim($moreClass . ' col-sm-2 col-form-label');
        $text = '<' . $tag
            . ($tag == 'label' ? $for : '')
            . ' class="' . $tagClass . '">'
            . ($text === '' ? '&nbsp;' : $text) . '</' . $tag . '>' . "\n";
        return $text;
    }

    protected function column2h($text, $moreClass = '')
    {
        $divClass = trim($moreClass . ' col-sm-10');
        $text = '<div class="' . $divClass. '">' . "\n"
            . $text . '</div>' . "\n";
        return $text;
    }

    protected function formCheck($body, $changeClass = '')
    {
        $changeClass = $changeClass === '' ? 'form-check' : $changeClass;
        $text = '<div class="' . $changeClass . '">' . "\n"
            . $body
            . '</div>' . "\n";
        return $text;
    }

    protected function setMode($dir)
    {
        if ($dir === 'h') {
            // Horizontal layout
            $this->render->setShow('layout:horizontal:2:10');
            self::$defaultFormGroupClass = 'form-group row';
        } else {
            // Vertical layout
            $this->render->setShow('layout:vertical:10');
            self::$defaultFormGroupClass = 'form-group col-sm-10';
        }
    }

    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
        self::$defaultFormGroupClass = 'form-group col-sm-10';
    }

    /**
     * Just kill a warning since we don't perform any tests.
     *
     * @doesNotPerformAssertions
     */
    public function testNothing()
    {

    }

}