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
    protected $actionName = '';

    protected $priority = AbstractJudge::DEFAULT_PRIORITY;

    protected $internalPriority = 0;

    public function hasAction(): bool
    {
        return !empty($this->actionName);
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName): void
    {
        $this->actionName = $actionName;
    }

    public function withActionName(string $actionName): self
    {
        $clonedObject = clone $this;
        $clonedObject->actionName = $actionName;

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
