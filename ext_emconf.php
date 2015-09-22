<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "powermail".
 *
 * Auto generated 04-07-2013 17:03
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'powermail',
	'description' => 'Powermail is a well-known, editor-friendly, powerful
		and easy to use mailform extension with a lots of features
		(spam prevention, marketing information, optin, ajax submit, diagram analysis, etc...)',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '2.11.1',
	'dependencies' => 'cms,extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_powermail,typo3temp/tx_powermail',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Powermail Development Team',
	'author_email' => 'alexander.kellner@in2code.de',
	'author_company' => 'in2code.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-7.99.99',
			'extbase' => '6.2.0-7.99.99',
			'fluid' => '6.2.0-7.99.99',
			'cms' => '',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
	'_md5_values_when_last_written' => '',
);