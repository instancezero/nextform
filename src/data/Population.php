<?php

namespace Abivia\NextForm\Data;

/**
 * Describes how a data object is displayed on a form.
 */
class Population {
    use \Abivia\Configurable\Configurable;

    static protected $knownSources = [
        'fixed', 'local', 'remote', 'static',
    ];
    protected $list;
    protected $parameters;
    protected $query;
    protected $source;

    protected function configureValidate($property, &$value) {
        switch ($property) {
            case 'source':
                $result = in_array($value, self::$knownSources);
                break;
            default:
                $result = true;
        }
        return $result;
    }

    public function getList() {
        return $this -> list;
    }

    public function getQuery() {
        return $this -> query;
    }

    public function getSource() {
        return $this -> source;
    }

    public function setSource($source) {
        if (!$this -> configureValidate('source', $source)) {
            throw new \LogicException('Invalid value for source: ' . $source);
        }
        return $this;
    }

}
