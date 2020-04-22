<?php
declare(strict_types = 1);
namespace Bitmotion\Locate\FactProvider;

/***
 *
 * This file is part of the "Locate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 *
 ***/

use Bitmotion\Locate\Exception;

abstract class AbstractFactProvider
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
