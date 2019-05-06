<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

use TYPO3\CMS\Core\Log\Logger;

/**
 * Class AbstractJudge
 */
abstract class AbstractJudge implements JudgeInterface
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
     * @var string
     */
    protected $baseName = '';

    /**
     * @param array $configuration TypoScript configuration array for this judge
     */
    public function __construct(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
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
