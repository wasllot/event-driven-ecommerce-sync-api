<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible. If this setting
    | is null, Horizon will be available via the path defined in the "path"
    | option.
    |
    */

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors load the worker processes
    | in memory and manage them based on your given configuration.
    |
    */

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['high', 'default'],
                'balance' => 'auto', // Auto-scaling strategy
                'minProcesses' => 1,
                'maxProcesses' => 10, // Scale up to 10 workers for heavy load
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 60, // 60 seconds timeout
                'nice' => 0,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['high', 'default'],
                'balance' => 'simple', // Simple balancing for local dev
                'processes' => 3,
                'tries' => 3,
                'timeout' => 60,
                'memory' => 128,
            ],
        ],
    ],
];
