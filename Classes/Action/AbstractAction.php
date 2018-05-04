<?php

namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;
use TYPO3\CMS\Core\Log\Logger;


/**
 * Class AbstractAction
 *
 * @package Bitmotion\Locate\Action
 */
abstract class AbstractAction implements ActionInterface
{

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var Logger
     */
    protected $logger = null;


    /**
     *
     * @param array $configuration TypoScript configuration array for this action
     * @param Logger $logger
     */
    public function __construct(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * Call the action module
     *
     * @param array $facts
     * @param Decision $decision
     * @throws Exception
     */
    public function process(array &$facts, Decision &$decision)
    {
        throw new Exception('Process not implemented in ' . __CLASS__);
    }

}

