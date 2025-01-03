<?php

use Leuchtfeuer\Locate\Domain\DTO\Configuration;

defined('TYPO3') || die('Access denied.');

// override parameter needs to be excluded form cHash calculation due to enforceValidation = true setting
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = Configuration::OVERRIDE_PARAMETER;