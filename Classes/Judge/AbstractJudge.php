<?php

namespace Bitmotion\Locate\Judge;

use TYPO3\CMS\Core\Log\Logger;


/**
 * Class AbstractJudge
 *
 * @package Bitmotion\Locate\Judge
 */
abstract class AbstractJudge implements JudgeInterface
{

    /**
     * @var array
     */
    protected $configArray;

    /**
     *
     * @var Logger
     */
    protected $logger = null;

    /**
     * @var string
     */
    protected $baseName = '';

    /**
     *
     * @param array $configuration TypoScript configuration array for this judge
     * @param Logger $logger
     */
    public function __construct(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $factsArray
     * @throws Exception
     */
    public function Process(&$factsArray)
    {
        throw new Exception('Process not implemented in ' . __CLASS__);
    }

    /**
     * Adds a prefix to the factArray property name
     *
     * @param string $property
     * @return string
     */
    protected function GetFactPropertyName($property)
    {
        return $this->baseName . '.' . $property;
    }
}

