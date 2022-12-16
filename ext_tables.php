<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(
    function () {

        /**
         * Table description files for localization and allowing powermail tables on pages of type default
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
        $tables = [
            \In2code\Powermail\Domain\Model\Mail::TABLE_NAME,
            \In2code\Powermail\Domain\Model\Answer::TABLE_NAME
        ];
        foreach ($tables as $table) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][$tgct]['options']['tables'][$table] = [
                'dateField' => 'tstamp',
                'expirePeriod' => 30
            ];
        }

        /**
         * Search with TYPO3 backend search
         *      search for an email: "#mail:senderemail"
         *      search for a form: "#form:contactform"
         */
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch']['mail'] = 'tx_powermail_domain_model_mail';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch']['form'] = 'tx_powermail_domain_model_form';
    }
);
