<?php

namespace Bitmotion\Locate\Judge;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


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
     * @param array $factsArray
     * @return Decision|FALSE
     */
    public function Process(&$factsArray)
    {
        $matches = $this->configArray['matches'];
        $matches = GeneralUtility::trimexplode("\n", $matches);

        foreach ($matches as $value) {
            list($c1, $c2) = explode('=', $value);
            $c1 = trim($c1);
            $c2 = trim($c2);
            $f1 = isset($factsArray[$c1]) ? $factsArray[$c1] : $c1;
            $f2 = isset($factsArray[$c2]) ? $factsArray[$c2] : $c2;
            if ($f1 != $f2) {
                $this->Logger->Info("Condition $c1 = $c2 failed: $f1 != $f2");
                return false;
            } else {
                $this->Logger->Info("Condition $c1 = $c2 is true: $f1 = $f2");
            }
        }

        /** @var Decision $decision */
        $decision = GeneralUtility::makeInstance(Decision::class);
        $decision->setActionName($this->configArray['action']);
        return $decision;
    }

}

