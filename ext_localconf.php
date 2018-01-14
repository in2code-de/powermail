<?php
if (!defined('TYPO3_MODE')) {
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
     * ContentElementWizard for Pi1
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:powermail/Configuration/TSConfig/ContentElementWizard.typoscript">'
    );

    /**
     * Table description files for localization
     */
    $tables = [
        \In2code\Powermail\Domain\Model\Form::TABLE_NAME,
        \In2code\Powermail\Domain\Model\Page::TABLE_NAME,
        \In2code\Powermail\Domain\Model\Field::TABLE_NAME,
        \In2code\Powermail\Domain\Model\Mail::TABLE_NAME,
        \In2code\Powermail\Domain\Model\Answer::TABLE_NAME
    ];
    foreach ($tables as $table) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            $table,
            'EXT:powermail/Resources/Private/Language/locallang_csh_' . $table . '.xlf'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages($table);
    }

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
});
