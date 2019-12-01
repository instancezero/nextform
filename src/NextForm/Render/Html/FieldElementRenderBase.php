<?php

/**
 *
 */
namespace Abivia\NextForm\Render\Html;

use Abivia\NextForm\Contracts\RenderInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Render\Attributes;
use Abivia\NextForm\Render\Block;

class FieldElementRenderBase
{

    const FIELD_CLASS = 'FieldElementRender';
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

    static $handlerCache = [];

    /**
     * Types of <input> that we'll auto-generate a confirmation for
     * @var array
     */
    static $inputConfirmable = [
        'email', 'number', 'password', 'tel', 'text',
    ];

    public function __construct(RenderInterface $engine, Binding $binding) {
        $this->engine = $engine;
        $this->binding = $binding;
    }

    /**
     * Render a data list, if there is one.
     * @param \Abivia\NextForm\Render\Attributes $attrs Parent attributes.
     * @param Binding $binding The binding for the element we're rendering.
     * @param string $type The element type
     * @param array $options Options, specifically access rights.
     * @return \Abivia\NextForm\Render\Block
     */
    public function dataList(Attributes $attrs, $type, $options)
    {
        $block = new Block();
        // Check for a data list, if there is write access.
        $list = $this->engine->getAccess($options) === 'write'
            && Attributes::inputHas($type, 'list')
            ? $this->binding->getList(true) : [];
        if (!empty($list)) {
            $attrs->set('list', $attrs->get('id') . '_list');
            $block->body = '<datalist id="' . $attrs->get('list') . "\">\n";
            foreach ($list as $option) {
                $optAttrs = new Attributes();
                $optAttrs->set('value', $option->getValue());
                $optAttrs->setIfNotNull('data-nf-name', $option->getName());
                $optAttrs->setIfNotEmpty('*data-nf-group', $option->getGroups());
                $optAttrs->setIfNotNull('*data-nf-sidecar', $option->sidecar);
                $block->body .= $this->engine->writeTag('option', $optAttrs) . "\n";
            }
            $block->body .= "</datalist>\n";
        }
        return $block;
    }

    /**
     * Get an instance of the class that handles fields of the specified type.
     *
     * @param string $type The field type.
     * @return object An instance of the required handler.
     * @throws \RuntimeException If an acceptable class doesn't exist.
     */
    protected function getFieldHandler($type)
    {
        $engineClass = \get_class($this->engine);
        $classType = \ucfirst($type);

        if (!isset(self::$handlerCache[$engineClass])) {
            self::$handlerCache[$engineClass] = [];
        }
        if (isset(self::$handlerCache[$engineClass][$classType])) {
            $fieldHandler = self::$handlerCache[$engineClass][$classType];
        } else {
            // Look for a specific handler under the current renderer
            $fieldHandler = $engineClass . '\\' . self::FIELD_CLASS
                . '\\' . $classType;
            if (!\class_exists($fieldHandler)) {

                // Look for a specific handler in the common renderer
                $lastPos = \strrpos($engineClass, '\\');
                $fieldHandler = \substr($engineClass, 0, $lastPos + 1)
                    . '\\Html\\' . self::FIELD_CLASS . '\\' . $classType;
                if (!\class_exists($fieldHandler)) {

                    // Fall back to the common handler
                    $fieldHandler = $engineClass . '\\' . self::FIELD_CLASS
                        . '\\Common';

                    // Give up
                    if (!\class_exists($fieldHandler)) {
                        throw new \RuntimeException(
                            'No render class for field type ' . $type
                        );
                    }
                }
            }
            self::$handlerCache[$engineClass][$classType] = $fieldHandler;
        }
        return new $fieldHandler($this, $this->engine, $this->binding);
    }

    /**
     * Write a field element.
     * @param array $options
     * @return \Abivia\NextForm\Render\Block
     */
    public function render($options = [])
    {
        /*
            'image' remains incomplete...
        */
        $result = new Block();
        $presentation = $this->binding->getDataProperty()->getPresentation();
        $type = $presentation->getType();
        switch ($type) {
            case 'checkbox':
            case 'radio':
                $fieldHandler = $this->getFieldHandler('checkbox');
                break;

            default:
                $fieldHandler = $this->getFieldHandler($type);
                break;
        }

        $options['confirm'] = false;
        $repeater = true;
        while ($repeater) {
            $block = $fieldHandler->render($options);
            // Check to see if we need to generate a confirm field, and
            // haven't already done so...
            if (
                in_array($type, self::$inputConfirmable)
                && $presentation->getConfirm()
                && $this->engine->getAccess($options) === 'write'
                && !$options['confirm']
            ) {
                $options['confirm'] = true;
            } else {
                $repeater = false;
            }
            $result->merge($block);
        }

        $result->merge($this->engine->renderTriggers($this->binding));

        return $result;
    }

    public function setFieldHandler($type, $className)
    {
        $engineClass = \get_class($this->engine);
        $classType = \ucfirst($type);

        if (!isset(self::$handlerCache[$engineClass])) {
            self::$handlerCache[$engineClass] = [];
        }
        self::$handlerCache[$engineClass][$classType] = $className;

        return $this;
    }

}
