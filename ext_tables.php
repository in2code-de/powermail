<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

/**
 * Include Plugins
 */
// Pi1
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('powermail', 'Pi1', 'Powermail');
// Pi2
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('powermail', 'Pi2', 'Powermail_Frontend');

/**
 * Disable non needed fields in tt_content
 */
$TCA['tt_content']['types']['list']['subtypes_excludelist']['powermail_pi1'] = 'select_key,pages,recursive';

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
            'icon' => 'EXT:powermail/ext_icon.svg',
            'labels' => 'LLL:EXT:powermail/Resources/Private/Language/locallang_mod.xlf',
        )
    );
}

/**
 * Include Flexform
 */
// Pi1
$TCA['tt_content']['types']['list']['subtypes_addlist']['powermail_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'powermail_pi1',
    'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi1.xml'
);

// Pi2
$TCA['tt_content']['types']['list']['subtypes_addlist']['powermail_pi2'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'powermail_pi2',
    'FILE:EXT:powermail/Configuration/FlexForms/FlexformPi2.xml'
);

/**
 * ContentElementWizard for Pi1
 */
$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['In2code\Powermail\Hook\ContentElementWizard'] =
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('powermail') . 'Classes/Hook/ContentElementWizard.php';

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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    \In2code\Powermail\Domain\Model\Form::TABLE_NAME,
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_form.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    \In2code\Powermail\Domain\Model\Form::TABLE_NAME
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    \In2code\Powermail\Domain\Model\Page::TABLE_NAME,
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_page.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    \In2code\Powermail\Domain\Model\Page::TABLE_NAME
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    \In2code\Powermail\Domain\Model\Field::TABLE_NAME,
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_field.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    \In2code\Powermail\Domain\Model\Field::TABLE_NAME
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    \In2code\Powermail\Domain\Model\Mail::TABLE_NAME,
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_mail.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    \In2code\Powermail\Domain\Model\Mail::TABLE_NAME
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    \In2code\Powermail\Domain\Model\Answer::TABLE_NAME,
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_answer.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    \In2code\Powermail\Domain\Model\Answer::TABLE_NAME
);

/**
 * Garbage Collector
 */
if (\In2code\Powermail\Utility\ConfigurationUtility::isEnableTableGarbageCollectionActive()) {
    $tgct = 'TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask';
    $table = \In2code\Powermail\Domain\Model\Mail::TABLE_NAME;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][$tgct]['options']['tables'][$table] = array(
        'dateField' => 'tstamp',
        'expirePeriod' => 30
    );
    $table = \In2code\Powermail\Domain\Model\Answer::TABLE_NAME;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][$tgct]['options']['tables'][$table] = array(
        'dateField' => 'tstamp',
        'expirePeriod' => 30
    );
}
