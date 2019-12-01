<?php

/**
 *
 */
include_once __DIR__ . '/HtmlRenderFrame.php';

/**
 * Provides support for SimpleHtml rendering tests.
 */
class SimpleHtmlRenderFrame extends HtmlRenderFrame
{

    protected function column1($text, $tag = 'label', $for = 'field_1')
    {
        $for = $for === '' ? '' : ' for="' . $for . '"';
        $text = '<' . $tag
            . ($tag === 'label' ? $for : '')
            . ' style="display:inline-block; vertical-align:top; width:20%">'
            . ($text === '' ? '&nbsp;' : $text) . '</' . $tag . '>' . "\n";
        return $text;
    }

    protected function column2($text)
    {
        $text = '<div style="display:inline-block; vertical-align:top; width:40%">' . "\n"
            . $text . '</div>' . "\n";
        return $text;
    }

    protected function setMode($dir)
    {
        if ($dir === 'h') {
            // Horizontal layout
            $this->render->setShow('layout:hor:20%:40%');
            self::$defaultFormGroupClass = '';
        } else {
            // Vertical layout
            $this->render->setShow('layout:vertical');
            self::$defaultFormGroupClass = '';
        }
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