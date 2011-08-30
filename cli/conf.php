<?php

if (!strstr($_SERVER['PHP_SELF'], 'typo3conf')) {
	$BACK_PATH = '../../../';
	define('TYPO3_MOD_PATH', 'ext/powermail/cli/');
} else {
	$BACK_PATH = '../../../../typo3/';
	define('TYPO3_MOD_PATH', '../typo3conf/ext/powermail/cli/');
}

$MCONF['name'] = '_CLI_cronjob'; // BE-User name

?>