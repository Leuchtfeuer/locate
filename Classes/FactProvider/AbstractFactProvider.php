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

namespace Leuchtfeuer\Locate\FactProvider;

use Leuchtfeuer\Locate\Domain\DTO\Configuration;

abstract class AbstractFactProvider
{
    protected bool $multiple = false;

    /** @var array<string, mixed> */
    protected array $facts = [];

    protected int $priority = 0;

    /**
     * @param string $basename The basename for the factsArray. This name comes from configuration.
     * @param Configuration $configuration TypoScript configuration array for this fact provider
     */
    public function __construct(protected string $basename = '', protected Configuration $configuration = new Configuration())
    {
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @return string The base name of the actual fact provider
     */
    abstract public function getBasename(): string;

    /**
     * Call the fact module which might add some data to the factArray
     */
    abstract public function process(): self;

    abstract public function isGuilty(string $prosecution): bool;

    /**
     * Adds a prefix to the factArray property name
     */
    protected function getFactPropertyName(string $property): string
    {
        return mb_strtolower($property);
    }

    /**
     * Priority is only set if there are multiple facts (e.g. for browser accept languages)
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
