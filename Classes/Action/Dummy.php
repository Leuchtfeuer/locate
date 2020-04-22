<?php
declare(strict_types = 1);
namespace Bitmotion\Locate\Action;

/***
 *
 * This file is part of the "Locate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 *
 ***/

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
