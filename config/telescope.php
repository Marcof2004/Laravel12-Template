<?php

use Laravel\Telescope\Http\Middleware\Authorize;
use Laravel\Telescope\Watchers;

return [

    'enabled' => env('TELESCOPE_ENABLED', true),

    'path' => env('TELESCOPE_PATH', 'telescope'),

    'driver' => env('TELESCOPE_DRIVER', 'database'),

    'storage' => [
        'database' => [
            'connection' => env('TELESCOPE_DB_CONNECTION', 'telescope'),
            'chunk' => 1000,
        ],
    ],

    'middleware' => [
        'web',
        Authorize::class,
    ],

    'only_paths' => [
        // 'api/*',
    ],

    'ignore_paths' => array_filter(explode(',', env('TELESCOPE_IGNORE_PATHS', 'telescope-api,vendor/telescope'))),

    'ignore_commands' => array_filter(explode(',', env('TELESCOPE_IGNORE_COMMANDS', ''))),

    'watchers' => [
        Watchers\BatchWatcher::class => env('TELESCOPE_WATCHER_BATCH', true),
        Watchers\CacheWatcher::class => env('TELESCOPE_WATCHER_CACHE', true),
        Watchers\CommandWatcher::class => [
            'enabled' => env('TELESCOPE_WATCHER_COMMAND', true),
            'ignore' => [],
        ],
        Watchers\DumpWatcher::class => env('TELESCOPE_WATCHER_DUMP', true),
        Watchers\EventWatcher::class => env('TELESCOPE_WATCHER_EVENT', true),
        Watchers\ExceptionWatcher::class => env('TELESCOPE_WATCHER_EXCEPTION', true),
        Watchers\GateWatcher::class => env('TELESCOPE_WATCHER_GATE', true),
        Watchers\JobWatcher::class => env('TELESCOPE_WATCHER_JOB', true),
        Watchers\LogWatcher::class => env('TELESCOPE_WATCHER_LOG', true),
        Watchers\MailWatcher::class => env('TELESCOPE_WATCHER_MAIL', true),
        Watchers\ModelWatcher::class => [
            'enabled' => env('TELESCOPE_WATCHER_MODEL', true),
            'events' => ['eloquent.*'],
        ],
        Watchers\NotificationWatcher::class => env('TELESCOPE_WATCHER_NOTIFICATION', true),
        Watchers\QueryWatcher::class => [
            'enabled' => env('TELESCOPE_WATCHER_QUERY', true),
            'slow' => 100,
        ],
        Watchers\RedisWatcher::class => env('TELESCOPE_WATCHER_REDIS', true),
        Watchers\RequestWatcher::class => [
            'enabled' => env('TELESCOPE_WATCHER_REQUEST', true),
            'size_limit' => env('TELESCOPE_RESPONSE_SIZE_LIMIT', 64),
        ],
        Watchers\ScheduleWatcher::class => env('TELESCOPE_WATCHER_SCHEDULE', true),
        Watchers\ViewWatcher::class => env('TELESCOPE_WATCHER_VIEW', true),
    ],
];
