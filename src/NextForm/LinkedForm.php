<?php

namespace Abivia\NextForm;

use Abivia\NextForm\Contracts\FormInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Form\Binding\FieldBinding;
use Abivia\NextForm\Form\Binding\ContainerBinding;
use Abivia\NextForm\NextForm;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;

/**
 * A Linked Form is a renderable connection between a schema definition,
 * a form definition, and the data to be placed on the form.
 */
class LinkedForm
{
    /**
     * A list of all bindings in the form.
     * @var Binding[]
     */
    protected $allBindings = [];

    /**
     * A list of top level bindings.
     * @var Binding[]
     */
    protected $bindings = [];

    /**
     * The form definition.
     * @var Form
     */
    protected $form;

    /**
     * The results of generating the form.
     * @var Block
     */
    protected $formBlock;

    /**
     * The HTML id of the form on the page.
     * @var string
     */
    protected $id;

    /**
     * The managing NextForm object.
     *
     * @var NextForm
     */
    protected $manager;

    /**
     * The form name in the generated output
     * @var string
     */
    protected $nameHtml;

    /**
     * Maps form names to form bindings
     * @var array
     */
    protected $nameMap;

    /**
     * Options that get passed to the renderer.
     * @var array
     */
    protected $options;

    /**
     * The name of a schema segment not to use as a prefix in form names.
     * @var string
     */
    protected $segmentNameDrop;

    public function __construct(FormInterface $form = null, $options = [])
    {
        $this->form = $form;
        $this->setOptions($options);
    }

    /**
     * Assign name attributes for all the bindings on the form.
     *
     * @return $this
     */
    protected function assignNames()
    {
        $this->nameMap = [];
        $containerCount = 1;

        foreach ($this->allBindings as $binding) {
            if ($binding instanceof FieldBinding) {
                $parts = \explode(NextForm::SEGMENT_DELIM, $binding->getObject());
                if ($parts[0] === $this->segmentNameDrop) {
                    unset($parts[0]);
                }
                $baseName = \implode('_', $parts);
                $name = $baseName;
                $confirmName = $baseName . NextForm::$confirmLabel;
                $append = 0;
                while (
                    isset($this->nameMap[$name])
                    || isset($this->nameMap[$confirmName])
                ) {
                    $name = $baseName . '_' . ++$append;
                    $confirmName = $name . '_' . $append . NextForm::$confirmLabel;
                }
                $this->nameMap[$name] = $binding;
                $binding->setNameOnForm($name);
            } elseif ($binding instanceof ContainerBinding) {
                $baseName = 'container_';
                $name = $baseName . $containerCount;
                while (isset($this->nameMap[$name])) {
                    $name = $baseName . ++$containerCount;
                }
                $this->nameMap[$name] = $binding;
                $binding->setNameOnForm($name);
            }
        }
        return $this;
    }

    /**
     * Create bindings for all elements in the form
     * @param NextForm $manager The form manager
     * @return $this
     */
    public function bind(NextForm $manager)
    {
        $this->manager = $manager;
        $this->allBindings = [];
        $this->bindings = [];
        $this->segmentNameDrop = $manager->getSegmentNameDrop();
        foreach ($this->form->getElements() as $element) {
            $binding = Binding::fromElement($element);
            $this->linkBinding($binding);
            $this->bindings[] = $binding;
            $manager->connectBinding($binding);
            $this->bindChildren($manager, $binding);
        }

        return $this;
    }

    /**
     * Connect bindings for all elements contained in another element.
     * @param NextForm $manager
     * @param Binding $parent
     */
    protected function bindChildren(NextForm $manager, Binding $parent) {
        if ($parent instanceof ContainerBinding) {
            foreach ($parent->getBindings() as $binding) {
                $this->linkBinding($binding);
                $manager->connectBinding($binding);
                $this->bindChildren($manager, $binding);
            }
        }
    }

    /**
     * Generate the form.
     *
     * @return Block
     */
    public function generate() : Block {

        // Assign field names
        $this->assignNames();

        // Run the translations.
        $translator = $this->manager->service('Translate');
        foreach ($this->allBindings as $binding) {
            $binding->translate($translator);
        }

        // Start the form
        $renderer = $this->manager->service('Render');
        $renderer->setOption(
            'Captcha',
            $this->manager->serviceProvider('Captcha')
        );
        $this->formBlock = $renderer->start($this->options);

        // Inject any state data from the options
        if (
            isset($this->options['state'])
            && is_array($this->options['state'])
        ) {
            $this->formBlock->merge(
                $renderer->stateData($this->options['state'])
            );
        }

        // Write all the bindings
        foreach ($this->bindings as $binding) {
            $this->formBlock->merge(
                $binding->generate($renderer, $this->manager->service('Access'))
            );
        }

        // Close the form and done.
        $this->formBlock->close();

        return $this->formBlock;
    }

    /**
     * Get the HTML id for this form.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    public function getBlock() : Block
    {
        return $this->formBlock;
    }

    /**
     * Build the link between the binding and this linked form.
     *
     * @param Binding $binding
     */
    protected function linkBinding(Binding $binding)
    {
            $this->allBindings[] = $binding;
            $binding->setLinkedForm($this);
    }

    /**
     * Set form options.
     *
     * @param array $options Generation options, optional unless stated otherwise:
     *  $options = [
     *      'attributes' => (Render\Attributes) Attributes to be added to the form element.
     *      'id' => The HTML id for the form. If not provided, one is generated.
     *              May also be passed through in 'attributes'.
     *      'name' => The HTML name for the form. If not provided, the id is used.
     *              May also be passed through in 'attributes'.
     *  ]
     *  @return $this
     */
    public function setOptions($options)
    {
        // Make sure we have attributes
        if (!isset($options['attributes'])) {
            $options['attributes'] = new Attributes();
        }
        $attrs = &$options['attributes'];

        // If we were passed an ID, clean it up and add to attributes
        if (isset($options['id'])) {
            $attrs->set('id', $options['id']);
        }

        // Pick up the ID or auto-generate one
        if ($attrs->has('id')) {
            $this->id = $attrs->get('id');
        } else {
            if ($this->id === null) {
                $this->id = NextForm::htmlIdentifier('form', true);
            }
            $attrs->set('id', $this->id);
        }

        // If we have been passed a name, use it
        if (isset($options['name'])) {
            $this->name = $options['name'];
            $attrs->set('name', $this->name);
        }

        // If there is no name, use the ID
        if (!$attrs->has('name')) {
            $this->name = $this->id;
            $attrs->set('name', $this->id);
        }

        // Pass the ID to the form
        $options['id'] = $this->id;

        $this->options = $options;

        return $this;
    }

}
