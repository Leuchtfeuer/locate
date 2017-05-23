<?php

namespace Bitmotion\Locate\FactProvider;

use Bitmotion\Locate\Tools\IP;


/**
 * IP to Country
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage FactProvider
 */
class IP2Country extends AbstractFactProvider
{


    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $factsArray
     */
    public function Process(&$factsArray)
    {
        $factPropertyName = $this->GetFactPropertyName('countryCode');
        $factsArray[$factPropertyName] = \Bitmotion\Locate\Tools\IP2Country::GetCountryIso2FromIP(IP::GetUserIpAsLong());

        $factPropertyName = $this->GetFactPropertyName('IP2Dezimal');
        $factsArray[$factPropertyName] = IP::GetUserIpAsLong();
    }

}
