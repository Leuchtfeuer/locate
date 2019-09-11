<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

/**
 * @deprecated
 */
class Constants extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     * @deprecated
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
