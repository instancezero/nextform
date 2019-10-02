<?php

namespace Abivia\NextForm\Trigger;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 *
 */
class Trigger implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * @var Action A List of actions to take when this trigger fires.
     */
    protected $actions;
    /**
     * @var string An event that causes this trigger to fire
     */
    protected $event;
    /**
     * @var array A list of valid event types
     */
    static protected $eventValidation = [
        'onchange', 'oninvalid', 'onvalid',
    ];
    /**
     * @var array Rules for converting properties to JSON
     */
    static protected $jsonEncodeMethod = [
        'event' => ['drop:null'],
        'value' => [],
        'actions' => [],
    ];
    protected $type;
    protected $value;

    /**
     * Map a property to a class.
     * @param string $property The current class property name.
     * @param mixed $value The value to be stored in the property, made available for inspection.
     * @return mixed An object containing a class name and key, or false
     * @codeCoverageIgnore
     */
    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'actions' => ['className' => '\Abivia\NextForm\Trigger\Action', 'key' => ''],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    protected function configureComplete()
    {
        // Exactly one of event or value must be set.
        if (isset($this -> event) == isset($this -> value)) {
            return false;
        }
        if (isset($this -> event)) {
            $this -> type = 'event';
        } else {
            $this -> type = 'value';
        }
        return true;
    }

    /**
     * Initialize this object at the start of configuration.
     */
    protected function configureInitialize()
    {
        $this -> event = null;
        $this -> type = null;
        $this -> value = null;
    }

    protected function configurePropertyAllow($property)
    {
        return in_array($property, ['actions', 'event', 'value']);
    }

    protected function configureValidate($property, &$value)
    {
        switch ($property) {
            case 'event':
                $value = strtolower($value);
                $result = in_array($value, self::$eventValidation);
                break;
            default:
                $result = true;
        }
        return $result;
    }

    public function getActions()
    {
        return $this -> actions;
    }

    public function getEvent()
    {
        return $this -> event;
    }

    public function getType()
    {
        return $this -> type;
    }

    public function getValue()
    {
        return $this -> value;
    }

}
