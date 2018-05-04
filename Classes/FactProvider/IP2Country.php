<?php

namespace Bitmotion\Locate\FactProvider;

use Bitmotion\Locate\Tools\IP;


/**
 * Class IP2Country
 *
 * @package Bitmotion\Locate\FactProvider
 */
class IP2Country extends AbstractFactProvider
{


    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $facts
     */
    public function process(array &$facts)
    {
        $factPropertyName = $this->GetFactPropertyName('countryCode');
        $facts[$factPropertyName] = \Bitmotion\Locate\Tools\IP2Country::GetCountryIso2FromIP(IP::GetUserIpAsLong());

        $factPropertyName = $this->GetFactPropertyName('IP2Dezimal');
        $facts[$factPropertyName] = IP::GetUserIpAsLong();
    }

}
