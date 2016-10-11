<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_locate_ip2country"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country',
		'label'     => 'iso2',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'readOnly' => '1',
		'editlock' => '1',
		'default_sortby' => "ORDER BY uid",
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Configuration/TCA/IP2Country.php',
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'icon_tx_locate_ip2country.png',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "ipfrom, ipto, iso2",
	)
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY,"Configuration/TypoScript/","Locate test setup");

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

if (TYPO3_MODE == 'BE')	{

	#t3lib_extMgm::addModule('web','txlocateM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}
?>