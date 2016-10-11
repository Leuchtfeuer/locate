<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Rene Fritz <typo3-ext(at)bitmotion.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Plugin 'Locate' for the 'locate' extension.
 *
 * @author	Rene Fritz <typo3-ext(at)bitmotion.de>
 * @package	TYPO3
 * @subpackage	tx_locate
 */
class tx_locate_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin {
	var $prefixId      = 'tx_locate_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_locate_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'locate';	// The extension key.
	var $pi_checkCHash = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{

		# $conf['debug'] = true;
		# $_SERVER['REMOTE_ADDR'] = '217.150.241.201';

		$locateProcessor = new \Bitmotion\Locate\Processor\Court($conf);
		$locateProcessor->setDryRun($conf['dryRun']);
		$locateProcessor->Run();

		if ($conf['debug']) {

			if ($objLog = $locateProcessor->Logger->GetWriter('Memory')) {
				$ResultLog = str_replace("\n\n", "\n", (string)$objLog->GetLog());
			}
			return nl2br($ResultLog) . \t3lib_utility_Debug::viewArray($locateProcessor->GetFactsArray());
		}
	}
}

