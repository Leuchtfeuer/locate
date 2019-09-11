<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractJudge implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var string
     */
    protected $baseName = '';

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
     * @throws Exception
     * @return bool
     */
    public function process(array &$facts)
    {
        throw new Exception('Process not implemented in ' . __CLASS__);
    }

    /**
     * Adds a prefix to the factArray property name
     */
    protected function getFactPropertyName(string $property): string
    {
        return $this->baseName . '.' . $property;
    }
}
