<?php
declare(strict_types = 1);

return [
    'frontend' => [
        'bitmotion/locate/language-redirect' => [
            'target' => \Bitmotion\Locate\Middleware\LanguageRedirectMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
    ],
];
