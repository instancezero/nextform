<?php

namespace Abivia\NextForm\Data;

/**
 * Text labels associated with a data object.
 */
class Labels implements \JsonSerializable{
    use \Abivia\Configurable\Configurable;
    use \Abivia\NextForm\JsonEncoder;

    public $after = null;
    public $before = null;
    public $error = null;
    public $heading = null;
    public $help = null;
    static protected $jsonEncodeMethod = [
        'after' => ['drop:null'],
        'before' => ['drop:null'],
        'error' => ['drop:null'],
        'heading' => ['drop:null'],
        'help' => ['drop:null'],
        'placeholder' => ['drop:null'],
    ];
    public $placeholder = null;

    /**
     * Merge another label set into this one and return a new merged object.
     * @param \Abivia\NextForm\Data\Labels $merge
     * @return \Abivia\NextForm\Data\Labels
     */
    public function &merge(Labels $merge) {
        $newLabels = clone $this;
        foreach (self::$jsonEncodeMethod as $prop) {
            if ($merge -> $prop !== null) {
                $newLabels -> $prop = $merge -> $prop;
            }
        }
        return $newLabels;
    }

}
