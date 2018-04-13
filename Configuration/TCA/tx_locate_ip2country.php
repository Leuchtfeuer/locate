<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return [
    "ctrl" => [
        'title' => 'LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country',
        'label' => 'iso2',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'readOnly' => '1',
        'editlock' => '1',
        'default_sortby' => "ORDER BY uid",
        'iconfile' => 'EXT:locate/Resources/Public/Icons/icon_tx_locate_ip2country.png',
    ],
    "interface" => [
        "showRecordFieldList" => "ipfrom,ipto,iso2",
    ],
    "feInterface" => [
        "fe_admin_fieldList" => "ipfrom, ipto, iso2",
    ],
    "columns" => [
        "ipfrom" => [
            "exclude" => 1,
            "label" => "LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country.ipfrom",
            "config" => [
                "type" => "input",
                "size" => "4",
                "max" => "4",
                "eval" => "int",
                "checkbox" => "0",
                "default" => 0,
            ],
        ],
        "ipto" => [
            "exclude" => 1,
            "label" => "LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country.ipto",
            "config" => [
                "type" => "input",
                "size" => "4",
                "max" => "4",
                "eval" => "int",
                "checkbox" => "0",
                "default" => 0,
            ],
        ],
        "iso2" => [
            "exclude" => 1,
            "label" => "LLL:EXT:locate/Resources/Private/Language/locallang_db.xml:tx_locate_ip2country.iso2",
            "config" => [
                "type" => "input",
                "size" => "5",
            ],
        ],
    ],
    "types" => [
        "0" => ["showitem" => "ipfrom;;;;1-1-1, ipto, iso2"],
    ],
    "palettes" => [
        "1" => ["showitem" => ""],
    ],
];