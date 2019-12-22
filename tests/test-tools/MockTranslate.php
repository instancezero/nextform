<?php

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * An (almost) null translator
 */
class MockTranslate implements Translator {

    public $append = ' (tslt)';
    public $prepend = '';

    public function get($key, array $replace = [], $locale = null) {
        $replace = $replace;
        $locale = $locale;
        return $this->prepend . $key . $this->append;
    }

    public function choice($key, $number, array $replace = [], $locale = null) {
        return $key;
    }

    public function getLocale() {
        return 'no-CA';
    }

    public function setLocale($locale) {
    }

}


