<?php
namespace Bitmotion\Locate\Action;



/**
 * HelloWorld Action class
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Action
 */
class SetCookie extends AbstractAction {

	/**
	 * Call the action module
	 *
	 * @param array $factsArray
	 * @param \Bitmotion\Locate\Judge\Decision
	 */
	public function Process(&$factsArray, &$decision)
	{
		#die("Hello World");
#error_log(__LINE__);
	}

}

