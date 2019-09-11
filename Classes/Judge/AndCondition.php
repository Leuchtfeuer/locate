<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Judge;

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
