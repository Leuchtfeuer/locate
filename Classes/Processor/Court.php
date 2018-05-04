<?php

namespace Bitmotion\Locate\Processor;


use Bitmotion\Locate\Judge\Exception;
use Bitmotion\Locate\Log\Logger;
use Bitmotion\Locate\Log\Writer\Memory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Court
 *
 * @package Bitmotion\Locate\Processor
 */
class Court implements ProcessorInterface
{

    /**
     * Logger object
     *
     * @var \Bitmotion\Locate\Log\Logger
     */
    public $Logger;

    /**
     * @var array
     */
    protected $configArray;

    /**
     * @var array
     */
    protected $factsArray = [];

    /**
     * If set the action won't be executed
     *
     * @var boolean
     */
    protected $dryRun = false;

    /**
     *
     * @param array $configArray TypoScript config array
     */
    public function __construct($configArray)
    {
        $this->configArray = $configArray;
        $this->Logger = $this->CreateLogger();
    }

    /**
     *
     * @return \Bitmotion\Locate\Log\Logger
     */

    /**
     * @return Logger
     */
    protected function CreateLogger()
    {
        /**
         * @var Logger $objLog
         * @var Memory $objLogWriter
         */
        $objLog = GeneralUtility::makeInstance(Logger::class);
        $objLogWriter = GeneralUtility::makeInstance(Memory::class);
        $objLog->AddWriter($objLogWriter, 'Memory');

        return $objLog;
    }

    /**
     * @param boolean $dryRun
     */
    public function setDryRun($dryRun)
    {
        $this->dryRun = $dryRun;
    }

    /**
     * Processes the configuration
     */
    public function Run()
    {
        try {
            $this->GetFacts();

            if (!$this->factsArray) {
                throw new Exception('No facts are collected. this seems to be a misconfiguration.');
            }

            $this->ReviewFacts();

            $decision = $this->CallJudges();

            $this->CallAction($decision);

        } catch (\Bitmotion\Locate\Exception $e) {
            $this->Logger->Log($e->getMessage(), \Bitmotion\Locate\Log\Logger::EMERG);
        }
    }

    /**
     *
     */
    protected function GetFacts()
    {
        foreach ($this->configArray['facts.'] as $key => $value) {

            if (strpos($key, '.')) {
                continue;
            }

            $this->Logger->Info("Fact provider with key '$key' will be called: " . $value);

            /* @var $factProvider \Bitmotion\Locate\FactProvider\FactProviderInterface */
            $factProvider = new $value($key, $this->configArray['facts.'][$key . '.']);
            $factProvider->Process($this->factsArray);
        }
    }

    /**
     *
     */
    protected function ReviewFacts()
    {
        if (is_array($this->configArray['reviewer.']) && count($this->configArray['reviewer.'])) {
            foreach ($this->configArray['reviewer.'] as $key => $value) {

                if (strpos($key, '.')) {
                    continue;
                }

                $this->Logger->Info("Reviewer with key '$key' will be called: " . $value);

                #TODO;
            }
        }

        foreach ($this->factsArray as $key => $value) {
            if (!$value) {
                $this->factsArray[$key] = '__empty__';
            }
        }
    }


    /**
     *
     * @return \Bitmotion\Locate\Judge\Decision
     */
    protected function CallJudges()
    {

        #TODO sort TS numbers
        $actionName = null;
        foreach ($this->configArray['judges.'] as $key => $value) {

            if (strpos($key, '.')) {
                continue;
            }

            $this->Logger->Info("Juge with key '$key' will be called: " . $value);


            /* @var $factProvider \Bitmotion\Locate\FactProvider\FactInterface */
            $judge = new $value($this->configArray['judges.'][$key . '.'], $this->Logger);
            $decision = $judge->Process($this->factsArray);
            if ($decision) {
                break;
            }
        }
        return $decision;
    }


    /**
     *
     * @param \Bitmotion\Locate\Judge\Decision $decision
     * @throws \Bitmotion\Locate\Exception
     */
    protected function CallAction(\Bitmotion\Locate\Judge\Decision $decision)
    {
        if (!$decision->hasAction()) {
            throw new \Bitmotion\Locate\Exception("No action should be called. This migth be a problem in you configuration");
        }

        $actionName = $decision->getActionName();

        $actionConfigArray = $this->configArray['actions.'][$actionName . '.'];

        if (!$actionConfigArray) {
            throw new \Bitmotion\Locate\Exception("Action with name '$actionName' should be called but is not configured!");
        }


        $this->Logger->Info(" Action with name '$actionName' will be called");

        # TODO sort array
        foreach ($actionConfigArray as $key => $value) {

            if (strpos($key, '.')) {
                continue;
            }

            if ($this->dryRun) {
                $this->Logger->Info(" Action part '$key.$value' would be called, but dryRun is set.");
                continue;
            }

            $this->Logger->Info(" Action part '$key.$value' will be called");

            /* @var $actionPart \Bitmotion\Locate\Action\ActionInterface */
            $actionPart = new $value($actionConfigArray[$key . '.'], $this->Logger);
            $actionPart->Process($this->factsArray, $decision);

        }
        #TODO
    }

    /**
     *
     * @return array
     */
    public function GetFactsArray()
    {
        return $this->factsArray;
    }
}

