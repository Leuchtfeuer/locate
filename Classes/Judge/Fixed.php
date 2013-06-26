<?php
namespace Bitmotion\Locate\Judge;



/**
 * Fixed judge class
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Judge
 */
class Fixed extends AbstractJudge {

	/**
	 * The judge decide if the case is true and therefore the configured action should be called
	 *
	 * @param array $factsArray
	 * @return Decision|FALSE
	 */
	public function Process(&$factsArray)
	{
		$decision = new Decision();
		$decision->setActionName($this->configArray['action']);
		return $decision;
	}

}

