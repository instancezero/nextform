<?php
namespace Abivia\NextForm\Access;

use Abivia\NextForm\Contracts\AccessInterface;

/**
 * This the default null access provider.
 */
class NullAccess implements AccessInterface
{

    /**
     * Null access provider: grants access to everything.
     * @param string $segment The segment that the requested object belongs to.
     * @param string $objectName The name of the object.
     * @param string $operation The operation we're asking permission for (read|write).
     * @return bool True if access is granted. Always true.
     */
    public function allows($segment, $objectName, $operation, $user = null) : bool
    {
        return true;
    }

    /**
     * Provide a set user method.
     * @param mixed $user
     * @return $this
     */
    public function setUser($user)
    {
        return $this;
    }

}
