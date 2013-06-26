<?php
namespace Bitmotion\Locate\Processor;


use Bitmotion\Locate\Judge\Exception;
/**
 * Processor interface
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Processor
 */
class Court implements ProcessorInterface {

	protected $configArray;
	protected $factsArray = array();

	/**
	 * Logger object
	 *
	 * @var \Bitmotion\Locate\Log\Logger
	 */
	public $Logger;


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
	 * @param boolean $dryRun
	 */
	public function setDryRun( $dryRun) {
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

			/* @var $factProvider \Bitmotion\Locate\FactProvider\FactInterface */
			$factProvider = new $value($key, $this->configArray['facts.'][$key.'.']);
			$factProvider->Process($this->factsArray);
		}
	}


	/**
	 *
	 * @return array
	 */
	public function GetFactsArray()
	{
		return $this->factsArray;
	}


	/**
	 *
	 */
	protected function ReviewFacts()
	{
		foreach ($this->configArray['reviewer.'] as $key => $value) {

			if (strpos($key, '.')) {
				continue;
			}

			$this->Logger->Info("Reviewer with key '$key' will be called: " . $value);

			#TODO;
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
		$actionName = NULL;
		foreach ($this->configArray['judges.'] as $key =>  $value) {

			if (strpos($key, '.')) {
				continue;
			}

			$this->Logger->Info("Juge with key '$key' will be called: " . $value);


			/* @var $factProvider \Bitmotion\Locate\FactProvider\FactInterface */
			$judge = new $value($this->configArray['judges.'][$key.'.'], $this->Logger);
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

		$actionConfigArray = $this->configArray['actions.'][$actionName.'.'];

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

			/* @var $factProvider \Bitmotion\Locate\Action\ActionInterface */
			$actionPart = new $value($actionConfigArray[$key.'.'], $this->Logger);
			$actionPart->Process($this->factsArray, $decision);

		}
		#TODO
	}


	/**
	 *
	 * @return \Bitmotion\Locate\Log\Logger
	 */
	protected function CreateLogger()
	{
		$objLog = new \Bitmotion\Locate\Log\Logger();
		#$objLogWriter = new \Bitmotion\Locate\Log\Writer\Stream(\Cmp3\Cmp3::$LogPath . $objJob->ID . '.log');
		#$objLog->AddWriter($objLogWriter, 'Stream');
		$objLogWriter = new \Bitmotion\Locate\Log\Writer\Memory();
		$objLog->AddWriter($objLogWriter, 'Memory');

		return $objLog;
	}
}

