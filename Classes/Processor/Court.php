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

namespace Leuchtfeuer\Locate\Processor;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Leuchtfeuer\Locate\Exception\IllegalActionException;
use Leuchtfeuer\Locate\Exception\IllegalFactProviderException;
use Leuchtfeuer\Locate\Exception\IllegalJudgeException;
use Leuchtfeuer\Locate\FactProvider\AbstractFactProvider;
use Leuchtfeuer\Locate\FactProvider\StaticFactProvider;
use Leuchtfeuer\Locate\Judge\AbstractJudge;
use Leuchtfeuer\Locate\Judge\Decision;
use Leuchtfeuer\Locate\Verdict\AbstractVerdict;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidActionNameException;

class Court implements ProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $configuration = [];

    /**
     * @var AbstractFactProvider[]
     */
    protected $facts = [];

    /**
     * If set the action won't be executed
     */
    protected $dryRun = false;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
        $this->dryRun = (bool)$configuration['settings']['dryRun'];
    }

    /**
     * Processes the configuration
     */
    public function run(): ?ResponseInterface
    {
        // Exclude bots from redirects
        if ((bool)($this->configuration['settings']['excludeBots'] ?? false) && class_exists('Jaybizzle\CrawlerDetect\CrawlerDetect')) {
            $crawlerDetect = new CrawlerDetect(
                $GLOBALS['TYPO3_REQUEST']->getHeaders(),
                GeneralUtility::getIndpEnv('HTTP_USER_AGENT')
            );

            if ($crawlerDetect->isCrawler()) {
                return null;
            }
        }

        try {
            $this->processFacts();
            $decision = $this->callJudges();

            if ($decision === null || !$decision->hasVerdict()) {
                throw new \Exception('No verdict should be delivered. This might be a problem in you configuration', 1608653067);
            }

            return $this->enforceJudgement($decision->getVerdictName());
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
        foreach ($this->configuration['facts'] ?? [] as $key => $className) {
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

        foreach ($this->configuration['judges'] ?? [] as $key => $className) {
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

            $configuration = $this->configuration['judges'][$key . '.'] ?? [];

            if (empty($configuration)) {
                $this->logger->warning('No judges are configured.');
            }

            $this->logger->info(sprintf('Judge with key "%s" will be called.', $key));
            $this->addJudgement($judgements, $configuration, $key, $judge, $priorities);
        }

        return !empty($judgements) ? $this->getDecision($judgements) : null;
    }

    protected function addJudgement(array &$judgements, array $configuration, $key, AbstractJudge $judge, array &$priorities): void
    {
        $fact = (isset($configuration['fact']) && isset($this->facts[$configuration['fact']])) ? $this->facts[$configuration['fact']] : new StaticFactProvider();

        if ($fact instanceof AbstractFactProvider) {
            $judge = $judge->withConfiguration($this->configuration['judges'][$key . '.'])->adjudicate($fact, (int)$key);

            if ($judge->hasDecision() && !isset($decisions[$judge->getDecision()->getPriority()])) {
                $decision = $judge->getDecision();
                $priority = $decision->getPriority();

                if ($fact->isMultiple()) {
                    $priorities[$fact->getBasename()] = $priorities[$fact->getBasename()] ?? $priority;
                    $priority = $priorities[$fact->getBasename()];
                    $judgements[$priority][$fact->getPriority()] = $decision;
                } else {
                    $judgements[$priority] = $decision;
                }
            }
        }
    }

    protected function getDecision(array $judgements): Decision
    {
        ksort($judgements);
        $judgement = array_shift($judgements);

        if (is_array($judgement)) {
            return $this->getDecision($judgement);
        }

        return $judgement;
    }

    protected function enforceJudgement(string $actionName): ?ResponseInterface
    {
        $className = $this->configuration['verdicts'][$actionName];

        if (!class_exists($className)) {
            throw new InvalidActionNameException(sprintf('Class "%s" does not exist. Skip.', $className), 1608652319);
        }

        $verdict = GeneralUtility::makeInstance($className);

        if (!$verdict instanceof AbstractVerdict) {
            throw new IllegalActionException(
                sprintf('Verdict "%s" has to extend "%s".', $className, AbstractVerdict::class),
                1608632285
            );
        }

        $this->logger->info(sprintf('Verdict with name %s will be delivered', $actionName));

        if ($this->dryRun === false) {
            $configuration = array_merge($this->configuration['settings'], $this->configuration['verdicts'][$actionName . '.'] ?? []);
            $verdict = $verdict->withConfiguration($configuration);

            return $verdict->execute();
        }

        return null;
    }
}
