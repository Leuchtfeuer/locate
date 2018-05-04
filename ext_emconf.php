<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Locate',
    'description' => 'The users country and prefered language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages.  New functionality can be added easily.',
    'category' => 'fe',
    'author' => 'Rene Fritz, Florian Wessels',
    'author_email' => 'typo3-ext@bitmotion.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '8.7.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.2.99',
            'static_info_tables' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
