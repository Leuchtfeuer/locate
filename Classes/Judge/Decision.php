<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

class Decision
{
    protected $actionName = '';

    /**
     * @var array
     */
    protected $specification = [];

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

    public function getSpecifications(): array
    {
        return $this->specification;
    }

    public function setSpecifications(array $specification)
    {
        $this->specification = $specification;
    }

    public function addSpecification(string $name, string $value)
    {
        $this->specification[$name] = $value;
    }
}
