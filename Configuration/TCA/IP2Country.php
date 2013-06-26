<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_locate_ip2country"] = array (
	"ctrl" => $TCA["tx_locate_ip2country"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "ipfrom,ipto,iso2"
	),
	"feInterface" => $TCA["tx_locate_ip2country"]["feInterface"],
	"columns" => array (
		"ipfrom" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country.ipfrom",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"default" => 0
			)
		),
		"ipto" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country.ipto",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"default" => 0
			)
		),
		"iso2" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country.iso2",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "ipfrom;;;;1-1-1, ipto, iso2")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>