<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


if (!class_exists('\Cmp3\Autoload', false)) {

	$PATH_extension = t3lib_extMgm::extPath('locate');
	require_once($PATH_extension.'Classes/System/Autoloader.php');

	\Bitmotion\System\Autoloader::RegisterAutoload();
}




t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_locate_pi1.php','_pi1','',0);
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_locate_pi1.php','_pi1','list_type',0);


?>