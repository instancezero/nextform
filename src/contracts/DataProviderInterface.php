<?php

/**
 * A DataProvider services AJAX requests for form population.
 * @codeCoverageIgnore
 */
interface DataProviderInterface
{
    public function get($segment, $key, ...$where);

    public function put($segment, $key, $collection);

}
