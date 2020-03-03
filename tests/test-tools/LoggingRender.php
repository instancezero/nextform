<?php

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Block;

/**
 *
 */
class LoggingRender implements RenderInterface {

    protected $log = [];

    public function getLog() {
        return $this->log;
    }

    /**
     * Pop the rendering context
     */
    public function popContext()
    {

    }

    /**
     * Push the rendering context
     */
    public function pushContext()
    {

    }

    /**
     * Generate form content for an element.
     * @param Binding $binding The element and data context to be rendered.
     * @param array $options
     *  $options = [
     *      'access' => (string) Access level. One of hide|none|view|write. Default is write.
     *  ]
     * @return Block The generated text and any dependencies, scripts, etc.
     */
    public function render(Binding $binding, $options = []) : Block
    {
        $this->log[$binding->getNameOnForm() . '/'
            . $binding->getElement()->getName()] = $options['access'];
        return new Block();
    }

    /**
     * Set global options.
     * @param array $options Render-specific options.
     */
    public function setOptions($options = [])
    {

    }

    /**
     * Set parameters related to the appearance of the form.
     * @param string $settings
     */
    public function setShow($settings)
    {

    }

    public function showGet($scope, $key)
    {
        return '';
    }

    /**
     * Initiate a form.
     * @param array $options Render-specific options.
     */
    public function start($options = []) : Block
    {
        $this->log = [];
        return new Block();
    }

    /**
     * Embed RESTful state data/context into the form.
     * @param array $state
     */
    public function stateData($state) : Block
    {
        return new Block();
    }

}
