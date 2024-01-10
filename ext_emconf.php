<?php

$EM_CONF['locate'] = [
    'title' => 'Locate',
    'description' => 'The users country and preferred language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages. New functionality can be added easily.',
    'version' => '10.0.4',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
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
            'Bitmotion\\Locate\\' => 'Classes',
        ],
    ],
];
