<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

class Decision
{
    /**
     * @var bool
     */
    protected $actionName = false;

    /**
     * @var array
     */
    protected $specification = [];

    public function hasAction(): bool
    {
        return $this->actionName ? true : false;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName)
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
