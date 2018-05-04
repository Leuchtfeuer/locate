<?php

namespace Bitmotion\Locate\Judge;

use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class AndCondition
 *
 * @package Bitmotion\Locate\Judge
 */
class AndCondition extends AbstractJudge
{

    /**
     * The judge decide if the case is true and therefore the configured action should be called
     *
     * @param array $facts
     * @return Decision|FALSE
     */
    public function process(array &$facts)
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
            } else {
                $this->logger->info("Condition $c1 = $c2 is true: $f1 = $f2");
            }
        }

        /** @var Decision $decision */
        $decision = GeneralUtility::makeInstance(Decision::class);
        $decision->setActionName($this->configuration['action']);
        return $decision;
    }

}

