<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

class Fixed extends AbstractJudge
{
    /**
     * The judge decide if the case is true and therefore the configured action should be called
     */
    public function process(array $facts, int $priority = 999): ?Decision
    {
        $decision = new Decision();
        $decision->setActionName($this->configuration['action']);

        if (isset($this->configuration['priority'])) {
            $decision->setPriority((int)$this->configuration['priority']);
        }

        return $decision;
    }
}
