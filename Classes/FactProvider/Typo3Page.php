<?php

namespace Bitmotion\Locate\FactProvider;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Provide multiple environment data from t3lib_div::getIndpEnv()
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package Locate
 * @subpackage FactProvider
 */
class Typo3Page extends AbstractFactProvider {

	/**
	 * Call the fact module which might add some data to the factArray
	 *
	 * @param array $factsArray
	 */
	public function Process(&$factsArray) {
		$factPropertyName = $this->GetFactPropertyName ( 'pid' );
		$factsArray [$factPropertyName] = $GLOBALS ['TSFE']->id;

		$factPropertyName = $this->GetFactPropertyName ( 'type' );
		$factsArray [$factPropertyName] = $GLOBALS ['TSFE']->type;

		$factPropertyName = $this->GetFactPropertyName ( 'rootLine' );
		$factsArray [$factPropertyName] = implode ( ',', array_keys ( $GLOBALS ['TSFE']->rootLine ) );

		$factPropertyName = $this->GetFactPropertyName ( 'rootLineTitles' );
		$titlesArray = array ();
		foreach ( $GLOBALS ['TSFE']->rootLine as $value ) {
			$titlesArray [] = $value ['title'];
		}
		$factsArray [$factPropertyName] = implode ( '|', ($titlesArray) );

		$factPropertyName = $this->GetFactPropertyName ( 'loginUser' );
		$factsArray [$factPropertyName] = ($GLOBALS ['TSFE']->loginUser ? 'true' : 'false');

		$factPropertyName = $this->GetFactPropertyName ( 'groupList' );
		$factsArray [$factPropertyName] = $GLOBALS ['TSFE']->gr_list;

		$factPropertyName = $this->GetFactPropertyName ( 'sys_language_uid' );
		$factsArray [$factPropertyName] = $GLOBALS ['TSFE']->sys_language_uid;

		$factPropertyName = $this->GetFactPropertyName ( 'sys_language_isocode' );
		$factsArray [$factPropertyName] = $GLOBALS ['TSFE']->sys_language_isocode;

		$factPropertyName = $this->GetFactPropertyName ( 'language' );
		$factsArray [$factPropertyName] = $GLOBALS ['TSFE']->config ['config'] ['language'];
#TODO locale without dot
#split locale de_DE: de DE
		$factPropertyName = $this->GetFactPropertyName ( 'locale' );
		$factsArray [$factPropertyName] = $GLOBALS ['TSFE']->config ['config'] ['locale_all'];
	}

#TODO modify so it can be used
	/**
	 * Returns an array of sys_language records containing the ISO code as the key and the record's uid as the value
	 *
	 * @return	array	sys_language records: ISO code => uid of sys_language record
	 * @copyright borrowed from tx_rlmplanguagedetection_pi1
	 */
	function getSysLanguages() {
		$availableLanguages = array();

		if (strlen($this->conf['defaultLang'])) {
			$availableLanguages[0] = trim(strtolower($this->conf['defaultLang']));
		}

			// Two options: prior TYPO3 3.6.0 the title of the sys_language entry must be one of the two-letter iso codes in order
			// to detect the language. But if the static_languages is installed and the sys_language record points to the correct
			// language, we can use that information instead.

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'lg_iso_2',
			'static_languages',
			'1=1'
		);
		if (!$this->conf['useOldOneTreeConcept'] && $res) {
				// Table and field exist so create query for the new approach:
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'sys_language.uid, static_languages.lg_iso_2 as isocode',
				'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode = static_languages.uid',
				'1=1' . $this->cObj->enableFields ('sys_language') . $this->cObj->enableFields ('static_languages')
			);
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'sys_language.uid, sys_language.title as isocode',
				'sys_language',
				'1=1' . $this->cObj->enableFields ('sys_language')
			);
		}
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if (TYPO3_DLOG && !$row['isocode'])
				GeneralUtility::devLog('No ISO-code given for language with UID ' . $row['uid'], $this->extKey);
			$availableLanguages[$row['uid']] = trim(strtolower($row['isocode']));
		}

		// Get the isocodes associated with the available sys_languade uid's
		if (is_array($availableLanguages)) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'sys_language.uid, static_languages.lg_iso_2 as isocode, static_languages.lg_country_iso_2',
				'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode=static_languages.uid',
				'sys_language.uid IN('.implode(',',array_keys($availableLanguages)).')'.
					$this->cObj->enableFields('sys_language').
					$this->cObj->enableFields('static_languages')
				);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$tmpLanguages[trim(strtolower($row['isocode'] . ($row['lg_country_iso_2']? '-' . $row['lg_country_iso_2'] : '')))] = $row['uid'];
			}
			$availableLanguages = $tmpLanguages;
		}

		//Remove all languages except limitToLanguages
		if($this->conf['limitToLanguages'] != '') {
			$limitToLanguages = GeneralUtility::trimExplode(
			  ',', //Delimiter string to explode with
			  strtolower($this->conf['limitToLanguages']), //The string to explode
			  true //If set, all empty values (='') will NOT be set in output
			);
			$tmp = array();
			foreach($availableLanguages as $key=>$value) {
				//Only add allowed languages
				if(in_array($key, $limitToLanguages)) {
					$tmp[$key] = $value;
				}
			}
			$availableLanguages = $tmp;
		}

		//Remove all languages in the exclude list
		if($this->conf['excludeLanguages'] != '') {
			if($this->conf['excludeLanguages'] != '') {
				$excludeLanguages = GeneralUtility::trimExplode(
				  ',', //Delimiter string to explode with
				  strtolower($this->conf['excludeLanguages']), //The string to explode
				  true //If set, all empty values (='') will NOT be set in output
				);
			}
			$tmp = array();
			foreach($excludeLanguages as $excludeLanguage) {
				unset($availableLanguages[$excludeLanguage]);
			}
		}

		return $availableLanguages;
	}
}
