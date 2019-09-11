<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class IP2Country extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts)
    {
        $ipAsLong = (int)sprintf('%u', ip2long(GeneralUtility::getIndpEnv('REMOTE_ADDR')));
        $factPropertyName = $this->GetFactPropertyName('countryCode');
        $facts[$factPropertyName] = \Bitmotion\Locate\Tools\IP2Country::GetCountryIso2FromIP($ipAsLong);

        $factPropertyName = $this->GetFactPropertyName('IP2Dezimal');
        $facts[$factPropertyName] = $ipAsLong;
    }
}
