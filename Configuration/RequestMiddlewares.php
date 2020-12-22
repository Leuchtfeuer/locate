<?php
declare(strict_types = 1);

return [
    'frontend' => [
        'leuchtfeuer/locate/language-redirect' => [
            'target' => \Leuchtfeuer\Locate\Middleware\LanguageRedirectMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
        'leuchtfeuer/locate/page-unavailable' => [
            'target' => \Leuchtfeuer\Locate\Middleware\PageUnavailableMiddleware::class,
            'after' => [
                'typo3/cms-frontend/page-resolver',
            ],
            'before' => [
                'typo3/cms-frontend/preview-simulator',
            ],
        ],
    ],
];
