<?php
defined('TYPO3_MODE') || die;

return [
    'ctrl' => [
        'label' => 'iso2',
        'adminOnly' => true,
        'rootLevel' => 1,
        'is_static' => 1,
        'readOnly' => 1,
        'default_sortby' => 'ORDER BY cn_short_en',
        'title' => 'LLL:EXT:locate/Resources/Private/Language/locallang_db.xlf:tx_locate_ip2country',
        'iconfile' => 'EXT:locate/Resources/Public/Icons/icon_tx_locate_ip2country.png',
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'ipfrom,ipto,iso2',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'ipfrom, ipto, iso2',
    ],
    'columns' => [
        'ipfrom' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:locate/Resources/Private/Language/locallang_db.xlf:tx_locate_ip2country.ipfrom',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'max' => 4,
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0,
            ],
        ],
        'ipto' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:locate/Resources/Private/Language/locallang_db.xlf:tx_locate_ip2country.ipto',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'max' => 4,
                'eval' => 'int',
                'checkbox' => 0,
                'default' => 0,
            ],
        ],
        'iso2' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:locate/Resources/Private/Language/locallang_db.xlf:tx_locate_ip2country.iso2',
            'config' => [
                'type' => 'input',
                'size' => 5,
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'ipfrom;;;;1-1-1, ipto, iso2'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];
