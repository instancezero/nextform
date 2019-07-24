<?php

namespace Abivia\NextForm\Traits;

/**
 * Make an empty label structure collapse into null.
 */
trait JsonLabelFolder {

    /**
     * Fold an empty labels structure into a null value when converting to JSON.
     * @param type $labels
     * @return type
     */
    protected function jsonLabelFold($labels) {
        if ($labels !== null && $labels -> isEmpty()) {
            $labels = null;
        }
        return $labels;
    }

}
