<?php

namespace Abivia\NextForm\Form\Binding;

use Abivia\NextForm\NextForm;
use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Data\SchemaCollection;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\Render\Block;
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
     * Connect data bindings in the schemas
     *
     * @param SchemaCollection $schemas
     */
    public function bindSchema(?SchemaCollection $schemas)
    {
        parent::bindSchema($schemas);
        foreach ($this->bindings as $binding) {
            $binding->bindSchema($schemas);
        }
    }

    /**
     * Use a renderer to turn this element into part of the form.
     *
     * @param RenderInterface $renderer Any Render object.
     * @param AccessInterface $access Any access control object.
     * @param array $options Options: accessOverride to override default access.
     * @return Block
     */
    public function generate(
        RenderInterface $renderer,
        AccessInterface $access,
        $options = []
    ) : Block {
        // Container access: use an empty segment and the element name
        $options = $this->checkAccess(
            $access, '', $this->element->getName(), $options
        );
        $containerData = $renderer->render($this, $options);
        foreach ($this->bindings as $binding) {
            $containerData->merge(
                $binding->generate($renderer, $access, $options)
            );
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
     * @param NextForm $manager
     * @return $this
     */
    public function setManager(NextForm $manager)
    {
        parent::setManager($manager);
        foreach ($this->bindings as $binding) {
            $binding->setManager($manager);
        }
        return $this;
    }

    /**
     * Translate any contained bindings.
     *
     * @param Translator $translator
     * @return $this
     */
    public function translate(Translator $translator = null) : Binding
    {
        parent::translate($translator);

        foreach ($this->bindings as $binding) {
            $binding->translate($translator);
        }
        return $this;
    }

}