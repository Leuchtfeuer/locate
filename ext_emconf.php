<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Locate',
    'description' => 'The users country and prefered language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages.  New functionality can be added easily.',
    'category' => 'Bitmotion Extensions',
    'author' => 'Rene Fritz, Florian Wessels',
    'author_email' => 'typo3-ext@bitmotion.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '7.6.4-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
            'static_info_tables' => '',
            'php' => '5.5',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
