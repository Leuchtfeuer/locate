<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;

class Dummy extends AbstractAction
{
    /**
     * Call the action module
     */
    public function process(array &$facts, Decision &$decision)
    {
        // nothing
    }
}
