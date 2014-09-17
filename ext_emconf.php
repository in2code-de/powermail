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
	'description' => '
		Powermail is a well-known,
		powerful and easy to use mailform extension with a lots
		of features (spam prevention, marketing, double-optin, etc...)
	',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '2.0.18',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_powermail',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Powermail dev team',
	'author_email' => 'alexander.kellner@in2code.de',
	'author_company' => 'in2code.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array (
		'depends' => array (
			'typo3' => '4.6.0-6.2.99',
			'extbase' => '1.4.0-6.2.99',
			'fluid' => '1.4.0-6.2.99',
		),
		'conflicts' => array (
		),
		'suggests' => array (
		),
	),
);