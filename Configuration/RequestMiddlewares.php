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
        'leuchtfeuer/locate/page-unavailable' => [
            'target' => \Bitmotion\Locate\Middleware\PageUnavailableMiddleware::class,
            'after' => [
                'typo3/cms-frontend/page-resolver',
            ],
            'before' => [
                'typo3/cms-frontend/preview-simulator',
            ],
        ],
    ],
];
