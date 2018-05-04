<?php

namespace Bitmotion\Locate\Judge;


/**
 * Class Fixed
 *
 * @package Bitmotion\Locate\Judge
 */
class Fixed extends AbstractJudge
{

    /**
     * The judge decide if the case is true and therefore the configured action should be called
     *
     * @param array $facts
     * @return Decision
     */
    public function process(array &$facts): Decision
    {
        $decision = new Decision();
        $decision->setActionName($this->configuration['action']);

        return $decision;
    }

}

