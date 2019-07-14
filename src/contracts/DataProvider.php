<?php

/**
 * A DataProvider services AJAX requests for form population.
 */
interface DataProvider {
    public function get($segment, $key, ...$where);

    public function put($segment, $key, $collection);

}
