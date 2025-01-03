<?php

declare(strict_types = 1);

// Feature is not available if EXT:static_info_tables is not loaded
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('static_info_tables') === false) {
    return;
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:tx_locate_domain_model_region',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => false,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:locate/Resources/Public/Icons/tx_locate_domain_model_region.svg',
    ],
    'types' => [
        '1' => ['showitem' => '--div--;LLL:EXT:locate/Resources/Private/Language/Database.xlf:tabs.basic,title,countries,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,hidden',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'title' => [
            'exclude' => false,
            'label' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:tx_locate_domain_model_region.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'countries' => [
            'exclude' => false,
            'label' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:tx_locate_domain_model_region.countries',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => \Leuchtfeuer\Locate\Utility\CountryHelper::class . '->selectItemsTCA',
                'minitems' => 1,
                'size' => 10,
                'autoSizeMax' => 30,
                'multiple' => false,
            ],
        ],
    ],
];
