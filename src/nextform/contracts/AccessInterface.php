<?php

namespace Abivia\NextForm\Contracts;

/**
 * Interface for access control.
 * @codeCoverageIgnore
 */
interface AccessInterface
{
    /**
     * Determine if the a user has access to an object.
     * @param string $segment The segment that the requested object belongs to.
     * @param string $objectName The name of the object.
     * @param string $operation The operation we're asking permission for (read|write).
     * @param string $user Overrides the current user to get another user's access.
     * @return bool
     */
    public function hasAccess($segment, $objectName, $operation, $user = null) : bool;

    /**
     * Set a default user for subsequent access requests.
     * @param string $user The user identifier
     * @return \self
     */
    public function setUser($user);

}
