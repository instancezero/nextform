<?php
namespace Abivia\NextForm\Access;

use Abivia\NextForm\Contracts\Access as AccessContract;

/**
 * This the default null access provider.
 */
class NullAccess implements AccessContract {

    /**
     * Null access provider: grants access to everything.
     * @param string $segment The segment that the requested object belongs to.
     * @param string $objectName The name of the object.
     * @param string $operation The operation we're asking permission for (read|write).
     * @return bool True if access is granted. Always true.
     */
    public function hasAccess($segment, $objectName, $operation) : bool {
        return true;
    }

    public function setUser($user) {

    }

}
