<?php

namespace Abivia\NextForm\Access;

use Abivia\Configurable\Configurable;

/**
 * A simple role for the BasicAccess class.
 */
class Role
{
    use Configurable;

    /**
     * The name of this role.
     * @var string
     */
    protected $name;

    /**
     * A list of permissions associated with the role.
     * @var Permissions
     */
    protected $permissions;

    /**
     * Map a property to a class.
     */
    protected function configureClassMap($property, $value)
    {
        static $classMap = [
            'permissions' => ['className' => '\Abivia\NextForm\Access\Permissions'],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    /**
     * Get the role name.
     * @return string
     */
    public function getName() : string
    {
        return $this -> name;
    }

    /**
     * Get the permissions object for this role.
     * @return \Abivia\NextForm\Access\Permissions
     */
    public function getPermissions() : Permissions
    {
        return $this -> permissions;
    }

    /**
     * Determine if this role has a specific permission.
     * @param string $object Either an object name or an object.operation
     * @param string $operation Optional operation, required if $object doesn't have an
     * operation.
     * @return ?bool Boolean if the permission exists, null if it doesn't.
     */
    public function has($object, $operation = null) : ?bool
    {
        return $this -> permissions -> has($object, $operation);
    }

    /**
     * Set the role name
     * @param string $name The name for this role.
     * @return \self
     */
    public function setName($name) : self
    {
        $this -> name = $name;
        return $this;
    }

}
