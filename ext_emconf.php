<?php

########################################################################
# Extension Manager/Repository config file for ext: "locate"
#
# Auto generated 24-06-2013 10:49
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Locate',
	'description' => 'The users country and prefered language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages.  New functionality can be added easily.',
	'category' => 'Bitmotion Extensions',
	'author' => 'Rene Fritz',
	'author_email' => 'typo3-ext(at)bitmotion.de',
	'shy' => '',
	'dependencies' => 'static_info_tables',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '<a href="http://www.bitmotion.de" target="_blank">Bitmotion</a>',
	'version' => '1.1.2',
	'constraints' => array(
		'depends' => array(
		    'typo3' => '4.5.0-6.2.99'
			'static_info_tables' => '',
			'php' => '5.3',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:18:{s:9:"ChangeLog";s:4:"54d8";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"e06b";s:14:"ext_tables.php";s:4:"67b5";s:14:"ext_tables.sql";s:4:"db1d";s:23:"icon_tx_locate_ip2country.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"2f9b";s:7:"tca.php";s:4:"ecfc";s:27:"pi1/class.tx_locate_pi1.php";s:4:"f322";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"1e02";s:14:"mod1/index.php";s:4:"8366";s:18:"mod1/locallang.xml";s:4:"e059";s:22:"mod1/locallang_mod.xml";s:4:"c779";s:19:"mod1/moduleicon.gif";s:4:"8074";s:19:"doc/wizard_form.dat";s:4:"69de";s:20:"doc/wizard_form.html";s:4:"927e";}',
);

?>