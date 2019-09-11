<?php
declare(strict_types=1);
namespace Bitmotion\Locate\FactProvider;

/**
 * @deprecated
 */
class Exception extends \Bitmotion\Locate\Exception
{
    /**
     * @deprecated
     */
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        trigger_error(sprintf('Using %s is deprecated. Use %s instead.', __CLASS__, \Bitmotion\Locate\Exception::class), E_USER_DEPRECATED);

        parent::__construct($message, $code, $previous);
    }
}
