<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Judge;

use Leuchtfeuer\Locate\FactProvider\AbstractFactProvider;
use Leuchtfeuer\Locate\Utility\TypeCaster;

class Fixed extends AbstractJudge
{
    /**
     * @inheritDoc
     */
    public function adjudicate(?AbstractFactProvider $factProvider, int $priority = AbstractJudge::DEFAULT_PRIORITY): AbstractJudge
    {
        $this->decision = new Decision();
        $this->decision->setVerdictName(TypeCaster::toString($this->configuration['verdict']));
        $this->decision->setPriority($priority);

        if (isset($this->configuration['priority'])) {
            $this->decision->setPriority(TypeCaster::toInt($this->configuration['priority']));
        }

        return $this;
    }
}
