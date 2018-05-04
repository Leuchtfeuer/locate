<?php

namespace Bitmotion\Locate\Action;


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
     * @param array $factsArray
     * @param \Bitmotion\Locate\Judge\Decision
     */
    public function Process(&$factsArray, &$decision)
    {
        // nothing
    }

}

