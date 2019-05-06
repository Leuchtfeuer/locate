<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

/**
 * Class BrowserAcceptedLanguage
 */
class BrowserAcceptedLanguage extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts)
    {
        $languages = $this->getAcceptedLanguages();

        $factPropertyName = $this->getFactPropertyName('lang');
        $facts[$factPropertyName] = $languages[0];

        $factPropertyName = $this->getFactPropertyName('lang1');
        $facts[$factPropertyName] = $languages[1];

        $factPropertyName = $this->getFactPropertyName('lang2');
        $facts[$factPropertyName] = $languages[2];

        $locales = $this->getAcceptedLocales();

        $factPropertyName = $this->getFactPropertyName('locale');
        $facts[$factPropertyName] = $locales[0];

        $factPropertyName = $this->getFactPropertyName('locale1');
        $facts[$factPropertyName] = $locales[1];

        $factPropertyName = $this->getFactPropertyName('locale2');
        $facts[$factPropertyName] = $locales[2];
    }

    protected function getAcceptedLanguages(): array
    {
        $httpAcceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        preg_match_all('/([a-z]{2})(?:-[a-zA-Z]{2})?/', $httpAcceptLanguage, $matches);

        $pref_lang = [];
        foreach ($matches[1] as $lang) {
            if (!in_array($lang, $pref_lang)) {
                $pref_lang [] = $lang;
            }
        }

        return $pref_lang;
    }

    protected function getAcceptedLocales(): array
    {
        $httpAcceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        preg_match_all('/([a-z]{2})(?:-[a-zA-Z]{2})?/', $httpAcceptLanguage, $matches);

        //TODO is the quality property needed? The browser seem to send it sorted anyway
        $pref_locale = [];
        foreach ($matches[0] as $locale) {
            list($a, $b) = explode('-', $locale);
            if ($b) {
                $locale = $a . '_' . strtoupper($b);
            }
            $pref_locale[] = $locale;
        }

        return $pref_locale;
    }
}
