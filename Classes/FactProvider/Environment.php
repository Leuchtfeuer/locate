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

namespace Leuchtfeuer\Locate\FactProvider;

use Leuchtfeuer\Locate\Utility\LocateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Environment extends AbstractFactProvider
{
    const PROVIDER_NAME = 'env';

    /**
     * @inheritDoc
     */
    public function getBasename(): string
    {
        return self::PROVIDER_NAME;
    }

    /**
     * @inheritDoc
     */
    public function process(): self
    {
        foreach (GeneralUtility::getIndpEnv('_ARRAY') as $key => $value) {
            $this->facts[$this->getFactPropertyName($key)] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isGuilty($prosecution): bool
    {
        $subject = array_keys($prosecution);
        $subject = array_shift($subject);
        $value = $prosecution[$subject];
        LocateUtility::mainstreamValue($subject);

        return ($this->getSubject()[$subject] ?? false) == $value;
    }
}
