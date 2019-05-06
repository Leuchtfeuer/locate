<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Reviewer;

/**
 * Interface ReviewerInterface
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
     */
    public function process(array &$facts);
}
