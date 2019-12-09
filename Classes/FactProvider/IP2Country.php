<?php
declare(strict_types=1);
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class IP2Country extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts)
    {
        $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        $ipAsLong = $this->getNumericIp($ip);

        $factPropertyName = $this->getFactPropertyName('countryCode');
        $iso2 = strtolower(\Bitmotion\Locate\Tools\IP2Country::getCountryIso2FromIP($ipAsLong));
        $facts[$factPropertyName][$iso2] = 1;

        $factPropertyName = $this->getFactPropertyName('IP2Dezimal');
        $facts[$factPropertyName][$ipAsLong] = 1;
    }

    protected function getNumericIp($ip): int
    {
        $binNum = '';

        foreach (unpack('C*', inet_pton($ip)) as $byte) {
            $binNum .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }

        return (int)base_convert(ltrim($binNum, '0'), 2, 10);
    }
}
