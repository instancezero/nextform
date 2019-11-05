<?php

/**
 *
 */
namespace Abivia\NextForm\Renderer\CommonHtml;

use Abivia\NextForm\Contracts\RendererInterface;
use Abivia\NextForm\Form\Binding\Binding;
use Abivia\NextForm\Renderer\Block;

class FieldElement  {

    /**
     *
     * @var Binding
     */
    protected $binding;

    /**
     *
     * @var RendererInterface
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

    public function __construct(RendererInterface $engine, Binding $binding) {
        $this->engine = $engine;
        $this->binding = $binding;
    }

    protected function epilog()
    {
        return new Block();
    }

    protected function getFieldHandler($type)
    {
        $engineClass = \get_class($this->engine);
        $classType = \ucfirst($type);

        if (!isset(self::$handlerCache[$engineClass])) {
            $fieldHandler = self::$handlerCache[$engineClass] = [];
        }
        if (isset(self::$handlerCache[$engineClass][$classType])) {
            $fieldHandler = self::$handlerCache[$engineClass][$classType];
        } else {
            // Look for a specific handler under the current renderer
            $fieldHandler = $engineClass . '\\FieldElement\\' . $classType;
            if (!\class_exists($fieldHandler)) {

                // Look for a specific handler in the common renderer
                $lastPos = \strrpos($engineClass, '\\');
                $fieldHandler = \substr($engineClass, 0, $lastPos + 1)
                    . '\\CommonHtml\\FieldElement\\' . $classType;
                if (!\class_exists($fieldHandler)) {

                    // Fall back to the common handler
                    $fieldHandler = $engineClass . '\\FieldElement\\Common';

                    // Give up
                    if (!\class_exists($fieldHandler)) {
                        throw new RuntimeException(
                            'No render class for field type ' . $type
                        );
                    }
                }
            }
            self::$handlerCache[$engineClass][$classType] = $fieldHandler;
        }
        return new $fieldHandler($this->engine, $this->binding);
    }

    /**
     * Write a field element.
     * @param array $options
     * @return \Abivia\NextForm\Renderer\Block
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
                && $options['access'] === 'write' && !$options['confirm']
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

}
