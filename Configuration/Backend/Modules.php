<?php

return [
    'web_powermail' => [
        'parent' => 'web',
        'position' => [],
        'access' => 'user',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_mod.xlf',
        'path' => '/module/web/Powermail',
        'extensionName' => 'Powermail',
        'controllerActions' => [
            \In2code\Powermail\Controller\ModuleController::class =>
                'dispatch, list, exportXls, exportCsv, reportingBe, toolsBe, overviewBe, ' .
                'checkBe, converterBe, converterUpdateBe, reportingFormBe, reportingMarketingBe, ' .
                'fixUploadFolder, fixWrongLocalizedForms, fixFilledMarkersInLocalizedFields, ' .
                'fixWrongLocalizedPages, fixFilledMarkersInLocalizedPages'
        ]
    ],
];
