<?php

namespace Bitmotion\Locate\FactProvider;

/**
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package Locate
 * @subpackage FactProvider
 */
class BrowserAcceptedLanguage extends AbstractFactProvider
{

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $factsArray
     */
    public function Process(&$factsArray)
    {

        $languages = $this->getAcceptedLanguages();

        $factPropertyName = $this->GetFactPropertyName('lang');
        $factsArray[$factPropertyName] = $languages[0];

        $factPropertyName = $this->GetFactPropertyName('lang1');
        $factsArray[$factPropertyName] = $languages[1];

        $factPropertyName = $this->GetFactPropertyName('lang2');
        $factsArray[$factPropertyName] = $languages[2];


        $locales = $this->getAcceptedLocales();

        $factPropertyName = $this->GetFactPropertyName('locale');
        $factsArray[$factPropertyName] = $locales[0];

        $factPropertyName = $this->GetFactPropertyName('locale1');
        $factsArray[$factPropertyName] = $locales[1];

        $factPropertyName = $this->GetFactPropertyName('locale2');
        $factsArray[$factPropertyName] = $locales[2];
    }

    protected function getAcceptedLanguages()
    {
        $m = [];
        $http_accept_language = isset ($_SERVER ['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER ['HTTP_ACCEPT_LANGUAGE'] : "";
        preg_match_all('/([a-z]{2})(?:-[a-zA-Z]{2})?/', $http_accept_language, $m);

        $pref_lang = [];
        foreach ($m [1] as $lang) {
            if (!in_array($lang, $pref_lang)) {
                $pref_lang [] = $lang;
            }
        }
        return $pref_lang;
    }

    protected function getAcceptedLocales()
    {
        $m = [];
        $http_accept_language = isset ($_SERVER ['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER ['HTTP_ACCEPT_LANGUAGE'] : "";
        preg_match_all('/([a-z]{2})(?:-[a-zA-Z]{2})?/', $http_accept_language, $m);

        #TODO is the quality property needed? The browser seem to send it sorted anyway
        $pref_locale = [];
        foreach ($m[0] as $locale) {
            list($a, $b) = explode('-', $locale);
            if ($b) {
                $locale = $a . '_' . strtoupper($b);
            }
            $pref_locale[] = $locale;
        }
        return $pref_locale;
    }


}
