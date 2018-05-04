<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['LOG']['Bitmotion']['Locate']['writerConfiguration'] = [
    \TYPO3\CMS\Core\Log\LogLevel::INFO => [
        TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            'logFile' => 'typo3temp/var/logs/locate.log',
        ],
    ],

    \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
        TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            'logFile' => 'typo3temp/var/logs/locate.log',
        ],
    ],
];