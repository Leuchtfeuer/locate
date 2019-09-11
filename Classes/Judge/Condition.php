<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

class Condition extends AbstractJudge
{
    /**
     * The judge decide if the case is true and therefore the configured action should be called
     */
    public function process(array $facts, int $priority = 999): ?Decision
    {
        $match = $this->getMatch();
        $decision = new Decision();
        $decision->setActionName($this->configuration['action']);

        if ($match !== null) {
            $match = preg_replace('/\s+/', '', $match);
            list($factIdentifier, $value) = explode('=', $match);

            if (!isset($facts[$factIdentifier]) || !(isset($facts[$factIdentifier][$value]) || $facts[$factIdentifier] == $value)) {
                return null;
            }

            if ($priority < $decision->getPriority()) {
                $decision->setPriority((int)$priority);
            }
        }

        return $decision;
    }

    protected function getMatch(): ?string
    {
        if (isset($this->configuration['matches'])) {
            trigger_error('Using matches property is deprecated. Use match instead.', E_USER_DEPRECATED);
            $this->configuration['match'] = trim($this->configuration['matches']);
        }

        return $this->configuration['match'] ?? null;
    }
}
