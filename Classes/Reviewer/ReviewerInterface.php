<?php

namespace Bitmotion\Locate\Reviewer;


/**
 * Interface ReviewerInterface
 *
 * @package Bitmotion\Locate\Reviewer
 */
interface ReviewerInterface
{

    /**
     * @param string $baseName The basename for the factsArray. This name comes from configuration.
     * @param array $configArray TypoScript configuration array for this fact provider
     */
    public function __construct($baseName, $configArray);

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $factsArray
     */
    public function Process(&$factsArray);
}

