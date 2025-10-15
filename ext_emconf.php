<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'powermail',
    'description' => 'Powermail is a well-known, editor-friendly, powerful
        and easy to use mailform extension with a lots of features
        (spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)',
    'category' => 'plugin',
    'version' => '12.5.4',
    'state' => 'stable',
    'author' => 'Powermail Development Team',
    'author_email' => 'service@in2code.de',
    'author_company' => 'in2code.de',
    'constraints' => [
        'depends' => [
            'typo3' => '12.2.0-12.5.99',
            'php' => '8.1.0 - 8.3.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
            'base_excel' => '',
            'static_info_tables' => ''
        ],
    ],
];
