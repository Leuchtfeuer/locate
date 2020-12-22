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

namespace Leuchtfeuer\Locate\Processor;

interface ProcessorInterface
{
    /**
     * @param array $configuration TypoScript config array
     */
    public function __construct(array $configuration);

    /**
     * Processes the configuration
     */
    public function run();
}
