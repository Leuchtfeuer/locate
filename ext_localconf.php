<?php
defined('TYPO3_MODE') || die;

// Turn logging off by default
$GLOBALS['TYPO3_CONF_VARS']['LOG']['Bitmotion']['Locate']['writerConfiguration'] = [
    \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
        \TYPO3\CMS\Core\Log\Writer\NullWriter::class => [],
    ],
];