<?php
declare(strict_types = 1);
namespace Bitmotion\Locate\Processor;

/***
 *
 * This file is part of the "Locate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Florian Wessels <f.wessels@bitmotion.de>, Bitmotion GmbH
 *
 ***/

/**
 * Interface ProcessorInterface
 */
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
