<?php

$EM_CONF['locate'] = [
    'title' => 'Locate',
    'description' => 'The users country, preferred language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages. Locate also provides geo blocking for configurable pages in configurable countries.',
    'version' => '13.0.0',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'author' => 'Dev Leuchtfeuer',
    'author_email' => 'dev@Leuchtfeuer.com',
    'author_company' => 'Leuchtfeuer Digital Marketing',
    'autoload' => [
        'psr-4' => [
            'Leuchtfeuer\\Locate\\' => 'Classes',
        ],
    ],
];
