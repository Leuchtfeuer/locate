<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Processor;

use Bitmotion\Locate\Action\ActionInterface;
use Bitmotion\Locate\Exception;
use Bitmotion\Locate\FactProvider\FactProviderInterface;
use Bitmotion\Locate\Judge\Decision;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Court
 */
class Court implements ProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var array
     */
    protected $facts = [];

    /**
     * If set the action won't be executed
     *
     * @var bool
     */
    protected $dryRun = false;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setDryRun(bool $dryRun): void
    {
        $this->dryRun = $dryRun;
    }

    /**
     * Processes the configuration
     */
    public function run(): void
    {
        try {
            $this->processFacts();
            $this->callAction($this->callJudges());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    protected function processFacts(): void
    {
        foreach ($this->configuration['facts.'] as $key => $className) {
            if (!class_exists($className)) {
                throw new \Bitmotion\Locate\Action\Exception('Class ' . $className . ' does not exist.');
            }

            $this->logger->info("Fact provider with key '$key' will be called: " . $className);

            /* @var $factProvider FactProviderInterface */
            $factProvider = GeneralUtility::makeInstance($className, $key, []);
            $factProvider->process($this->facts);
        }
    }

    /**
     * @todo: Maybe sort TypoScript keys
     * @throws \Bitmotion\Locate\Judge\Exception
     */
    protected function callJudges(): ?Decision
    {
        foreach ($this->configuration['judges.'] as $key => $value) {
            // As we have an TypoScript array, skip every key which has sub properties
            if (strpos((string)$key, '.') !== false) {
                continue;
            }

            $this->logger->info("Juge with key '$key' will be called: " . $value);

            /* @var $factProvider FactProviderInterface */
            $factProvider = GeneralUtility::makeInstance($value, $this->configuration['judges.'][$key . '.']);
            $decision = $factProvider->process($this->facts);

            if ($decision) {
                return $decision;
            }
        }

        return null;
    }

    /**
     * @throws \Bitmotion\Locate\Exception
     */
    protected function callAction(Decision $decision)
    {
        if (!$decision->hasAction()) {
            throw new Exception('No action should be called. This migth be a problem in you configuration');
        }

        $actionName = $decision->getActionName();

        $actionConfigArray = $this->configuration['actions.'][$actionName . '.'];

        if (!$actionConfigArray) {
            throw new Exception("Action with name '$actionName' should be called but is not configured!");
        }

        $this->logger->info(" Action with name '$actionName' will be called");

        foreach ($actionConfigArray as $key => $value) {
            // As we have an TypoScript array, skip every key which has sub properties
            if (strpos((string)$key, '.') !== false) {
                continue;
            }

            if ($this->dryRun) {
                $this->logger->info(" Action part '$key.$value' would be called, but dryRun is set.");
                continue;
            }

            $this->logger->info(" Action part '$key.$value' will be called");

            /* @var $action ActionInterface */
            $action = GeneralUtility::makeInstance($value, $actionConfigArray[$key . '.']);
            $action->process($this->facts, $decision);
        }
    }

    public function getFacts(): array
    {
        return $this->facts;
    }
}
