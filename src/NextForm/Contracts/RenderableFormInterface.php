<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Contracts\AccessInterface;
use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Form;
use Abivia\NextForm\NextForm;
use Abivia\NextForm\Render\Block;
use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * A Bound Form is a renderable connection between a schema definition,
 * a form definition, and the data to be placed on the form.
 */
interface RenderableFormInterface
{

    public function __construct(Form $form = null, $options = []);

    /**
     * Create bindings for all elements in the form
     * @return $this
     */
    public function bind(NextForm $manager);

    /**
     * Generate the form.
     *
     * @return Block
     */
    public function generate(
        RenderInterface $renderer,
        ?AccessInterface $access = null,
        ?Translator $translator = null
    ) : Block;

    /**
     * Get the HTML id for this form.
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Get the generated form.
     *
     * @return Block
     */
    public function getBlock() : Block;

    /**
     * Set form options.
     *
     * @param array $options Generation options.
     *
     *  @return $this
     */
    public function setOptions($options);

}
