<?php

namespace Abivia\NextForm\Form;

use Abivia\Configurable\Configurable;
use Abivia\NextForm;
use Abivia\NextForm\Form\Element\Element;
use Abivia\NextForm\Traits\JsonEncoderTrait;
use Abivia\NextForm\Traits\ShowableTrait;

/**
 *
 */
class Form implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;
    use ShowableTrait;

    /**
     * A list of top level elements on the form.
     * @var Element[]
     */
    protected $elements;

    static protected $jsonEncodeMethod = [
        'name' => ['drop:blank', 'drop:null'],
        'useSegment' => ['drop:blank'],
        'show' => ['drop:blank'],
        'elements' => ['method:jsonCollapseElements'],
    ];

    protected $name;

    protected $useSegment = '';

    public function __construct()
    {
        $this->show = '';
    }

    /**
     * Sets up options and converts string-valued elements into field objects.
     * @param \stdClass $config
     */
    protected function configureInitialize(&$config)
    {
        // Pass an instance of the form down in Configurable's options so we can
        // access the form directly from deep within the data structures.
        $this->configureOptions['_form'] = &$this;

        // Any elements that are simply strings are converted to basic field objects
        if (isset($config->elements) && is_array($config->elements)) {
            foreach ($config->elements as &$value) {
                if (is_string($value)) {
                    $value = self::expandField($value);
                }
            }
        }
    }

    protected function configureClassMap($property, $value)
    {
        $result = false;
        if ($property == 'elements') {
            $result = new \stdClass;
            $result->key = '';
            $result->className = [Element::class, 'classFromType'];
        }
        return $result;
    }

    /**
     * Generate a form object from a file
     * @param string $formFile
     * @return \Abivia\NextForm
     * @throws RuntimeException
     */
    static public function fromFile($formFile)
    {
        $form = new Form();
        if (!$form->configure(json_decode(file_get_contents($formFile)), true)) {
            throw new \RuntimeException(
                'Failed to load ' . $formFile . "\n"
                . implode("\n", $form->configureErrors)
            );
        }
        return $form;
    }

    static public function expandField($value)
    {
        $groupParts = explode(NextForm::GROUP_DELIM, $value);
        // Convert to a useful class
        $obj = new \stdClass;
        $obj->type = 'field';
        $obj->object = array_shift($groupParts);
        if (!empty($groupParts)) {
            $obj->memberOf = $groupParts;
        }
        return $obj;
    }

    /**
     * Get a list of top level elements in the form.
     * @return Element[]
     */
    public function getElements() {
        return $this->elements;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSegment()
    {
        return $this->useSegment;
    }

    /**
     * See if any of the contained elements can be represented as a shorthand string.
     * @param type $elementList
     */
    protected function jsonCollapseElements($elementList)
    {
        foreach ($elementList as &$element) {
            $element = $element->jsonCollapse();
        }
        return $elementList;
    }

    protected function options($options)
    {
    }

}
