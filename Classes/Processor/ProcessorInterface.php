<?php
namespace Bitmotion\Locate\Processor;


/**
 * Processor interface
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage Processor
 */
interface ProcessorInterface {

	/**
	 *
	 * @param array $configArray TypoScript config array
	 */
	public function __construct($configArray);

	/**
	 * Processes the configuration
	 */
	public function Run();
}

