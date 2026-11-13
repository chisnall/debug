<?php

$config = [
    // Application
    'app' => [
        'httpUser' => 'apache',
    ],

    // Directories
    'directories' => [
        'entries' => getEnvironment('DIR_ENTRIES'),
        'logs' => getEnvironment('DIR_LOGS'),
        'xdebug' => getEnvironment('DIR_XDEBUG'),
    ],

    // Page
    'page' => [
        // Defaults
        'default' => [
            'layout' => 'default',
            'title' => 'Debug',
        ],
        // Error
        'error' => [
            'client' => [
                'layout' => 'error',
                'view' => 'error/client',
            ],
            'framework' => [
                'layout' => 'error',
                'view' => 'error/framework',
            ],
            'php' => [
                'layout' => 'error',
                'view' => 'error/php',
            ],
        ],
        // Flash message classes
        'flash' => [
            'success' => 'flash-success',
            'info' => 'flash-info',
            'warning' => 'flash-warning',
        ],
    ],

    // Classes
    'class' => [
        'exception' => [
            'controller' => 'App\Controllers\SiteController',
            'framework' => 'App\Exception\FrameworkException',
            'notFound' => 'App\Exception\NotFoundException',
        ],
    ],

    // Controllers
    'controller' => [
        'genericView' => [
            'controller' => 'App\Controllers\SiteController',
            'method' => 'genericView',
        ]
    ],

    // Debug
    'debug' => [
        'pollingInterval' => 1000,
        'token' => [
            'enabled' => getEnvironment('TOKEN_ENABLED'),
            'value' => getEnvironment('TOKEN_SECRET'),
        ],
    ],

    # Ignore referer on these pages
    'no-referer' => [
        '/login',
        '/logout',
        '/register',
    ]
];

return $config;
