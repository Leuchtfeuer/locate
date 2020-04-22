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
 *  (c) 2019 Florian Wessels <f.wessels@bitmotion.de>, Bitmotion GmbH
 *
 ***/

use Bitmotion\Locate\Exception;
use Bitmotion\Locate\Judge\Decision;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractAction implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param array $configuration TypoScript configuration array for this action
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Call the action module
     *
     * @throws Exception
     */
    public function process(array &$facts, Decision &$decision)
    {
        throw new Exception('Process not implemented in ' . __CLASS__);
    }
}
