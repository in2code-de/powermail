<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'powermail',
    'description' => 'Powermail is a well-known, editor-friendly, powerful
        and easy to use mailform extension with a lots of features
        (spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)',
    'category' => 'plugin',
    'version' => '13.0.3',
    'state' => 'stable',
    'author' => 'Powermail Development Team',
    'author_email' => 'service@in2code.de',
    'author_company' => 'in2code.de',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'php' => '8.2.0 - 8.4.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
            'base_excel' => '',
            'static_info_tables' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'In2code\\Powermail\\' => 'Classes',
        ],
    ],
];
