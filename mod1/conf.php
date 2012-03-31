<?php
if (!strstr($_SERVER['PHP_SELF'], 'typo3conf')) {
    $BACK_PATH='../../../../typo3/';
    define('TYPO3_MOD_PATH', '../typo3conf/ext/powermail/mod1/');
} else {
    $BACK_PATH='../../../../typo3/';
    define('TYPO3_MOD_PATH', '../typo3conf/ext/powermail/mod1/');
}

$MCONF['name'] = 'web_txpowermailM1';
	
$MCONF['access'] = 'user,group';
$MCONF['script'] = 'index.php';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref'] = 'LLL:EXT:powermail/mod1/locallang_mod.xml';
?>