<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\Html;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Renderer\Block;

class CellElement  {

    /**
     *
     * @var Binding
     */
    protected $binding;

    /**
     *
     * @var RendererInterface
     */
    protected $engine;

    public function __construct(RendererInterface $engine, Binding $binding) {
        $this->engine = $engine;
        $this->binding = $binding;
    }

    /**
     * Write a cell element.
     * @param array $options
     * @return \Abivia\NextForm\Renderer\Block
     */
    public function render($options = [])
    {
        $access = $this->engine->getAccess($options);
        if ($access === 'hide') {
            $block = new Block();
        } else {
            $block = $this->engine->writeElement(
                'div', ['show' => 'cellElementAttributes', 'force' => true]
            );
        }
        $block->onCloseDone = [$this->engine, 'popContext'];
        $this->engine->pushContext();
        $this->engine->setContext('containerAccess', $access);
        $this->engine->setContext('inCell', true);
        $this->engine->setContext('cellFirstElement', true);
        $this->engine->showDoLayout('form', 'inline');

        return $block;
    }

}
