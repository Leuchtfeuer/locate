<?php
defined('TYPO3_MODE') || die;

call_user_func(
    function ($extensionKey) {
        // Turn logging off by default
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Leuchtfeuer']['Locate']['writerConfiguration'] = [
            \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
                \TYPO3\CMS\Core\Log\Writer\NullWriter::class => [],
            ],
        ];
    }, 'locate'
);
