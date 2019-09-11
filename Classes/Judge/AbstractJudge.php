<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

use Bitmotion\Locate\Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractJudge implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $configuration = [];

    /**
     * @param array $configuration TypoScript configuration array for this judge
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @throws \Bitmotion\Locate\Exception
     */
    public function process(array &$facts)
    {
        throw new Exception(sprintf('Process not implemented in %s.', __CLASS__));
    }
}
