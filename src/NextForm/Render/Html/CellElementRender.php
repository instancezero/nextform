<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Block;

class CellElementRender  {

    /**
     *
     * @var Binding
     */
    protected $binding;

    /**
     *
     * @var RenderInterface
     */
    protected $engine;

    public function __construct(RenderInterface $engine, Binding $binding) {
        $this->engine = $engine;
        $this->binding = $binding;
    }

    /**
     * Write a cell element.
     * @param array $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {
        $access = $this->engine->getAccess($options);
        if ($access === 'none') {
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
