<?php

namespace Bitmotion\Locate;
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
use Bitmotion\Locate\Processor\Court;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;

/**
 * Plugin 'Locate' for the 'locate' extension.
 *
 * @author	Rene Fritz <typo3-ext(at)bitmotion.de>
 * @package	TYPO3
 * @subpackage	tx_locate
 */
class Locate extends AbstractPlugin {

    var $prefixId      = 'tx_locate_pi1';		// Same as class name
    var $scriptRelPath = 'pi1/class.tx_locate_pi1.php';	// Path to this script relative to the extension dir.
    var $extKey        = 'locate';	// The extension key.
    var $pi_checkCHash = true;

    /**
     * The main method of the PlugIn
     *
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	string The content that is displayed on the website
     */
    function main($content,$conf)	{

        /** @var Court $locateProcessor */
        $locateProcessor = GeneralUtility::makeInstance(Court::class, $conf);
        $locateProcessor->setDryRun($conf['dryRun']);
        $locateProcessor->Run();

        if ($conf['debug']) {

            if ($objLog = $locateProcessor->Logger->GetWriter('Memory')) {
                $ResultLog = str_replace("\n\n", "\n", (string)$objLog->GetLog());
            }
            return nl2br($ResultLog) . \TYPO3\CMS\Core\Utility\DebugUtility::viewArray($locateProcessor->GetFactsArray());
        }
    }
}

