<?php

/**
 *
 */
include_once __DIR__ . '/Bootstrap4RenderFrame.php';

/**
 * Provides support for SimpleHtml rendering tests.
 */
class Bootstrap4HorizontalRenderFrame extends Bootstrap4RenderFrame
{

    protected function column1($text, $tag = 'label', $for = 'field_1', $moreClass = '')
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

    protected function column2($text, $moreClass = '')
    {
        $divClass = trim($moreClass . ' col-sm-10');
        $text = '<div class="' . $divClass. '">' . "\n"
            . $text . '</div>' . "\n";
        return $text;
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