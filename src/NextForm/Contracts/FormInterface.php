<?php

namespace Abivia\NextForm\Contracts;

/**
 *
 */
interface FormInterface
{

    /**
     * Generate a form object from a file
     * @param string $formFile
     * @return ?FormInterface
     */
    static public function fromFile($formFile) : ?FormInterface;

    /**
     * Get a list of top level elements in the form.
     * @return Element[]
     */
    public function getElements();

    public function getName();

    public function getSegment();

}
