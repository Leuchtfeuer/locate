<?php

namespace Bitmotion\Locate\Action;


/**
 * Fixed Action class
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Action
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

