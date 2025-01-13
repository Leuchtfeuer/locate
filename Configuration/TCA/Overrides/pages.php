<?php

declare(strict_types = 1);

$temporaryColumns = [
    'tx_locate_regions' => [
        'exclude' => true,
        'label' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:pages.tx_locate_regions',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectMultipleSideBySide',
            'foreign_table' => 'tx_locate_domain_model_region',
            'foreign_table_where' => 'ORDER BY title',
            'MM' => 'tx_locate_page_region_mm',
            'size' => 10,
            'autoSizeMax' => 30,
            'multiple' => false,
            'items' => [
                [
                    'label' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:pages.tx_locate_regions.applyWhenNoMatch',
                    'value' => \Leuchtfeuer\Locate\Domain\Repository\RegionRepository::APPLY_WHEN_NO_IP_MATCHES,
                ],
            ],
        ],
    ],
    'tx_locate_invert' => [
        'exclude' => true,
        'label' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:pages.tx_locate_invert',
        'description' => 'LLL:EXT:locate/Resources/Private/Language/Database.xlf:pages.tx_locate_invert.description',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $temporaryColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;LLL:EXT:locate/Resources/Private/Language/Database.xlf:tabs.locate,tx_locate_invert,tx_locate_regions'
);
