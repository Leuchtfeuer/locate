<?php

namespace Bitmotion\Locate\Judge;


/**
 * Class Decision
 *
 * @package Bitmotion\Locate\Judge
 */
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

    /**
     * @return boolean
     */
    public function hasAction(): bool
    {
        return ($this->actionName ? true : false);
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @param string $actionName
     */
    public function setActionName(string $actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * @return array
     */
    public function getSpecifications(): array
    {
        return $this->specification;
    }

    /**
     * @param array $specification
     */
    public function setSpecifications(array $specification)
    {
        $this->specification = $specification;
    }

    /**
     *
     * @param string $name
     * @param string $value
     */
    public function addSpecification(string $name, string $value)
    {
        $this->specification[$name] = $value;
    }

}

