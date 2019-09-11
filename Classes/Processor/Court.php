<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Processor;

use Bitmotion\Locate\Action\AbstractAction;
use Bitmotion\Locate\Exception;
use Bitmotion\Locate\FactProvider\AbstractFactProvider;
use Bitmotion\Locate\Judge\AbstractJudge;
use Bitmotion\Locate\Judge\Decision;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Court implements ProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $configuration = [];

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
    public function run(): void
    {
        try {
            $this->processFacts();
            $this->callAction($this->callJudges());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    protected function processFacts(): void
    {
        foreach ($this->configuration['facts'] as $key => $className) {
            if (!class_exists($className)) {
                throw new Exception(sprintf('Class %s does not exist.', $className));
            }

            $this->logger->info(sprintf('Fact provider with key "%s" will be called.', $key));

            /* @var $factProvider AbstractFactProvider */
            $factProvider = GeneralUtility::makeInstance($className, $key, []);
            $factProvider->process($this->facts);
        }
    }

    /**
     * @throws Exception
     */
    protected function callJudges(): ?Decision
    {
        foreach ($this->configuration['judges'] as $key => $value) {
            // As we have an TypoScript array, skip every key which has sub properties
            if (strpos((string)$key, '.') !== false) {
                continue;
            }

            $this->logger->info(sprintf('Judge with key %s will be called: %s', $key, $value));

            if (!class_exists($value)) {
                $this->logger->error(sprintf('Class %s does nost exist.', $value));
                throw new Exception(sprintf('Judge %s does not exist.', $value));
            }

            /* @var $judge AbstractJudge */
            $judge = GeneralUtility::makeInstance($value, $this->configuration['judges'][$key . '.']);
            $decision = $judge->process($this->facts);

            if ($decision) {
                return $decision;
            }
        }

        return null;
    }

    /**
     * @throws Exception
     */
    protected function callAction(Decision $decision)
    {
        if (!$decision->hasAction()) {
            throw new Exception('No action should be called. This migth be a problem in you configuration');
        }

        $actionName = $decision->getActionName();
        $actionConfigArray = $this->configuration['actions'][$actionName . '.'];

        if (!$actionConfigArray) {
            throw new Exception(sprintf('Action with name "%s" should be called but is not configured!', $actionName));
        }

        $this->logger->info(" Action with name '$actionName' will be called");

        foreach ($actionConfigArray as $key => $value) {
            // As we have an TypoScript array, skip every key which has sub properties
            if (strpos((string)$key, '.') !== false) {
                continue;
            }

            if ($this->dryRun) {
                $this->logger->info(sprintf('Action part "%s.%s" would be called, but dryRun is set.', $key, $value));
                continue;
            }

            $this->logger->info(sprintf('Action part "%s.%s" will be called.', $key, $value));
            $configuration = array_merge($this->configuration['settings'], $actionConfigArray[$key . '.']);

            /* @var $action AbstractAction */
            $action = GeneralUtility::makeInstance($value, $configuration);
            $action->process($this->facts, $decision);
        }
    }

    public function getFacts(): array
    {
        return $this->facts;
    }
}
