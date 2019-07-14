<?php

namespace Abivia\NextForm\Access;

/**
 * A simple role for the BasicAccess class.
 */
class Role {
    use \Abivia\Configurable\Configurable;

    protected $name;
    protected $permissions;

    /**
     * Map a property to a class.
     */
    protected function configureClassMap($property, $value) {
        static $classMap = [
            'permissions' => ['className' => '\Abivia\NextForm\Access\Permissions'],
        ];
        if (isset($classMap[$property])) {
            return (object) $classMap[$property];
        }
        return false;
    }

    public function getName() {
        return $this -> name;
    }

    public function has($object, $operation = null) {
        return $this -> permissions -> has($object, $operation);
    }

    public function setName($name) {
        $this -> name = $name;
        return $this;
    }

}
