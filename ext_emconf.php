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
    'version' => '8.0.1',
    'state' => 'stable',
    'author' => 'Powermail Development Team',
    'author_email' => 'alexander.kellner@in2code.de',
    'author_company' => 'in2code.de',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-10.99.99',
            'php' => '7.0.0-7.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [],
    ],
    '_md5_values_when_last_written' => '',
];
