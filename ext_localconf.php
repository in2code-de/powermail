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
    $uncachedFormActions .= ', create, confirmation, optinConfirm, marketing, disclaimer';

    /**
     * Include Frontend Plugins for Powermail
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Powermail',
        'Pi1',
        [
            \In2code\Powermail\Controller\FormController::class =>
                'form, create, confirmation, optinConfirm, marketing, disclaimer'
        ],
        [
            \In2code\Powermail\Controller\FormController::class => $uncachedFormActions
        ]
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Powermail',
        'Pi2',
        [
            \In2code\Powermail\Controller\OutputController::class => 'list, show, edit, update, export, rss, delete'
        ],
        [
            \In2code\Powermail\Controller\OutputController::class => 'list, edit, update, export, rss, delete'
        ]
    );

    /**
     * ContentElementWizard for Pi1
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '@import \'EXT:powermail/Configuration/TSConfig/ContentElementWizard.typoscript\''
    );

    /**
     * PageTSConfig for backend mod list
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '@import \'EXT:powermail/Configuration/TSConfig/WebList.typoscript\''
    );

    /**
     * Hook to show PluginInformation under a tt_content element in page module of type powermail
     */
    $cmsLayout = 'cms/layout/class.tx_cms_layout.php';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$cmsLayout]['tt_content_drawItem']['powermail'] =
        \In2code\Powermail\Hook\PluginPreview::class;

    /**
     * Hook for initially filling the marker field in backend
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \In2code\Powermail\Hook\CreateMarker::class;

    /**
     * Hook to extend the FlexForm
     */
    $ffTools = \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$ffTools]['flexParsing']['powermail'] =
        In2code\Powermail\Hook\FlexFormManipulationHook::class;

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
});
