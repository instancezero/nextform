<?php

namespace Abivia\NextForm\Contracts;

use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Block;


/**
 * Interface for a page renderer.
 * @codeCoverageIgnore
 */
interface RenderInterface
{

    /**
     * Pop the rendering context
     */
    public function popContext();

    /**
     * Push the rendering context
     */
    public function pushContext();

    /**
     * Generate form content for an element.
     * @param Binding $binding The element and data context to be rendered.
     * @param array $options
     *  $options = [
     *      'access' => (string) Access level. One of hide|none|view|write. Default is write.
     *  ]
     * @return Block The generated text and any dependencies, scripts, etc.
     */
    public function render(Binding $binding, $options = []) : Block;

    /**
     * Set global options.
     * @param array $options Render-specific options.
     */
    public function setOptions($options = []);

    /**
     * Set parameters related to the appearance of the form.
     * @param string $settings
     */
    public function setShow($settings);

    /**
     * Look for a show setting, falling back to the form if required.
     * @param string $scope The scope to be searched for a value.
     * @param string $key The index of the value we want.
     * @return mixed
     */
    public function showGet($scope, $key);

    /**
     * Initiate a form.
     * @param array $options Render-specific options.
     */
    public function start($options = []) : Block;

    /**
     * Generate RESTful state data/context for embedding into a form.
     *
     * @param array $state
     */
    static public function stateData($state) : Block;

    /**
     * Turn a list into a suitable output string.
     *
     * @param string[] $list
     * @param array $options Implementation dependent options.
     * @return array
     */
    static public function writeList($list = [], $options = []) : string;

}
