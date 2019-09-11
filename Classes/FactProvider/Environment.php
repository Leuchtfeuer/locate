<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Environment extends AbstractFactProvider
{
    /**
     * Call the fact module which might add some data to the factArray
     */
    public function process(array &$facts)
    {
        /** @var array $envFactArray */
        $envFactArray = GeneralUtility::getIndpEnv('_ARRAY');

        foreach ($envFactArray as $key => $value) {
            $factPropertyName = $this->GetFactPropertyName($key);
            $facts[$factPropertyName] = $value;
        }

        foreach ($_SERVER as $key => $value) {
            $facts['SERVER_' . $key] = $value;
        }
    }
}
