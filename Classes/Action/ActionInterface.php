<?php
namespace Bitmotion\Locate\Action;


/**
 * Action interface
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Action
 */
interface ActionInterface {

	/**
	 *
	 * @param array $configArray TypoScript configuration array for this action
	 * @param \Bitmotion\Locate\Log\Logger
	 */
	public function __construct($configArray, $logger);

	/**
	 * Call the action module
	 *
	 * @param array $factsArray
	 * @param \Bitmotion\Locate\Judge\Decision
	 */
	public function Process(&$factsArray, &$decision);
}

