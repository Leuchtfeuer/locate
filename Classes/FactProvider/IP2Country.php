<?php
namespace Bitmotion\Locate\FactProvider;


/**
 * IP to Country
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package    Locate
 * @subpackage FactProvider
 */
class IP2Country extends AbstractFactProvider {


	/**
	 * Call the fact module which might add some data to the factArray
	 *
	 * @param array $factsArray
	 */
	public function Process(&$factsArray)
	{
		$factPropertyName = $this->GetFactPropertyName('countryCode');
		$factsArray[$factPropertyName] = $this->GetCountryIso2FromIP();

		$factPropertyName = $this->GetFactPropertyName('IP2Dezimal');
		$factsArray[$factPropertyName] = $this->GetIP2Long();
	}


	/**
	 * Check the IP in the geoip table and returns iso 2 code for the current remote address
	 *
	 * @return	false|string Example: DE
	 */
	protected static function GetCountryIso2FromIP()
	{
		$IP = self::GetIP2Long();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('iso2','tx_locate_ip2country','ipfrom <= '.$IP.' AND ipto >= '.$IP);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			return $row['iso2'];
		}
		return false;
	}


	/**
	 * translates the ip to the decimal form
	 *
	 * @return string
	 */
	protected static function GetIP2Long()
	{
		return sprintf("%u",IP2Long(\t3lib_div::getIndpEnv('REMOTE_ADDR')));
	}

}
