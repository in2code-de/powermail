<?php
/***************************************************************
 * Extension Manager/Repository config file for ext "powermail".
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'powermail',
    'description' => 'Powermail is a well-known, editor-friendly, powerful
        and easy to use mailform extension with a lots of features
        (spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)',
    'category' => 'plugin',
    'version' => '7.3.1',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => 'uploads/tx_powermail,typo3temp/assets/tx_powermail',
    'clearcacheonload' => 1,
    'author' => 'Powermail Development Team',
    'author_email' => 'alexander.kellner@in2code.de',
    'author_company' => 'in2code.de',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
            'php' => '7.0.0-7.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [],
    ],
    '_md5_values_when_last_written' => '',
];
