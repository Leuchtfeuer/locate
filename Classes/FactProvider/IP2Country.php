<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\FactProvider;

use Leuchtfeuer\Locate\Utility\LocateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IP2Country extends AbstractFactProvider
{
    const PROVIDER_NAME = 'countrybyip';

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
        $iso2 = GeneralUtility::makeInstance(LocateUtility::class)->getCountryIso2FromIP();
        LocateUtility::mainstreamValue($iso2);
        $this->facts[$this->getBasename()] = $iso2;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isGuilty($prosecution): bool
    {
        $prosecution = (string)$prosecution;
        LocateUtility::mainstreamValue($prosecution);

        return $this->facts[$this->getBasename()] === $prosecution;
    }
}
