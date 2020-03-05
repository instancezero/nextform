<?php

/**
 * Skeleton for a mock class that logs all entry points.
 */
class MockBase
{
    static protected $_MockBase_log = [];

    public function __call($name, $args)
    {
        self::_MockBase_log($name, $args);
        return null;
    }

    static public function __callStatic($name, $args)
    {
        self::_MockBase_log('::' . $name, $args);
        return null;
    }

    public function __set(string $name, mixed $value) : void
    {
        self::_MockBase_log('set.' . $name, $args);
    }

    public function __get(string $name) : mixed
    {
        self::_MockBase_log('set.' . $name, $args);
        return null;
    }

    public function __isset(string $name) : bool
    {
        self::_MockBase_log('isset' . $name, $args);
        return false;
    }

    public function __unset( string $name) : void
    {
        self::_MockBase_log('unset.' . $name, $args);
    }

    /**
     * Gets everything logged since the last _MockBase_reset()
     * @return array
     */
    static public function _MockBase_getLog()
    {
        if (static::class === 'MockBase') {
            return self::$_MockBase_log;
        }
        return self::$_MockBase_log[static::class] ?? null;
    }

    /**
     * Write a log for this class
     */
    static public function _MockBase_log($name, $args = [])
    {
        if (static::class !== 'MockBase') {
            if (!isset(self::$_MockBase_log[static::class])) {
                self::$_MockBase_log[static::class] = [];
            }
            self::$_MockBase_log[static::class][] = [$name, $args];
        }
    }

    /**
     * Clear the internal log
     */
    static public function _MockBase_reset()
    {
        if (static::class === 'MockBase') {
            self::$_MockBase_log = [];
        } else {
            self::$_MockBase_log[static::class] = [];
        }
    }

}


