<?php

namespace Leuchtfeuer\Locate\Domain\DTO;

class Configuration
{
    public const string OVERRIDE_PARAMETER = 'setLang';
    protected bool $dryRun = false;
    protected bool $excludeBots = true;
    protected bool $sessionHandling = true;
    protected bool $overrideSessionValue = true;
    protected string $overrideQueryParameter = self::OVERRIDE_PARAMETER;
    protected string $simulateIp = '';
    /** @var array<string, mixed> */
    protected array $verdicts = [];
    /** @var array<string, mixed> */
    protected array $facts = [];
    /** @var array<string, mixed>  */
    protected array $judges = [];

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    public function setDryRun(bool $dryRun): void
    {
        $this->dryRun = $dryRun;
    }

    public function isExcludeBots(): bool
    {
        return $this->excludeBots;
    }

    public function setExcludeBots(bool $excludeBots): void
    {
        $this->excludeBots = $excludeBots;
    }

    public function isSessionHandling(): bool
    {
        return $this->sessionHandling;
    }

    public function setSessionHandling(bool $sessionHandling): void
    {
        $this->sessionHandling = $sessionHandling;
    }

    public function isOverrideSessionValue(): bool
    {
        return $this->overrideSessionValue;
    }

    public function setOverrideSessionValue(bool $overrideSessionValue): void
    {
        $this->overrideSessionValue = $overrideSessionValue;
    }

    public function getOverrideQueryParameter(): string
    {
        return $this->overrideQueryParameter;
    }

    public function setOverrideQueryParameter(string $overrideQueryParameter): void
    {
        $this->overrideQueryParameter = $overrideQueryParameter;
    }

    public function getSimulateIp(): string
    {
        return $this->simulateIp;
    }

    public function setSimulateIp(string $simulateIp): void
    {
        $this->simulateIp = $simulateIp;
    }

    /**
     * @return array<string, mixed>
     */
    public function getVerdicts(): array
    {
        return $this->verdicts;
    }

    /**
     * @param array<string, mixed> $verdicts
     */
    public function setVerdicts(array $verdicts): void
    {
        $this->verdicts = $verdicts;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFacts(): array
    {
        return $this->facts;
    }

    /**
     * @param array<string, mixed> $facts
     */
    public function setFacts(array $facts): void
    {
        $this->facts = $facts;
    }

    /**
     * @return array<string, mixed>
     */
    public function getJudges(): array
    {
        return $this->judges;
    }

    /**
     * @param array<string, mixed> $judges
     */
    public function setJudges(array $judges): void
    {
        $this->judges = $judges;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return [
            'dryRun' => $this->isDryRun(),
            'overrideQueryParameter' => $this->getOverrideQueryParameter(),
            'overrideSessionValue' => $this->isOverrideSessionValue(),
            'sessionHandling' => $this->isSessionHandling(),
            'excludeBots' => $this->isExcludeBots(),
            'simulateIp' => $this->getSimulateIp(),
        ];
    }
}