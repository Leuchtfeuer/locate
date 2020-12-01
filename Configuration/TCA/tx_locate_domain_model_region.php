<?php

declare(strict_types = 1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:tx_locate_domain_model_region',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => false,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:locate/Resources/Public/Icons/tx_locate_domain_model_region.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, title, countries',
    ],
    'types' => [
        '1' => ['showitem' => '
            --div--;LLL:EXT:locate/Resources/Private/Language/Database.xlf:tabs.basic,
                title,countries,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                hidden,',
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
                        0 => '',
                        1 => '',
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
                'eval' => 'trim,required',
            ],
        ],
        'countries' => [
            'exclude' => false,
            'label' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:tx_locate_domain_model_region.countries',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'minitems' => 1,
                'size' => 10,
                'autoSizeMax' => 30,
                'multiple' => false,
                'foreign_table' => 'static_countries',
                'MM' => 'tx_locate_region_country_mm',
            ],
        ],
    ],
];
