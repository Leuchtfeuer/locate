<?php

$EM_CONF['locate'] = [
    'title' => 'Locate',
    'description' => 'The users country and preferred language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages. It is also possible to deny access to configurable pages for configurable countries.',
    'version' => '11.0.0-dev',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.0.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'static_info_tables' => '',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Florian Wessels',
    'author_email' => 'f.wessels@Leuchtfeuer.com',
    'author_company' => 'Leuchtfeuer Digital Marketing',
    'autoload' => [
        'psr-4' => [
            'Leuchtfeuer\\Locate\\' => 'Classes',
        ],
    ],
];
