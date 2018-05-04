<?php

namespace Bitmotion\Locate\Processor;


/**
 * Interface ProcessorInterface
 *
 * @package Bitmotion\Locate\Processor
 */
interface ProcessorInterface
{

    /**
     * @param array $configuration TypoScript config array
     */
    public function __construct(array $configuration);

    /**
     * Processes the configuration
     */
    public function run();
}

