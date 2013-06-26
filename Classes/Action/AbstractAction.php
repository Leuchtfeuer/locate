<?php
namespace Bitmotion\Locate\Action;



/**
 * Abstract Action class
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Action
 */
abstract class AbstractAction implements ActionInterface {

	protected $configArray;

	/**
	 *
	 * @var \Bitmotion\Locate\Log\Logger
	 */
	protected $Logger;

	/**
	 *
	 * @param array $configArray TypoScript configuration array for this action
	 * @param \Bitmotion\Locate\Log\Logger $logger
	 */
	public function __construct($configArray, $logger)
	{
		$this->configArray = $configArray;
		$this->Logger = $logger;
	}

	/**
	 * Call the action module
	 *
	 * @param array $factsArray
	 * @param \Bitmotion\Locate\Judge\Decision
	 */
	public function Process(&$factsArray, &$decision)
	{
		throw new Exception('Process not implemented in ' . __CLASS__);
	}

}

