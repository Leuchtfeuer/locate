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

/**
 * @deprecated
 */
class AndCondition extends Condition
{
    /**
     * @deprecated
     */
    public function __construct(array $configuration)
    {
        trigger_error(sprintf('Using %s is deprecated. Use %s instead.', __CLASS__, Condition::class), E_USER_DEPRECATED);

        parent::__construct($configuration);
    }

    /**
     * @deprecated
     */
    public function process(array $facts, int $priority = 999): ?Decision
    {
        trigger_error(sprintf('Using %s is deprecated. Use %s instead.', __CLASS__, Condition::class), E_USER_DEPRECATED);

        return parent::process($facts, $priority);
    }
}
