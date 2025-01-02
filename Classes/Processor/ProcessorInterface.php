<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Processor;

use Leuchtfeuer\Locate\Domain\DTO\Configuration;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

interface ProcessorInterface
{

    public function __construct(LoggerInterface $logger);

    public function withConfiguration(Configuration $configuration): self;

    /**
     * Processes the configuration
     */
    public function run(): ?ResponseInterface;
}
