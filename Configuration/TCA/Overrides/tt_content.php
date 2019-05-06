<?php
declare(strict_types=1);
defined('TYPO3_MODE') || die;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'locate',
    'Configuration/TypoScript',
    'IP 2 Country'
);
