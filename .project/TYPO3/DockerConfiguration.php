<?php

$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'] = [
    'charset' => 'utf8',
    'driver' => 'mysqli',
    'dbname' => getenv('MYSQL_DATABASE'),
    'host' => getenv('MYSQL_HOST'),
    'user' => getenv('MYSQL_USER'),
    'password' => getenv('MYSQL_PASSWORD'),
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = 'LOKAL: ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];

$GLOBALS['TYPO3_CONF_VARS']['GFX']['colorspace'] = 'sRGB';
$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_enabled'] = true;
$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] = 'ImageMagick';
$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path'] = '/usr/bin/';
$GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path_lzw'] = '/usr/bin/';

// joh316
$GLOBALS['TYPO3_CONF_VARS']['BE']['installToolPassword'] = '$argon2i$v=19$m=65536,t=16,p=1$RmZtaE5LQU1rSGw2NUZiWQ$YdU5on+xJ4lI6Gwd4LWpbddeAEu88cctS2dnO+r9ty0';

$GLOBALS['TYPO3_CONF_VARS']['BE']['lockSSL'] = '0';
$GLOBALS['TYPO3_CONF_VARS']['BE']['compressionLevel'] = '0';
$GLOBALS['TYPO3_CONF_VARS']['BE']['debug'] = true;

// Debug lokal aktivieren - OS übergreifend
$GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] = 1;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] = 'file';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLogLevel'] = 0;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = '*';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['clearCacheSystem'] = 1;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] = 1;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['exceptionalErrors'] = '28674';

// Mail Settings
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_smtp_server'] = 'mail:1025';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_smtp_encrypt'] = '';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_smtp_password'] = '';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_smtp_username'] = '';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] = 'smtp';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] = 'docker@localhost';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] = 'local - Docker';

// Used for in-container requests
$GLOBALS['TYPO3_CONF_VARS']['HTTP']['verify'] = '0';
