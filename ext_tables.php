<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

call_user_func(function () {

    /**
     * Include Plugins
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('powermail', 'Pi1', 'Powermail');
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('powermail', 'Pi2', 'Powermail_Frontend');

    /**
     * Disable not needed fields in tt_content
     */
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['powermail_pi1'] = 'select_key,pages,recursive';

    /**
     * Include Backend Module
     */
    if (
        TYPO3_MODE === 'BE' &&
        !\In2code\Powermail\Utility\ConfigurationUtility::isDisableBackendModuleActive() &&
        !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)
    ) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'In2code.powermail',
            'web',
            'm1',
            '',
            array(
                'Module' => 'dispatch, list, exportXls, exportCsv, reportingBe, toolsBe, overviewBe, ' .
                    'checkBe, converterBe, converterUpdateBe, reportingFormBe, reportingMarketingBe, ' .
                    'fixUploadFolder, fixWrongLocalizedForms, fixFilledMarkersInLocalizedFields, ' .
                    'fixWrongLocalizedPages, fixFilledMarkersInLocalizedPages'
            ),
            array(
                'access' => 'user,group',
                'icon' => 'EXT:powermail/Resources/Public/Icons/powermail.svg',
                'labels' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_mod.xlf',
            )
        );
    }

    /**
     * Include Flexform
     */
    // Pi1
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['powermail_pi1'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'powermail_pi1',
        'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi1.xml'
    );

    // Pi2
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['powermail_pi2'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'powermail_pi2',
        'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi2.xml'
    );

    /**
     * ContentElementWizard for Pi1
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:powermail/Configuration/TSConfig/ContentElementWizard.typoscript">'
    );

    /**
     * Include TypoScript
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'powermail',
        'Configuration/TypoScript/Main',
        'Main Template'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'powermail',
        'Configuration/TypoScript/Powermail_Frontend',
        'Powermail_Frontend'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'powermail',
        'Configuration/TypoScript/BootstrapClassesAndLayout',
        'Add classes and CSS based on bootstrap'
    );
    if (!\In2code\Powermail\Utility\ConfigurationUtility::isDisableMarketingInformationActive()) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            'powermail',
            'Configuration/TypoScript/Marketing',
            'Marketing Information'
        );
    }

    /**
     * Table Configuration
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
     * Garbage Collector
     */
    $tgct = 'TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask';
    $tables = [\In2code\Powermail\Domain\Model\Mail::TABLE_NAME, \In2code\Powermail\Domain\Model\Answer::TABLE_NAME];
    foreach ($tables as $table) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][$tgct]['options']['tables'][$table] = [
            'dateField' => 'tstamp',
            'expirePeriod' => 30
        ];
    }

    /**
     * Register icons
     */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'extension-powermail-main',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:powermail/Resources/Public/Icons/powermail.svg']
    );

    /**
     * Search with TYPO3 backend search
     *      search for an email: "#mail:senderemail"
     *      search for a form: "#form:contactform"
     */
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch']['mail'] = 'tx_powermail_domain_model_mail';
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch']['form'] = 'tx_powermail_domain_model_form';

});
