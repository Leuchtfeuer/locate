<?php
namespace Bitmotion\Locate\Tools;


/**
 * IP to Country
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage FactProvider
 */
abstract class IP2Country {

	/**
	 * Check the IP in the geoip table and returns iso 2 code for the current remote address
	 *
	 * @param integer $IP as long remote address
	 * @return	false|string Example: DE
	 */
	public static function GetCountryIso2FromIP($IP)
	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('iso2','tx_locate_ip2country','ipfrom <= '.$IP.' AND ipto >= '.$IP);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			return $row['iso2'];
		}
		return false;
	}

}
