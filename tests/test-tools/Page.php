<?php
use Abivia\NextForm\Render\Block;

/**
 *
 */
class Page {
    static public function write($title, Block $html, $template = 'boilerplate') {
        $page = file_get_contents(__DIR__ . '/' . $template . '.html');
        $page = str_replace(
            [
                '{{title}}',
                '<!--{{head}}-->',
                '{{form}}',
                '<!--{{scripts}}-->',
                '{{data}}'
            ],
            [
                $title,
                $html->head . implode("\n", $html->linkedFiles) . "\n"
                . ($html->styles ? "<style>\n" . $html->styles . "</style>" : ''),
                $html->body,
                implode("\n", $html->scriptFiles)
                . ($html->script ? "<script>\n" . $html->script . "</script>" : ''),
                str_replace('--', '- -', print_r($html->data, true)),
            ],
            $page
        );
        return $page;
    }
}
