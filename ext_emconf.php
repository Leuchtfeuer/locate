<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Locate',
    'description' => 'The users country and prefered language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages.  New functionality can be added easily.',
    'category' => 'fe',
    'author' => 'Florian Wessels, Rene Fritz',
    'author_email' => 'typo3-ext@bitmotion.de',
    'author_company' => 'Bitmotion GmbH',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '8.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
            'static_info_tables' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Bitmotion\\Locate\\' => 'Classes'
        ],
    ],
];
