<?php

namespace Abivia\NextForm\Data;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Describes how a data object is displayed on a form.
 */
class Presentation implements \JsonSerializable
{
    use Configurable;
    use JsonEncoderTrait;

    /**
     * The number of columns to use when displaying an element.
     * @var int
     */
    protected $cols;

    /**
     * Set when user input should be entered twice to confirm correctness.
     * @var bool
     */
    protected $confirm = false;

    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'confirm' => ['drop:false'],
        'type' => [],
        'cols' => ['drop:null'],
        'rows' => ['drop:null'],
    ];

    /**
     * The reference for valid presentation types.
     * @var string[]
     */
    static protected $knownTypes = [
        'button', 'checkbox', 'color', 'date', 'datetime-local',
        'email', 'file', 'hidden', 'image', 'month', 'number',
        'password', 'radio', 'range', 'reset', 'search', 'select',
        'submit', 'tel', 'text', 'textarea', 'time', 'url', 'week',
    ];

    /**
     * The number of rows to use when displaying an element.
     * @var int
     */
    protected $rows;

    /**
     * Type indicates how the data should appear on a form.
     * @var string
     */
    protected $type;

    static public function build($type) : Presentation
    {
        $pres = new Presentation();
        $pres->setType($type);
        return $pres;
    }

    /**
     * Ensures that selected configuration values are valid (cols, confirm, rows, and type).
     * @param string $property
     * @param mixed $value
     * @return boolean
     */
    protected function configureValidate($property, &$value)
    {
        switch ($property) {
            case 'cols':
            case 'rows':
                if (!is_numeric($value) || ((int) $value) < 0) {
                    $this->configureLogError($property . ' must be a positive number.');
                    return false;
                }
                $value = (int) $value;
                break;
            case 'confirm':
                if (!is_bool($value)) {
                    $this->configureLogError($property . ' must be boolean.');
                    return false;
                }
                break;
            case 'type':
                if (!(in_array($value, self::$knownTypes))) {
                    $this->configureLogError(
                        'Invalid value "'. $value . '" for property "' . $property . '".'
                    );
                    return false;
                }
                break;
        }
        return true;
    }

    /**
     * Get the number of display columns.
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * Get the "confirmation required" flag.
     * @return bool
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * Get the number of display rows.
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Get the visual presentation type.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the number of display columns.
     * @param int $cols The number of columns.
     * @return $this
     * @throws \RuntimeException if the setting is not a positive integer.
     */
    public function setCols($cols)
    {
        $this->configureErrors = [];
        if (!$this->configureValidate('cols', $cols)) {
            throw new \RuntimeException(implode("\n", $this->configureErrors));
        }
        $this->cols = $cols;
        return $this;
    }

    /**
     * Set the "confirmation required" flag.
     * @param bool $confirm
     * @return $this
     * @throws \RuntimeException if the setting is not a positive integer.
     */
    public function setConfirm($confirm)
    {
        $this->configureErrors = [];
        if (!$this->configureValidate('confirm', $confirm)) {
            throw new \RuntimeException(implode("\n", $this->configureErrors));
        }
        $this->confirm = $confirm;
        return $this;
    }

    /**
     * Set the number of display rows.
     * @param int $rows The number of rows.
     * @return $this
     * @throws \RuntimeException if the setting is not a positive integer.
     */
    public function setRows($rows)
    {
        $this->configureErrors = [];
        if (!$this->configureValidate('rows', $rows)) {
            throw new \RuntimeException(implode("\n", $this->configureErrors));
        }
        $this->rows = $rows;
        return $this;
    }

    /**
     * Set the visual presentation type.
     * @param string $value
     * @return $this
     * @throws \RuntimeException If the type is not recognized.
     */
    public function setType($value)
    {
        if (!$this->configureValidate('type', $value)) {
            if (is_scalar($value)) {
                $msg = '"' . $value . '" is not a valid presentation type.';
            } else {
                $msg = 'non-scalar value passed to setType()';
            }
            throw new \RuntimeException($msg);
        }
        $this->type = $value;
        return $this;
    }

}
