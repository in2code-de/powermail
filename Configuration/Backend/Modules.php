<?php

return [
    'web_powermail' => [
        'parent' => 'web',
        'position' => [],
        'access' => 'user',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_mod.xlf',
        'path' => '/module/web/powermail',
        'extensionName' => 'Powermail',
        'target' => [
            '_default' => \In2code\Powermail\Controller\ModuleController::class . '::listAction',
        ],
        'controllerActions' => [
            \In2code\Powermail\Controller\ModuleController::class =>
                'dispatch, list, exportXls, exportCsv, reportingBe, toolsBe, overviewBe, ' .
                'checkBe, converterBe, converterUpdateBe, reportingFormBe, reportingMarketingBe, ' .
                'fixUploadFolder, fixWrongLocalizedForms, fixFilledMarkersInLocalizedFields, ' .
                'fixWrongLocalizedPages, fixFilledMarkersInLocalizedPages',
        ],
    ],
    'powermail_list' => [
        'parent' => 'web_powermail',
        'position' => [],
        'access' => 'user',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => [
            'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang.xlf:BackendSelectionList',
        ],
        'extensionName' => 'Powermail',
        'path' => '/module/powermail/list',
        'controllerActions' => [
            \In2code\Powermail\Controller\ModuleController::class =>
                'dispatch, list, exportXls, exportCsv',
        ],
    ],
    'powermail_overview_be' => [
        'parent' => 'web_powermail',
        'position' => ['after' => 'powermail_list'],
        'access' => 'user',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => [
            'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang.xlf:BackendSelectionOverview',
        ],
        'extensionName' => 'Powermail',
        'path' => '/module/powermail/overviewbe',
        'controllerActions' => [
            \In2code\Powermail\Controller\ModuleController::class => 'overviewBe',
        ],
    ],
    'powermail_reporting_form' => [
        'parent' => 'web_powermail',
        'position' => ['after' => 'powermail_overview_be'],
        'access' => 'user',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => [
            'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang.xlf:BackendSelectionReportingForm',
        ],
        'extensionName' => 'Powermail',
        'path' => '/module/powermail/reporting-form',
        'controllerActions' => [
            \In2code\Powermail\Controller\ModuleController::class => 'reportingFormBe',
        ],
    ],
    'powermail_reporting_marketing' => [
        'parent' => 'web_powermail',
        'position' => ['after' => 'powermail_reporting_form'],
        'access' => 'user',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => [
            'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang.xlf:BackendSelectionReportingMarketing',
        ],
        'extensionName' => 'Powermail',
        'path' => '/module/powermail/reporting-marketing',
        'controllerActions' => [
            \In2code\Powermail\Controller\ModuleController::class => 'reportingMarketingBe',
        ],
    ],
    'powermail_check_be' => [
        'parent' => 'web_powermail',
        'position' => ['after' => 'powermail_reporting_marketing'],
        'access' => 'admin',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => [
            'title' => 'LLL:EXT:powermail/Resources/Private/Language/locallang.xlf:BackendSelectionCheck',
        ],
        'extensionName' => 'Powermail',
        'path' => '/module/powermail/check-be',
        'controllerActions' => [
            \In2code\Powermail\Controller\ModuleController::class => 'checkBe,' .
                'fixUploadFolder, fixWrongLocalizedForms, fixFilledMarkersInLocalizedFields, ' .
                'fixWrongLocalizedPages, fixFilledMarkersInLocalizedPages',
        ],
    ],
];
