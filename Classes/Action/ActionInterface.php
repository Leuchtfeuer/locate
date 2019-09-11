<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Action;

use Bitmotion\Locate\Judge\Decision;

/**
 * @deprecated
 */
interface ActionInterface
{
    /**
     * @param array $configuration TypoScript configuration array for this action
     */
    public function __construct(array $configuration);

    /**
     * Call the action module
     */
    public function process(array &$facts, Decision &$decision);
}
