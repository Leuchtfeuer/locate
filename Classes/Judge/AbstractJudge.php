<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Judge;

use Leuchtfeuer\Locate\FactProvider\AbstractFactProvider;

abstract class AbstractJudge
{
    public const int DEFAULT_PRIORITY = 999;

    protected ?Decision $decision = null;

    /**
     * @param array<string, mixed> $configuration TypoScript configuration array for this judge
     */
    public function __construct(protected array $configuration = []) {}

    /**
     * @param array<string, mixed> $configuration TypoScript configuration array for this judge
     * @return $this
     */
    public function withConfiguration(array $configuration): self
    {
        $clonedObject = clone $this;
        $clonedObject->configuration = $configuration;

        return $clonedObject;
    }

    public function hasDecision(): bool
    {
        return $this->decision instanceof Decision;
    }

    public function getDecision(): ?Decision
    {
        return $this->decision;
    }

    /**
     * Call the fact module which might add some data to the factArray
     */
    abstract public function adjudicate(AbstractFactProvider $factProvider, int $priority = self::DEFAULT_PRIORITY): AbstractJudge;
}
