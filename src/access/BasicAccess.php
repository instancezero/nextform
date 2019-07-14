<?php
namespace Abivia\NextForm\Access;

use Abivia\NextForm;

use Abivia\NextForm\Contracts\Access as AccessContract;

/**
 * This a very skeletal access provider driven by a simple configuration file.
 */
class BasicAccess implements AccessContract {
    use \Abivia\Configurable\Configurable;

    protected $currentUser = null;
    protected $roles;
    protected $users;

    /**
     * Map a property to a class.
     */
    protected function configureClassMap($property, $value) {
        static $classMap = [
            'roles' => ['className' => '\Abivia\NextForm\Access\Role', 'key' => 'getName', 'keyIsMethod' => true],
            'users' => ['className' => '\Abivia\NextForm\Access\User', 'key' => 'getId', 'keyIsMethod' => true],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check if the property can be loaded from configuration.
     * @param string $property
     * @return boolean true if the property is allowed.
     */
    protected function configurePropertyAllow($property) {
        return in_array($property, ['roles', 'users']);
    }

    /**
     * Determine if the current user has access to an object.
     * @param string $segment The segment that the requested object belongs to.
     * @param string $objectName The name of the object.
     * @param string $operation The operation we're asking permission for (read|write).
     * @param mixed $user User to query, uses current user if null
     * @return bool True if access is granted.
     */
    public function hasAccess($segment, $objectName, $operation, $user = null) : bool {
        if ($user === null) {
            $user = $this -> currentUser;
        }
        if ($user === null || !isset($this -> users[$user])) {
            throw new \LogicException('No valid user has been selected.');
        }
        $composite = $segment . NextForm::SEGMENT_DELIM . $objectName;
        $segAccess = null;
        $objAccess = null;
        // Check the roles for the current user
        foreach ($this -> users[$user] -> getRoles() as $role) {
            if (isset($this -> roles[$role])) {
                $has = $this -> roles[$role] -> has($segment, $operation);
                if ($has !== null) {
                    $segAccess = $has;
                }
                if ($segAccess === false) {
                    break;
                }
                $has = $this -> roles[$role] -> has($composite, $operation);
                if ($has !== null) {
                    $objAccess = $has;
                }
                if ($objAccess === false) {
                    break;
                }
            }
        }
        if ($objAccess !== null) {
            $access = $objAccess;
        } else {
            $access = $segAccess === true;
        }
        return $access;
    }

    public function setUser($user) {
        if ($user === null || isset($this -> users[$user])) {
            $this -> currentUser = $user;
            return $this;
        }
        $this -> currentUser = null;
        throw new \LogicException('User not found.');
    }
}
