<?php

namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;
use TYPO3\CMS\Core\Log\Logger;


/**
 * Interface ActionInterface
 *
 * @package Bitmotion\Locate\Action
 */
interface ActionInterface
{

    /**
     * @param array $configuration TypoScript configuration array for this action
     * @param Logger $logger
     */
    public function __construct(array $configuration, Logger $logger);

    /**
     * Call the action module
     *
     * @param array $factsArray
     * @param \Bitmotion\Locate\Judge\Decision
     */
    public function Process(&$factsArray, &$decision);
}

