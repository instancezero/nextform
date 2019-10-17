<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Renderer\Block;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * Class for any binding that contains a list of sub-bindings.
 */
class ContainerBinding Extends Binding
{

    /**
     * The list of bindings contained by this instance.
     * @var Element[]
     */
    protected $bindings = [];

    /**
     * Connect data bindings in a schema
     * @param \Abivia\NextForm\Data\Schema $schema
     */
    public function bindSchema(\Abivia\NextForm\Data\Schema $schema)
    {
        parent::bindSchema($schema);
        foreach ($this->bindings as $binding) {
            $binding->bindSchema($schema);
        }
    }

    /**
     * Use a renderer to turn this element into part of the form.
     * @param RendererInterface $renderer Any Renderer object.
     * @param AccessInterface $access Any access control object
     * @param Translator $translate Any translation object.
     * @return Block
     */
    public function generate(
        RendererInterface $renderer,
        AccessInterface $access,
        Translator $translate
    ) : Block {
        $this->translate($translate);
        $options = false; // $access->hasAccess(...)
        $options = ['access' => 'write'];
        $containerData = $renderer->render($this, $options);
        foreach ($this->bindings as $element) {
            $containerData->merge($element->generate($renderer, $access, $translate));
        }
        $containerData->close();
        return $containerData;
    }

    /**
     * Get the bindings in this container.
     * @return Element[]
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Connect this binding to a form
     * @param NextForm $form
     * @return $this
     */
    public function setForm(NextForm $form)
    {
        parent::setForm($form);
        foreach ($this->bindings as $binding) {
            $binding->setForm($form);
        }
        return $this;
    }

}