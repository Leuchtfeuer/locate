<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Bitmotion\Locate\Judge;

use Bitmotion\Locate\Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractJudge implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $configuration = [];

    /**
     * @param array $configuration TypoScript configuration array for this judge
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Call the fact module which might add some data to the factArray
     *
     * @throws \Bitmotion\Locate\Exception
     */
    public function process(array $facts, int $priority = 999): ?Decision
    {
        throw new Exception(sprintf('Process not implemented in %s.', __CLASS__));
    }
}
