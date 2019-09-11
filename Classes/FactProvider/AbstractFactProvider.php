<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

abstract class AbstractFactProvider implements FactProviderInterface
{
    /**
     * @var string
     */
    protected $baseName = '';

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param string $baseName The basename for the factsArray. This name comes from configuration.
     * @param array $configuration TypoScript configuration array for this fact provider
     */
    public function __construct(string $baseName, array $configuration)
    {
        $this->baseName = $baseName;
        $this->configuration = $configuration;
    }

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @throws Exception
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
