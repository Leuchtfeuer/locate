<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class AndCondition extends AbstractJudge
{
    /**
     * The judge decide if the case is true and therefore the configured action should be called
     *
     * @return Decision|false
     */
    public function process(array $facts, int $priority = 999): ?Decision
    {
        $matches = $this->configuration['matches'];
        $matches = GeneralUtility::trimexplode("\n", $matches);

        foreach ($matches as $value) {
            list($c1, $c2) = explode('=', $value);
            $c1 = trim($c1);
            $c2 = trim($c2);
            $f1 = isset($facts[$c1]) ? $facts[$c1] : $c1;
            $f2 = isset($facts[$c2]) ? $facts[$c2] : $c2;
            if ($f1 != $f2) {
                $this->logger->info("Condition $c1 = $c2 failed: $f1 != $f2");

                return false;
            }
            $this->logger->info("Condition $c1 = $c2 is true: $f1 = $f2");
        }

        /** @var Decision $decision */
        $decision = GeneralUtility::makeInstance(Decision::class);
        $decision->setActionName($this->configuration['action']);

        return $decision;
    }
}
