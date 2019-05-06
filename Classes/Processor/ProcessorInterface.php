<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Processor;

/**
 * Interface ProcessorInterface
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
