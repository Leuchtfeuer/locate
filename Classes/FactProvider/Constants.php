<?php

namespace Bitmotion\Locate\FactProvider;


/**
 * Constants
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage FactProvider
 */
class Constants extends AbstractFactProvider
{


    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $factsArray
     */
    public function Process(&$factsArray)
    {
        foreach ($this->configArray as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $SubValue) {
                    $factsArray[$key . $subKey] = $SubValue;
                }
            } else {
                $factsArray[$key] = $value;
            }
        }
    }


}
