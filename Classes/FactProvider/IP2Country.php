<?php
declare(strict_types = 1);
namespace Bitmotion\Locate\FactProvider;

/***
 *
 * This file is part of the "Locate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Florian Wessels <f.wessels@bitmotion.de>, Bitmotion GmbH
 *
 ***/

use Bitmotion\Locate\Utility\LocateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IP2Country extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts)
    {
        $locateUtility = GeneralUtility::makeInstance(LocateUtility::class);
        $ipAsLong = $locateUtility->getNumericIp();

        $factPropertyName = $this->getFactPropertyName('countryCode');
        $iso2 = $locateUtility->getCountryIso2FromIP();
        $facts[$factPropertyName][$iso2] = 1;

        $factPropertyName = $this->getFactPropertyName('IP2Dezimal');
        $facts[$factPropertyName][$ipAsLong] = 1;
    }
}
