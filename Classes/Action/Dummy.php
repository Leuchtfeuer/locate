<?php
declare(strict_types=1);
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
