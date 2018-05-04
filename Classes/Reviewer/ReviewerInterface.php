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
     * @param array $configuration TypoScript configuration array for this fact provider
     */
    public function __construct(string $baseName, array $configuration);

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @param array $facts
     */
    public function process(array &$facts);
}

