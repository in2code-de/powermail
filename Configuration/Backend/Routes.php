<?php

return [
    'powermail_list' => [
        'path' => '/powermail/list',
        'target' => \In2code\Powermail\Controller\ModuleController::class . '::listAction',
    ],
    'powermail_formoverview' => [
        'path' => '/powermail/formoverview',
        'target' => \In2code\Powermail\Controller\ModuleController::class . '::overviewBeAction',
    ],
    'powermail_reportingform' => [
        'path' => '/powermail/reportingform',
        'target' => \In2code\Powermail\Controller\ModuleController::class . '::reportingFormBeAction',
    ],
    'powermail_reportingmarketing' => [
        'path' => '/powermail/reportingmarketing',
        'target' => \In2code\Powermail\Controller\ModuleController::class . '::reportingMarketingBeAction',
    ],
    'powermail_functioncheck' => [
        'path' => '/powermail/functioncheck',
        'target' => \In2code\Powermail\Controller\ModuleController::class . '::checkBeAction',
    ],
];
