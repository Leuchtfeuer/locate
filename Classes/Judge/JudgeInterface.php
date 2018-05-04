<?php

namespace Bitmotion\Locate\Judge;


/**
 * Interface JudgeInterface
 *
 * @package Bitmotion\Locate\Judge
 */
interface JudgeInterface
{

    /**
     *
     * @param array $configArray TypoScript configuration array for this judge
     * @param \Bitmotion\Locate\Log\Logger $logger
     */
    public function __construct($configArray, $logger);

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $factsArray
     * @return Decision|FALSE
     */
    public function Process(&$factsArray);
}

