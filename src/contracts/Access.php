<?php

namespace Abivia\NextForm\Contracts;

/**
 * Interface for access control.
 * @codeCoverageIgnore
 */
interface Access {
    public function hasAccess($segment, $objectName, $operation) : bool;

    public function setUser($user);

}
