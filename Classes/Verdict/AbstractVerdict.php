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

namespace Leuchtfeuer\Locate\Verdict;

use Leuchtfeuer\Locate\Store\SessionStore;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractVerdict
{
    /**
     * @var array<string, mixed>
     */
    protected array $configuration;

    protected SessionStore $session;

    public function __construct(
        protected readonly LoggerInterface $logger,
    ) {
        $this->session = new SessionStore();
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function withConfiguration(array $configuration): self
    {
        $clonedObject = clone $this;
        $clonedObject->configuration = $configuration;

        return $clonedObject;
    }

    /**
     * Call the action module
     */
    abstract public function execute(): ?ResponseInterface;
}
