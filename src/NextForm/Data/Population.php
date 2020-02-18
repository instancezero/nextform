<?php

namespace Abivia\NextForm\Data;

use Abivia\Configurable\Configurable;
use Abivia\NextForm\Data\Population\Option;
use Abivia\NextForm\Traits\JsonEncoderTrait;

/**
 * Describes the data source and values available for a field.
 */
class Population implements \JsonSerializable, \IteratorAggregate
{
    use Configurable;
    use JsonEncoderTrait;


    /**
     * Rules for the JsonEncoder
     * @var array
     */
    static protected $jsonEncodeMethod = [
        'source' => [],
        'parameters' => ['drop:empty','drop:null'],
        'query' => ['drop:blank','drop:null'],
        'translate' => ['drop:true'],
        'list' => [],
        'sidecar' => ['drop:null'],
    ];


    /**
     * A list of valid data sources.
     * @var array
     */
    static protected $knownSources = [
        'fixed', 'local', 'remote', 'static',
    ];

    /**
     * The list of available values.
     * @var Population\Option[]
     */
    protected $list;

    /**
     * Parameters associated with a query.
     * @var array
     */
    protected $parameters = [];

    /**
     * The query used to obtain values.
     * @var mixed
     */
    protected $query;
    /**
     * Arbitrary data associated with this field.
     * @var mixed
     */
    public $sidecar;

    /**
     * The type of data source.
     * @var string
     */
    protected $source;

    /**
     * Flag indicating if the data values are subject to translation (true=yes, translate).
     * @var bool
     */
    protected $translate = true;

    /**
     * Add an option to the population.
     *
     * @param Option $option
     * @return $this
     */
    public function addOption(Option $option)
    {
        $this->list[] = $option;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'list' => ['className' => '\Abivia\NextForm\Data\Population\Option'], //'key' => '', 'keyIsMethod' => true],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    /**
     * Ensures the setting for source is a known value.
     * @param string $property
     * @param mixed $value
     * @return boolean
     */
    protected function configureValidate($property, &$value)
    {
        switch ($property) {
            case 'source':
                $result = in_array($value, self::$knownSources);
                break;
            default:
                $result = true;
        }
        return $result;
    }

    /**
     * Get an iterator so we can loop through the list.
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->list);
    }

    /**
     * Get the current option list.
     * @return Population\Option[]
     */
    public function getList()
    {
        if ($this->list === null) {
            return [];
        }
        return $this->list;
    }

    /**
     * Get the query parameters.
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get the data list query.
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the sidecar data
     *
     * @return mixed
     */
    public function getSidecar()
    {
        return $this->sidecar;
    }

    /**
     * Get the data source type.
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get the translation status, true if text should be translated.
     * @return bool
     */
    public function getTranslate() : bool
    {
        return $this->translate;
    }

    /**
     * JsonEncoder support: Determine if this object is empty.
     * @return bool
     */
    public function isEmpty() : bool
    {
        if (!empty($this->list)) {
            return false;
        }
        if (!empty($this->parameters)) {
            return false;
        }
        if ($this->query !== null && $this->query !== '') {
            return false;
        }
        if ($this->sidecar !== null) {
            return false;
        }
        if ($this->source !== null && $this->source !== '') {
            return false;
        }
        if ($this->translate != true) {
            return false;
        }
        return true;
    }

    /**
     * Set the query parameters.
     *
     * @param array $params
     * @return $this
     */
    public function setParameters($params)
    {
        $this->parameters = $params;
        return $this;
    }

    /**
     * Set the query for getting a data list.
     * @param string $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Set the sidecar data.
     *
     * @param mixed $data
     * @return $this
     */
    public function setSidecar($data)
    {
        $this->sidecar = $data;
        return $this;
    }

    /**
     * Set the data source type.
     *
     * @param string $source
     * @return $this
     * @throws \LogicException
     */
    public function setSource($source)
    {
        if (!$this->configureValidate('source', $source)) {
            throw new \LogicException('Invalid value for source: ' . $source);
        }
        $this->source = $source;
        return $this;
    }

    /**
     * Set the translation status.
     *
     * @param bool $mustTranslate True if the text in the options list need to be translated.
     * @return $this
     */
    public function setTranslate(bool $mustTranslate)
    {
        $this->translate = $mustTranslate;
        return $this;
    }

}
