<?php

namespace Abivia\NextForm\Access;

use Abivia\Configurable\Configurable;

/**
 * A simple user for the BasicAccess class.
 */
class User
{
    use Configurable;

    /**
     * The user identifier.
     * @var string
     */
    protected $id;

    /**
     * A list of roles this user belongs to.
     * @var string[]
     */
    protected $roles = [];

    /**
     * Add a role to this user.
     * @return string[]
     */
    public function addRole($name) : self
    {
        if (!in_array($name, $this -> roles)) {
            $this -> roles[] = $name;
        }
        return $this;
    }

    /**
     * Delete a role from this user.
     * @return string[]
     */
    public function deleteRole($name) : self
    {
        if (($key = array_search($name, $this -> roles))) {
            unset($this -> roles[$key]);
        }
        return $this;
    }

    /**
     * Get the user ID.
     * @return string
     */
    public function getId()
    {
        return $this -> id;
    }

    /**
     * Get the user's roles.
     * @return string[]
     */
    public function getRoles()
    {
        return $this -> roles;
    }

    /**
     * Set the user ID.
     * @param string $id The user identifier.
     * @return self
     */
    public function setId($id) : self
    {
        $this -> id = $id;
        return $this;
    }

}
