<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Utility;

use TYPO3\CMS\Core\Country\CountryProvider;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

readonly class CountryHelper
{
    public function __construct(private CountryProvider $countryProvider) {}

    /**
     * @param array<string, mixed> $params
     */
    public function selectItemsTCA(array &$params): void
    {
        if (is_array($params['items'])) {
            $countries = $this->countryProvider->getAll();

            foreach ($countries as $country) {
                $label = $country->getLocalizedNameLabel();
                $params['items'][] = [
                    'label' => LocalizationUtility::translate($label) ?? '',
                    'value' => $country->getAlpha2IsoCode(),
                ];
            }

            $languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)
                ->createFromUserPreferences($GLOBALS['BE_USER']);
            $backendUserLocale = $languageService->getLocale();
            $languageCode = 'en';
            if ($backendUserLocale instanceof Locale) {
                $languageCode = $backendUserLocale->getLanguageCode();
            }
            $collator = new \Collator($languageCode);
            usort($params['items'], function ($a, $b) use ($collator) {
                return $collator->compare($a['label'], $b['label']) ?: PHP_INT_MAX;
            });
        }
    }
}
