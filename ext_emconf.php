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
    'version' => '10.5.0',
    'state' => 'stable',
    'author' => 'Powermail Development Team',
    'author_email' => 'service@in2code.de',
    'author_company' => 'in2code.de',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.6-11.5.99',
            'php' => '7.4.0-8.1.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [],
    ],
];
