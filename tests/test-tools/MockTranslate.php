<?php

use Illuminate\Contracts\Translation\Translator as Translator;

/**
 * An (almost) null translator
 */
class MockTranslate implements Translator {

    public function trans($key, array $replace = [], $locale = null) {
        $replace = $replace;
        $locale = $locale;
        return $key . ' (tslt)';
    }

    public function transChoice($key, $number, array $replace = [], $locale = null) {
        return $key;
    }

    public function getLocale() {
        return 'no-CA';
    }

    public function setLocale($locale) {
    }

}


