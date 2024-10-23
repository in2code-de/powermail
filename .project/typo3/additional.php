<?php

$changeSettings = [
    'BE' => [
        'installToolPassword' => '$argon2i$v=19$m=65536,t=16,p=1$RmZtaE5LQU1rSGw2NUZiWQ$YdU5on+xJ4lI6Gwd4LWpbddeAEu88cctS2dnO+r9ty0',
        'lockSSL' => '0',
        'compressionLevel' => '0',
        'debug' => true,
    ],
    'FE' => [
        'cacheHash' => [
            'enforceValidation' => false,
        ],
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8mb4',
                'driver' => 'mysqli',
                'dbname' => getenv('MYSQL_DATABASE'),
                'host' => getenv('MYSQL_HOST'),
                'user' => getenv('MYSQL_USER'),
                'password' => getenv('MYSQL_PASSWORD'),
                'tableoptions' => [
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
            ],
        ],
    ],
    'GFX' => [
        'colorspace' => 'sRGB',
        'processor_enabled' => true,
        'processor' => 'ImageMagick',
        'processor_path' => '/usr/bin/',
        'processor_path_lzw' => '/usr/bin/',
    ],
    'HTTP' => [
        'verify' => '0',
    ],
    'MAIL' => [
        'transport_smtp_server' => getenv('MAIL_HOST'),
        'transport_smtp_encrypt' => '',
        'transport_smtp_password' => '',
        'transport_smtp_username' => '',
        'transport' => 'smtp',
        'defaultMailFromAddress' => 'docker@localhost',
        'defaultMailFromName' => 'local - Docker',
    ],
    'SYS' => [
        'sitename' => 'LOKAL: ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
        'displayErrors' => 1,
        'enableDeprecationLog' => 'file',
        'features' => [
            'powermailEditorsAreAllowedToSendAttachments' => true,
        ],
        'systemLogLevel' => 0,
        'devIPmask' => '*',
        'clearCacheSystem' => 1,
        'curlUse' => 1,
        'exceptionalErrors' => '28674',
        'trustedHostsPattern' => '.*.*',
    ],
];

$GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], $changeSettings);
