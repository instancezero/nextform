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

    protected function formCheck($body, $changeClass = '')
    {
        $changeClass = $changeClass === '' ? 'form-check' : $changeClass;
        $text = '<div class="' . $changeClass . '">' . "\n"
            . $body
            . '</div>' . "\n";
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