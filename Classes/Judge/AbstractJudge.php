<?php
namespace Bitmotion\Locate\Judge;



/**
 * Abstract judge class
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Judge
 */
abstract class AbstractJudge implements JudgeInterface {

	protected $configArray;

	/**
	 *
	 * @var \Bitmotion\Locate\Log\Logger
	 */
	protected $Logger;


	/**
	 *
	 * @param array $configArray TypoScript configuration array for this judge
	 * @param \Bitmotion\Locate\Log\Logger $logger
	 */
	public function __construct($configArray, $logger)
	{
		$this->configArray = $configArray;
		$this->Logger = $logger;
	}

	/**
	 * Call the fact module which might add some data to the factArray
	 *
	 * @param array $factsArray
	 * @return Decision|FALSE
	 */
	public function Process(&$factsArray)
	{
		throw new Exception('Process not implemented in ' . __CLASS__);
	}

	/**
	 * Adds a prefix to the factArray property name
	 *
	 * @param string $property
	 * @return string
	 */
	protected function GetFactPropertyName($property)
	{
		return $this->baseName . '.' . $property;
	}
}

