<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (!class_exists('\Bitmotion\System\Autoloader', false)) {

    $PATH_extension = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('locate');
    require_once($PATH_extension . 'Classes/System/Autoloader.php');

    \Bitmotion\System\Autoloader::RegisterAutoload();
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_locate_pi1.php', '_pi1', '', 0);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_locate_pi1.php', '_pi1',
    'list_type', 0);

?>