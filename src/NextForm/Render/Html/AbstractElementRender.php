<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;

abstract class AbstractElementRender  {

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

}
