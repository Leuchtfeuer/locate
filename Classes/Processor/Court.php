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

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Leuchtfeuer\Locate\Domain\DTO\Configuration;
use Leuchtfeuer\Locate\Exception\IllegalActionException;
use Leuchtfeuer\Locate\Exception\IllegalFactProviderException;
use Leuchtfeuer\Locate\Exception\IllegalJudgeException;
use Leuchtfeuer\Locate\FactProvider\AbstractFactProvider;
use Leuchtfeuer\Locate\FactProvider\StaticFactProvider;
use Leuchtfeuer\Locate\Judge\AbstractJudge;
use Leuchtfeuer\Locate\Judge\Decision;
use Leuchtfeuer\Locate\Utility\TypeCaster;
use Leuchtfeuer\Locate\Verdict\AbstractVerdict;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidActionNameException;

class Court implements ProcessorInterface
{
    protected Configuration $configuration;

    /**
     * @var AbstractFactProvider[]
     */
    protected array $facts = [];

    public function __construct(private readonly LoggerInterface $logger) {}

    public function withConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Processes the configuration
     */
    public function run(ServerRequestInterface $request): ?ResponseInterface
    {
        // Exclude bots from redirects
        if ($this->configuration->isExcludeBots() && class_exists('Jaybizzle\CrawlerDetect\CrawlerDetect')) {
            $crawlerDetect = new CrawlerDetect(
                $request->getHeaders(),
                GeneralUtility::getIndpEnv('HTTP_USER_AGENT')
            );

            if ($crawlerDetect->isCrawler()) {
                return null;
            }
        }

        try {
            $this->processFacts();
            $decision = $this->callJudges();

            if ($decision instanceof Decision) {
                if (!$decision->hasVerdict()) {
                    throw new \Exception(
                        'No verdict should be delivered. This might be a problem in you configuration',
                        1608653067
                    );
                }
                return $this->enforceJudgement($decision->getVerdictName());
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }

        return null;
    }

    /**
     * @throws IllegalFactProviderException
     */
    protected function processFacts(): void
    {
        foreach ($this->configuration->getFacts() as $key => $className) {
            if (!is_string($className)) {
                $this->logger->warning('$className is not a string. Skip.');
                continue;
            }
            if (!class_exists($className)) {
                $this->logger->warning(sprintf('Class "%s" does not exist. Skip.', $className));
                continue;
            }

            /* @var $factProvider AbstractFactProvider */
            $factProvider = GeneralUtility::makeInstance($className, $key, $this->configuration);

            if (!$factProvider instanceof AbstractFactProvider) {
                throw new IllegalFactProviderException(
                    sprintf('Fact provider "%s" has to extend "%s".', $className, AbstractFactProvider::class),
                    1608631752
                );
            }

            $this->logger->info(sprintf('Fact provider with key "%s" will be called.', $key));
            $this->facts[$key] = $factProvider->process();
        }
    }

    /**
     * @throws IllegalJudgeException
     */
    protected function callJudges(): ?Decision
    {
        $judgements = [];
        $priorities = [];
        $judges = $this->configuration->getJudges();

        foreach ($judges as $key => $className) {
            // Since we have an TypoScript array, skip every key which has sub properties
            if (!is_string($className)) {
                continue;
            }

            if (!class_exists($className)) {
                $this->logger->warning(sprintf('Class "%s" does not exist. Skip.', $className));
                continue;
            }

            /* @var $judge AbstractJudge */
            $judge = GeneralUtility::makeInstance($className);

            if (!$judge instanceof AbstractJudge) {
                throw new IllegalJudgeException(
                    sprintf('Judge "%s" has to extend "%s".', $className, AbstractJudge::class),
                    1608632285
                );
            }

            $judgeConfig = TypeCaster::limitToArray($judges[$key . '.'] ?? []);

            if ($judgeConfig === []) {
                $this->logger->warning('No judges are configured.');
            }

            $this->logger->info(sprintf('Judge with key "%s" will be called.', $key));
            $this->addJudgement($judgements, $judgeConfig, (int)$key, $judge, $priorities);
        }

        return empty($judgements) ? null : $this->getDecision($judgements);
    }

    /**
     * @param array{} $judgements
     * @param array<int|string, mixed> $judgeConfig
     * @param array<string, int> $priorities
     */
    protected function addJudgement(
        array &$judgements,
        array $judgeConfig,
        int $key,
        AbstractJudge $judge,
        array &$priorities
    ): void {
        $fact = isset($judgeConfig['fact'], $this->facts[$judgeConfig['fact']])
            ? $this->facts[$judgeConfig['fact']]
            : new StaticFactProvider();

        if ($fact instanceof AbstractFactProvider) {
            $judge = $judge->withConfiguration($judgeConfig)->adjudicate($fact, $key);
            $decision = $judge->getDecision();

            if ($decision instanceof Decision) {
                $priority = $decision->getPriority();

                if ($fact->isMultiple()) {
                    $priorities[$fact->getBasename()] ??= $priority;
                    $priority = $priorities[$fact->getBasename()];
                    $judgements[$priority][$fact->getPriority()] = $decision;
                } else {
                    $judgements[$priority] = $decision;
                }
            }
        }
    }

    /**
     * @param array<int, Decision>|array<int, array<int, Decision>> $judgements
     */
    protected function getDecision(array $judgements): Decision
    {
        ksort($judgements);
        /** @var Decision|array<int, Decision> $judgement */
        $judgement = array_shift($judgements);

        if (is_array($judgement)) {
            return $this->getDecision($judgement);
        }

        return $judgement;
    }

    protected function enforceJudgement(string $actionName): ?ResponseInterface
    {
        $verdicts = $this->configuration->getVerdicts();
        $className = $verdicts[$actionName] ?? null;

        if (!is_string($className)) {
            throw new InvalidActionNameException('$className is not a string. Skip.', 1608652320);
        }
        if (!class_exists($className)) {
            throw new InvalidActionNameException(sprintf('Class "%s" does not exist. Skip.', $className), 1608652319);
        }

        $verdict = GeneralUtility::makeInstance($className, $this->logger);

        if (!$verdict instanceof AbstractVerdict) {
            throw new IllegalActionException(
                sprintf('Verdict "%s" has to extend "%s".', $className, AbstractVerdict::class),
                1608632285
            );
        }

        $this->logger->info(sprintf('Verdict with name %s will be delivered', $actionName));

        if ($this->configuration->isDryRun() === false) {
            $verdictSettings = is_array($verdicts[$actionName . '.']) ? $verdicts[$actionName . '.'] : [];
            $configuration = array_merge(
                $this->configuration->getSettings(),
                $verdictSettings
            );

            $verdict = $verdict->withConfiguration($configuration);

            return $verdict->execute();
        }

        return null;
    }
}
