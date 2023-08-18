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

namespace Leuchtfeuer\Locate\Judge;

class Decision
{
    protected string $verdictName = '';

    protected int $priority = AbstractJudge::DEFAULT_PRIORITY;

    protected int $internalPriority = 0;

    public function hasVerdict(): bool
    {
        return !empty($this->verdictName);
    }

    public function getVerdictName(): string
    {
        return $this->verdictName;
    }

    public function setVerdictName(string $verdictName): void
    {
        $this->verdictName = $verdictName;
    }

    public function withVerdictName(string $verdictName): self
    {
        $clonedObject = clone $this;
        $clonedObject->verdictName = $verdictName;

        return $clonedObject;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getInternalPriority(): int
    {
        return $this->internalPriority;
    }

    public function setInternalPriority(int $internalPriority): void
    {
        $this->internalPriority = $internalPriority;
    }
}
