<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

/**
 * Include Plugins
 */
// Pi1
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin($_EXTKEY, 'Pi1', 'Powermail');
// Pi2
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin($_EXTKEY, 'Pi2', 'Powermail_Frontend');

/**
 * Disable non needed fields in tt_content
 */
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'select_key,pages,recursive';

/**
 * Include Backend Module
 */
if (
    TYPO3_MODE === 'BE' &&
    !\In2code\Powermail\Utility\ConfigurationUtility::isDisableBackendModuleActive() &&
    !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)
) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'In2code.' . $_EXTKEY,
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
            'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.' .
                (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'svg' : 'gif'),
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xlf',
        )
    );
}

/**
 * Include Flexform
 */
// Pi1
$fileName = 'FlexformPi1.xml';
if (!\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.6')) {
    $fileName = 'FlexformPi1Old.xml';
}
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_pi1';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/' . $fileName
);

// Pi2
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_pi2';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/FlexformPi2.xml'
);

/**
 * ContentElementWizard for Pi1
 */
$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['In2code\Powermail\Utility\Hook\ContentElementWizard'] =
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) .
    'Classes/Utility/Hook/ContentElementWizard.php';

/**
 * Include TypoScript
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/Main',
    'Main Template'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/Powermail_Frontend',
    'Powermail_Frontend'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/CssDemo',
    'Add Demo CSS'
);
if (!\In2code\Powermail\Utility\ConfigurationUtility::isDisableMarketingInformationActive()) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $_EXTKEY,
        'Configuration/TypoScript/Marketing',
        'Marketing Information'
    );
}

/**
 * Table Configuration
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_powermail_domain_model_forms',
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_forms.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_powermail_domain_model_forms');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_powermail_domain_model_pages',
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_pages.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_powermail_domain_model_pages');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_powermail_domain_model_fields',
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_fields.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_powermail_domain_model_fields');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_powermail_domain_model_mails',
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_mails.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_powermail_domain_model_mails');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_powermail_domain_model_answers',
    'EXT:powermail/Resources/Private/Language/locallang_csh_tx_powermail_domain_model_answers.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_powermail_domain_model_answers');

/**
 * Garbage Collector
 */
if (\In2code\Powermail\Utility\ConfigurationUtility::isEnableTableGarbageCollectionActive()) {
    $tgct = 'TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask';
    $table = 'tx_powermail_domain_model_mails';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][$tgct]['options']['tables'][$table] = array(
        'dateField' => 'tstamp',
        'expirePeriod' => 30
    );
    $table = 'tx_powermail_domain_model_answers';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][$tgct]['options']['tables'][$table] = array(
        'dateField' => 'tstamp',
        'expirePeriod' => 30
    );
}
