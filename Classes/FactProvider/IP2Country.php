<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

use Bitmotion\Locate\Tools\IP;

class IP2Country extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts)
    {
        $factPropertyName = $this->GetFactPropertyName('countryCode');
        $facts[$factPropertyName] = \Bitmotion\Locate\Tools\IP2Country::GetCountryIso2FromIP(IP::GetUserIpAsLong());

        $factPropertyName = $this->GetFactPropertyName('IP2Dezimal');
        $facts[$factPropertyName] = IP::GetUserIpAsLong();
    }
}
