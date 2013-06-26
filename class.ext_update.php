<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2008 René Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Class for updating the db
 *
 * @author	 René Fritz <r.fritz@bitmotion.de>
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{

		$GLOBALS['TYPO3_DB']->debugOutput=1;

		$extPath = t3lib_extMgm::extPath('locate');

		$content = '';

		$content.= "<br />Import the IP data from a csv file which has to be placed in {$extPath}Resources/Private/IP2Country/ip-to-country.csv";
		$content .= '<br />';
		$import = t3lib_div::_GP('import');

		if ($import == 'Import') {


			$handle = fopen($extPath . "Resources/Private/IP2Country/ip-to-country.csv", "r");

			$sql = "TRUNCATE TABLE tx_locate_ip2country";
			$GLOBALS['TYPO3_DB']->admin_query($sql);


			$count = 0;
			$tstamp = time();
			$cruser_id = intval($GLOBALS['BE_USER']->user['uid']);

			$sql = 'INSERT INTO tx_locate_ip2country (tstamp,crdate,cruser_id,ipfrom,ipto,iso2) VALUES ';
			// get IP data from csv and write into db
			while ($zeile = fgetcsv($handle, 1024, ',', '"')) {
				if ($count) {
					$sql .= ",\n";
				}
				$sql .= '(' .
					$tstamp . ',' .
					$tstamp . ',' .
					$cruser_id . ',' .
					$GLOBALS['TYPO3_DB']->fullQuoteStr($zeile[0], 'tx_locate_ip2country') . ',' .
					$GLOBALS['TYPO3_DB']->fullQuoteStr($zeile[1], 'tx_locate_ip2country') . ',' .
					$GLOBALS['TYPO3_DB']->fullQuoteStr($zeile[2], 'tx_locate_ip2country') . ')';

				$count++;
			}
			$GLOBALS['TYPO3_DB']->admin_query($sql);

			fclose($handle);


			$content .= '<p><strong>Done.</strong></p>';
			$content .= "<p>Imported $count entries.</p>";

			#$content .= "<p>".nl2br($sql)."</p>";
		} else {
			$content .= '</form>';
			$content .= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">';
			$content .= '<br /><br />';
			$content .= '<input type="submit" name="import" value="Import" />';
			$content .= '</form>';
		}

		return $content;
	}



	function access() {
		return TRUE;
	}

}



?>