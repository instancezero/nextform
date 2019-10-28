<?php

namespace Abivia\NextForm\Trigger;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Manager;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 *
 */
class Action implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;

    static protected $jsonEncodeMethod = [
        'target' => ['drop:empty', 'drop:null', 'scalarize'],
        'subject' => [],
        'value' => [],
    ];
    protected $subject;
    static protected $subjectValidation = [
        'display', 'enable', 'readonly', 'script', 'value', 'visible'
    ];
    protected $target = [];
    protected $value;

    protected function configureInitialize(&$config)
    {
        if (\is_string($config)) {
            $expanded = new \stdClass;
            $parts = \explode(Manager::GROUP_DELIM, $config);
            \array_push($parts, null);
            \array_push($parts, null);
            $expanded->target = \explode(',', $parts[0]);
            \array_walk($expanded->target, 'trim');
            $expanded->subject = $parts[1];
            $expanded->value = $parts[2];
            $config = $expanded;
        }
    }

    protected function configureComplete()
    {
        // if the subject is boolean valued, convert strings to boolean.
        if (!\in_array($this->subject, ['script', 'value'])) {
            if (\strtolower($this->value) === 'true') {
                $this->value = true;
            } elseif (\strtolower($this->value) === 'false') {
                $this->value = false;
            }
        }
        return true;
    }

    protected function configureValidate($property, &$value)
    {
        switch ($property) {
            case 'subject':
                if (!($result = \in_array($value, self::$subjectValidation))) {
                    $this->configureLogError(
                        '"' . (\is_scalar($value) ? $value : \gettype($value))
                        . '" is not a valid value for "' . $property . '".'
                    );
                }
                break;
            case 'target':
                if (!\is_array($value)) {
                    $value = [$value];
                }
                $result = true;
                break;
            default:
                $result = true;
        }
        return $result;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function jsonCollapse() {
        if ($this->subject === 'script') {
            return $this;
        }
        if (\strpos($this->value, Manager::GROUP_DELIM) !== false) {
            return $this;
        }
        $result = implode(',', $this->target)
            . Manager::GROUP_DELIM . $this->subject
            . Manager::GROUP_DELIM
            . (\is_bool($this->value) ? (int) $this->value : $this->value);
        return $result;
    }

    public function setSubject($value)
    {
        if (!$this->configureValidate('subject', $value)) {
            throw new \UnexpectedValueException(
                'Valid values for subject are: ' . implode('|', self::$subjectValidation)
            );
        }
        $this->subject = $value;
        return $this;
    }

    public function setTarget($value)
    {
        if (!$this->configureValidate('target', $value)) {
            // @codeCoverageIgnoreStart
            throw new \UnexpectedValueException('Invalid value for target.');
            // @codeCoverageIgnoreEnd
        }
        $this->target = $value;
        return $this;
    }

    public function setValue($value)
    {
        if (!$this->configureValidate('value', $value)) {
            // @codeCoverageIgnoreStart
            throw new \UnexpectedValueException('Invalid value.');
            // @codeCoverageIgnoreEnd
        }
        $this->value = $value;
        return $this;
    }

}
