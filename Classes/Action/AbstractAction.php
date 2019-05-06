<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;
use TYPO3\CMS\Core\Log\Logger;

/**
 * Class AbstractAction
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
     * @param array $configuration TypoScript configuration array for this action
     */
    public function __construct(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * Call the action module
     *
     * @throws Exception
     */
    public function process(array &$facts, Decision &$decision)
    {
        throw new Exception('Process not implemented in ' . __CLASS__);
    }
}
