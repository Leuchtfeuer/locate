<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($extensionKey) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Imaging\IconFactory::class]['overrideIconOverlay'][$extensionKey]
            = \Leuchtfeuer\Locate\Hook\OverrideIconOverlayHook::class;
    }, 'locate'
);
