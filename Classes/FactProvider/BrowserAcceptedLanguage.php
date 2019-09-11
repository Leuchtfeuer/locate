<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

class BrowserAcceptedLanguage extends AbstractFactProvider
{
    protected $locales = [];

    protected $languages = [];

    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts): void
    {
        $this->getAcceptedLanguages();
        $facts = [];

        foreach ($this->locales as $priority => $locale) {
            $facts[$this->getFactPropertyName('locale')][$locale] = $priority;
        }

        foreach ($this->languages as $priority => $language) {
            $facts[$this->getFactPropertyName('lang')][$language] = $priority;
        }
    }

    protected function getAcceptedLanguages(): void
    {
        $httpAcceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        preg_match_all('/([a-z]{2})(?:-[a-zA-Z]{2})?/', $httpAcceptLanguage, $matches);

        list($locales, $languages) = $matches;

        array_walk($locales, function (&$locale) {
            $locale = str_replace('-', '_', $locale);
        });

        $this->locales = $locales;
        $this->languages = array_unique($languages);
    }
}
