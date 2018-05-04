<?php

namespace Bitmotion\Locate\FactProvider;


/**
 * Class Constants
 *
 * @package Bitmotion\Locate\FactProvider
 */
class Constants extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $facts
     */
    public function process(array &$facts)
    {
        foreach ($this->configuration as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $SubValue) {
                    $facts[$key . $subKey] = $SubValue;
                }
            } else {
                $facts[$key] = $value;
            }
        }
    }


}
