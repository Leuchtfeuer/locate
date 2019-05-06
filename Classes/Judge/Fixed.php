<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

/**
 * Class Fixed
 */
class Fixed extends AbstractJudge
{
    /**
     * The judge decide if the case is true and therefore the configured action should be called
     */
    public function process(array &$facts): Decision
    {
        $decision = new Decision();
        $decision->setActionName($this->configuration['action']);

        return $decision;
    }
}
