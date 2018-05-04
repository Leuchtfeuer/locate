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
     * @param array $configArray TypoScript config array
     */
    public function __construct(array $configArray);

    /**
     * Processes the configuration
     */
    public function run();
}

