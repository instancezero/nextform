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
        $out = $key;
        foreach ($replace as $search => $value) {
            $out = preg_replace(
                '/:' . $search . '([^a-z0-9]|$)/i',
                $value . '$1',
                $out
            );
        }
        $locale = $locale;
        return self::$prepend . $out . self::$append;
    }

    public function choice($key, $number, array $replace = [], $locale = null)
    {
        $number = $number;
        return $this->get($key, $replace, $locale);
    }

    public function getLocale()
    {
        return 'no-CA';
    }

    public function setLocale($locale)
    {
        $locale = $locale;
    }

}


