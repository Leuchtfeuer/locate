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

namespace Bitmotion\Locate\FactProvider;

use Bitmotion\Locate\Utility\LocateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IP2Country extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts)
    {
        $simulateIp = $this->configuration['simulateIp'] ?? null;

        $locateUtility = GeneralUtility::makeInstance(LocateUtility::class);
        $ipAsLong = $locateUtility->getNumericIp($simulateIp);

        $factPropertyName = $this->getFactPropertyName('countryCode');
        $iso2 = $locateUtility->getCountryIso2FromIP($simulateIp);
        $facts[$factPropertyName][$iso2] = 1;

        $factPropertyName = $this->getFactPropertyName('IP2Dezimal');
        $facts[$factPropertyName][$ipAsLong] = 1;
    }
}
