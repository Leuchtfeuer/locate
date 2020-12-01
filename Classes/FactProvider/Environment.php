<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

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
            $factPropertyName = $this->getFactPropertyName($key);
            $facts[$factPropertyName] = $value;
        }
    }
}
