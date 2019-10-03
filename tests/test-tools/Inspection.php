<?php

/**
 *
 */
trait Inspection {

    protected function getProperty($instance, $property) {
        $reflector = new \ReflectionClass($instance);
        $reflectorProperty = $reflector->getProperty($property);
        $reflectorProperty->setAccessible(true);

        return $reflectorProperty->getValue($instance);
    }

}
