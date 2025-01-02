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

namespace Leuchtfeuer\Locate\FactProvider;

use Leuchtfeuer\Locate\Utility\LocateUtility;
use Leuchtfeuer\Locate\Utility\TypeCaster;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BrowserAcceptedLanguage extends AbstractFactProvider
{
    public const string PROVIDER_NAME = 'browseracceptlanguage';

    protected bool $multiple = true;

    /**
     * @inheritDoc
     */
    public function getBasename(): string
    {
        return self::PROVIDER_NAME;
    }

    /**
     * @inheritDoc
     */
    public function process(): self
    {
        foreach ($this->getAcceptedLanguages() as $priority => $language) {
            $this->facts[$language] = $priority;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isGuilty(string $prosecution): bool
    {
        LocateUtility::mainstreamValue($prosecution);
        $this->priority = TypeCaster::toInt($this->facts[$prosecution] ?? 0);

        return isset($this->facts[$prosecution]);
    }

    /**
     * @return array<int, string>
     */
    protected function getAcceptedLanguages(): array
    {
        preg_match_all('/([a-z]{2})(?:-[a-zA-Z]{2})?/', GeneralUtility::getIndpEnv('HTTP_ACCEPT_LANGUAGE'), $matches);
        [$locales, $languages] = $matches;

        // ensure that all language codes are present in locales array
        $languages = array_merge($locales, $languages);
        $languages = array_unique($languages);
        $languages = array_values($languages);

        array_walk($languages, [LocateUtility::class, 'mainstreamValue']);

        return $languages;
    }
}
