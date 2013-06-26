<?php
namespace Bitmotion\Locate\Reviewer;



/**
 * Abstract Reviewer class
 *
 * This is in fact the same stuff as in FactProvider
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Reviewer
 */
abstract class AbstractReviewer implements ReviewerInterface {

	protected $baseName;

	protected $configArray;


	/**
	 *
	 * @param string $baseName The basename for the factsArray. This name comes from configuration.
	 * @param array $configArray TypoScript configuration array for this fact provider
	 */
	public function __construct($baseName, $configArray)
	{
		$this->baseName = $baseName;
		$this->configArray = $configArray;
	}

	/**
	 * Call the fact module which might add some data to the factArray
	 *
	 * @param array $factsArray
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

