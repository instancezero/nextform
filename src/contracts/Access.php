<?php

namespace Abivia\NextForm\Contracts;

/**
 *
 */
interface Access {
    public function hasAccess($segment, $objectName, $operation) : bool;

    public function setUser($user);

}
