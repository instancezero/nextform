<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\SimpleHtml;

use Abivia\NextForm\Renderer\Block;
use Abivia\NextForm\Renderer\CommonHtml\StaticElement as BaseStatic;

class StaticElement extends BaseStatic {

    protected function epilog()
    {
        $block = Block::fromString(
            $this->engine->queryContext('inCell') ? '&nbsp;' : "<br/>\n"
        );
        return $block;
    }

}
