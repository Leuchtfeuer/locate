<?php

namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;


/**
 * Class Dummy
 *
 * @package Bitmotion\Locate\Action
 */
class Dummy extends AbstractAction
{

    /**
     * Call the action module
     *
     * @param array $facts
     * @param Decision $decision
     */
    public function process(array &$facts, Decision &$decision)
    {
        // nothing
    }

}

