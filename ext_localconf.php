<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(function () {

    /**
     * Enable caching for show action in form controller
     */
    $uncachedFormActions = 'form';
    if (\In2code\Powermail\Utility\ConfigurationUtility::isEnableCachingActive()) {
        $uncachedFormActions = '';
    }
    $uncachedFormActions .= ', checkCreate, create, checkConfirmation, confirmation, optinConfirm, disclaimer';

    /**
     * Include Frontend Plugins for Powermail
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Powermail',
        'Pi1',
        [
            \In2code\Powermail\Controller\FormController::class =>
                'form, checkCreate, create, checkConfirmation, confirmation, optinConfirm, disclaimer'
        ],
        [
            \In2code\Powermail\Controller\FormController::class => $uncachedFormActions
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Powermail',
        'Pi5',
        [
            \In2code\Powermail\Controller\FormController::class => 'marketing'
        ],
        [
            \In2code\Powermail\Controller\FormController::class => 'marketing'
        ],
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Powermail',
        'Pi2',
        [
            \In2code\Powermail\Controller\OutputController::class => 'list, show'
        ],
        [
            \In2code\Powermail\Controller\OutputController::class => 'list'
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Powermail',
        'Pi3',
        [
            \In2code\Powermail\Controller\OutputController::class => 'edit, update, delete'
        ],
        [
            \In2code\Powermail\Controller\OutputController::class => 'edit, update, delete'
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Powermail',
        'Pi4',
        [
            \In2code\Powermail\Controller\OutputController::class => 'list, show, edit, update, delete'
        ],
        [
            \In2code\Powermail\Controller\OutputController::class => 'list, edit, update, delete'
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    /**
     * Include PageTSconfig
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '@import \'EXT:powermail/Configuration/TSConfig/ContentElementWizard.typoscript\''
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '@import \'EXT:powermail/Configuration/TSConfig/WebList.typoscript\''
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '@import \'EXT:powermail/Configuration/TSConfig/EditorsSendAttachments.typoscript\''
    );

    /**
     * Hook for initially filling the marker field in backend
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \In2code\Powermail\Hook\CreateMarker::class;

    /**
     * JavaScript evaluation of TCA fields
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['\In2code\Powermail\Tca\EvaluateEmail'] =
        'EXT:powermail/Classes/Tca/EvaluateEmail.php';

    /**
     * eID to get location from geo coordinates
     */
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidGetLocation'] =
        \In2code\Powermail\Eid\GetLocationEid::class . '::main';

    /**
     * User field registrations in TCA/FlexForm
     */
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1580037906] = [
        'nodeName' => 'powermailShowFormNoteIfNoEmailOrNameSelected',
        'priority' => 50,
        'class' => \In2code\Powermail\Tca\ShowFormNoteIfNoEmailOrNameSelected::class,
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1580039839] = [
        'nodeName' => 'powermailMarker',
        'priority' => 50,
        'class' => \In2code\Powermail\Tca\Marker::class,
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1580065317] = [
        'nodeName' => 'powermailShowFormNoteEditForm',
        'priority' => 50,
        'class' => \In2code\Powermail\Tca\ShowFormNoteEditForm::class,
    ];

    /**
     * Update Wizards
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['powermailRelationUpdateWizard']
        = \In2code\Powermail\Update\PowermailRelationUpdateWizard::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['powermailLanguageUpdateWizard']
        = \In2code\Powermail\Update\PowermailLanguageUpdateWizard::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['powermailPluginUpdater']
        = \In2code\Powermail\Update\PowermailPluginUpdater::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['powermailPermissionUpdater']
        = \In2code\Powermail\Update\PowermailPermissionUpdater::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['powermailPermissionSubmodulesUpdater']
        = \In2code\Powermail\Update\PowermailPermissionSubmoduleUpdater::class;

    /**
     * Feature toggle
     * ToDo: remove for TYPO3 v14 compatible version
     */
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['powermailEditorsAreAllowedToSendAttachments'] ??= false;
});
