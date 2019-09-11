<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

class Decision
{
    protected $actionName = '';

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
}
