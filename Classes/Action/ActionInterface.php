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
     * @param array $facts
     * @param Decision $decision
     */
    public function process(array &$facts, Decision &$decision);
}

