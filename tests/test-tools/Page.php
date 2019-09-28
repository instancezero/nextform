<?php
use Abivia\NextForm\Renderer\Block;

/**
 *
 */
class Page {
    static public function write($title, Block $html, $template = 'boilerplate') {
        $page = file_get_contents(__DIR__ . '/' . $template . '.html');
        $page = str_replace(
            ['{{title}}', '<!--{{head}}-->', '{{form}}', '<!--{{scripts}}-->'],
            [
                $title,
                $html -> head . implode("\n", $html -> styleFiles) . "\n"
                . ($html -> styles ? "<style>\n" . $html -> styles . "</style>" : ''),
                $html -> body,
                implode("\n", $html -> scriptFiles)
                . ($html -> script ? "<script>\n" . $html -> script . "</script>" : ''),
            ],
            $page
        );
        return $page;
    }
}
