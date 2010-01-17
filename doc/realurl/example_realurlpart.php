<?
//This could be part of your realurl configuration:


$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT'] = array (
	'powermail' => array(
		array (
			'GETvar' => 'tx_powermail_pi1[mailID]'
		),
		array (
			'GETvar' => 'tx_powermail_pi1[sendNow]'
		)
	)
);