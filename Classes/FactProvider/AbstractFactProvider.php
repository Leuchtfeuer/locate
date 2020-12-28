<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\FactProvider;

abstract class AbstractFactProvider
{
    protected $basename = '';

    protected $configuration = [];

    protected $multiple = false;

    protected $facts = [];

    protected $priority = 0;

    /**
     * @param string $basename The basename for the factsArray. This name comes from configuration.
     */
    public function __construct(string $basename = '')
    {
        $this->basename = $basename;
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
    abstract public function process();

    /**
     * @param $prosecution
     * @return bool
     */
    abstract public function isGuilty($prosecution): bool;

    /**
     * Adds a prefix to the factArray property name
     *
     * @param string $property
     * @return string
     */
    protected function getFactPropertyName(string $property): string
    {
        return mb_strtolower($property);
    }

    /**
     * @return array|mixed
     */
    public function getSubject()
    {
        if (count($this->facts) > 1) {
            return $this->facts;
        }

        return array_shift($this->facts);
    }

    /**
     * Priority is only set if there are multiple facts (e.g. for browser accept languages)
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
