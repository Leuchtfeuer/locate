<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\FactProvider;

class StaticFactProvider extends AbstractFactProvider
{
    const PROVIDER_NAME = 'static';

    public function getBasename(): string
    {
        return self::PROVIDER_NAME;
    }

    public function process(): self
    {
        return $this;
    }

    public function isGuilty($prosecution): bool
    {
        return true;
    }
}
