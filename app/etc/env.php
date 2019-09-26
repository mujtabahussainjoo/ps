<?php
return [
    'backend' => [
        'frontName' => 'swiftposadmin'
    ],
    'cron_consumers_runner' => [
        'cron_run' => false,
        'max_messages' => 1000,
        'consumers' => [
            'async.operations.all'
        ]
    ],
    'crypt' => [
        'key' => 'c5fa0fded68de0932906e9a295529fde'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'swiftsol_uat',
                'username' => 'swiftsol_only',
                'password' => 'MommyBeggedWhysSure',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'CROSS-ORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'ff3_'
            ],
            'page_cache' => [
                'id_prefix' => 'ff3_'
            ]
        ]
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 1
    ],
    'install' => [
        'date' => 'Fri, 17 May 2019 11:02:06 +0000'
    ],
    'db_logger' => [
        'output' => 'disabled',
        'log_everything' => 1,
        'query_time_threshold' => '0.001',
        'include_stacktrace' => 1
    ]
];
