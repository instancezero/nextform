<?php

namespace Abivia\NextForm\Access;

/**
 * A simple user for the BasicAccess class.
 */
class User {
    use \Abivia\Configurable\Configurable;

    protected $id;
    protected $roles;

    public function getId() {
        return $this -> id;
    }

    public function getRoles() {
        return $this -> roles;
    }

}
