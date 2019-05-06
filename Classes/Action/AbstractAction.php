<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class AbstractAction
 */
abstract class AbstractAction implements ActionInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param array $configuration TypoScript configuration array for this action
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
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
