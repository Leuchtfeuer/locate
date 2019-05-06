<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Processor;

use Bitmotion\Locate\Action\ActionInterface;
use Bitmotion\Locate\Exception;
use Bitmotion\Locate\FactProvider\FactProviderInterface;
use Bitmotion\Locate\Judge\Decision;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Court
 */
class Court implements ProcessorInterface
{
    /**
     * @var Logger
     */
    public $logger = null;

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

    /**
     * @param array $configuration TypoScript config array
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    public function setDryRun(bool $dryRun)
    {
        $this->dryRun = $dryRun;
    }

    /**
     * Processes the configuration
     */
    public function run()
    {
        try {
            $this->processFacts();
            $this->reviewFacts();
            $this->callAction($this->callJudges());
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    protected function processFacts()
    {
        foreach ($this->configuration['facts.'] as $key => $className) {
            if (strpos($key, '.')) {
                continue;
            }

            if (!class_exists($className)) {
                throw new \Bitmotion\Locate\Action\Exception('Class ' . $className . ' does not exist.');
            }

            $this->logger->info("Fact provider with key '$key' will be called: " . $className);

            /* @var $factProvider FactProviderInterface */
            $factProvider = GeneralUtility::makeInstance($className, $key, []);
            $factProvider->process($this->facts);
        }
    }

    protected function reviewFacts()
    {
        if (is_array($this->configuration['reviewer.']) && count($this->configuration['reviewer.'])) {
            foreach ($this->configuration['reviewer.'] as $key => $value) {
                if (strpos($key, '.')) {
                    continue;
                }

                $this->logger->info("Reviewer with key '$key' will be called: " . $value);
            }
        }

        foreach ($this->facts as $key => $value) {
            if (!$value) {
                $this->facts[$key] = '__empty__';
            }
        }
    }

    /**
     * @return Decision|bool
     */
    protected function callJudges()
    {
        $actionName = null;
        $decision = null;

        //TODO sort TS numbers
        foreach ($this->configuration['judges.'] as $key => $value) {
            if (strpos($key, '.')) {
                continue;
            }

            $this->logger->info("Juge with key '$key' will be called: " . $value);

            /* @var $factProvider FactProviderInterface */
            $factProvider = new $value($this->configuration['judges.'][$key . '.'], $this->logger);
            $decision = $factProvider->process($this->facts);

            if ($decision) {
                return $decision;
            }
        }

        return false;
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

        // TODO sort array
        foreach ($actionConfigArray as $key => $value) {
            if (strpos($key, '.')) {
                continue;
            }

            if ($this->dryRun) {
                $this->logger->info(" Action part '$key.$value' would be called, but dryRun is set.");
                continue;
            }

            $this->logger->info(" Action part '$key.$value' will be called");

            /* @var $action ActionInterface */
            $action = new $value($actionConfigArray[$key . '.'], $this->logger);
            $action->process($this->facts, $decision);
        }
    }

    public function getFacts(): array
    {
        return $this->facts;
    }
}
