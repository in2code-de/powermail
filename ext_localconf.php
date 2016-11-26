<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

call_user_func(function () {

    /**
     * Enable caching for show action in form controller
     */
    $uncachedFormActions = 'form';
    if (\In2code\Powermail\Utility\ConfigurationUtility::isEnableCachingActive()) {
        $uncachedFormActions = '';
    }
    $uncachedFormActions .= ', create, confirmation, optinConfirm, marketing';

    /**
     * Include Frontend Plugins for Powermail
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'In2code.powermail',
        'Pi1',
        [
            'Form' => 'form, create, confirmation, optinConfirm, marketing'
        ],
        [
            'Form' => $uncachedFormActions
        ]
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'In2code.powermail',
        'Pi2',
        [
            'Output' => 'list, show, edit, update, export, rss, delete'
        ],
        [
            'Output' => 'list, edit, update, export, rss, delete'
        ]
    );

    /**
     * Hook to show PluginInformation under a tt_content element in page module of type powermail
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['powermail'] =
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('powermail') .
        'Classes/Hook/PluginPreview.php:In2code\Powermail\Hooks\PluginPreview';

    /**
     * Hook for initially filling the marker field in backend
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        'EXT:powermail/Classes/Hook/CreateMarker.php:In2code\Powermail\Hook\CreateMarker';

    /**
     * Hook to extend the FlexForm
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][] =
        'EXT:powermail/Classes/Hook/FlexFormManipulationHook.php:In2code\Powermail\Hook\FlexFormManipulationHook';

    /**
     * Hook to extend the FlexForm since core version 8.5
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing']['powermail'] =
        In2code\Powermail\Hook\FlexFormManipulationHook::class;

    /**
     * JavaScript evaluation of TCA fields
     */
    $TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['\In2code\Powermail\Tca\EvaluateEmail'] =
        'EXT:powermail/Classes/Tca/EvaluateEmail.php';

    /**
     * eID to get location from geo coordinates
     */
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidGetLocation'] =
        'EXT:powermail/Classes/Eid/GetLocationEid.php';

    /**
     * eID to store marketing information
     */
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['powermailEidMarketing'] =
        'EXT:powermail/Classes/Eid/MarketingEid.php';

    /**
     * CommandController for powermail tasks
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
        \In2code\Powermail\Command\TaskCommandController::class;

    /**
     * SignalSlot to convert old tablenames to new tablenames automaticly after installing
     */
    $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    $dispatcher->connect(
        \TYPO3\CMS\Extensionmanager\Utility\InstallUtility::class,
        'afterExtensionInstall',
        \In2code\Powermail\Slot\ConvertTableNames::class,
        'convert'
    );
});
