<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\Manager;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Form\Form;
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

    public function addBinding(Binding $binding) : self
    {
        $this->bindings[] = $binding;
        return $this;
    }

    /**
     * Connect data bindings in a schema
     *
     * @param Schema $schema
     */
    public function bindSchema(Schema $schema)
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
        foreach ($this->bindings as $binding) {
            $containerData->merge($binding->generate($renderer, $access, $translate));
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
     * Connect this binding to a manager
     * @param Manager $manager
     * @return $this
     */
    public function setManager(Manager $manager)
    {
        parent::setManager($manager);
        foreach ($this->bindings as $binding) {
            $binding->setManager($manager);
        }
        return $this;
    }

}