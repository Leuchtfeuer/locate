<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

/***
 *
 * This file is part of the "Locate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Florian Wessels <f.wessels@bitmotion.de>, Bitmotion GmbH
 *
 ***/

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
