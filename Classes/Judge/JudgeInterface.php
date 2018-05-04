<?php

namespace Bitmotion\Locate\Judge;

use TYPO3\CMS\Core\Log\Logger;


/**
 * Interface JudgeInterface
 *
 * @package Bitmotion\Locate\Judge
 */
interface JudgeInterface
{
    /**
     *
     * @param array $configuration TypoScript configuration array for this judge
     * @param Logger $logger
     */
    public function __construct(array $configuration, Logger $logger);

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $facts
     * @return Decision|FALSE
     */
    public function process(array &$facts);
}

