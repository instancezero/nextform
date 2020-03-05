<?php

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * An (almost) null translator
 */
class MockTranslate implements Translator {

    static public $append = ' (tslt)';
    static public $instanceCount = 0;
    static public $prepend = '';

    public function __construct()
    {
        ++self::$instanceCount;
    }

    public function get($key, array $replace = [], $locale = null)
    {
        $replace = $replace;
        $locale = $locale;
        return self::$prepend . $key . self::$append;
    }

    public function choice($key, $number, array $replace = [], $locale = null)
    {
        return $key;
    }

    public function getLocale()
    {
        return 'no-CA';
    }

    public function setLocale($locale)
    {
    }

}


