<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "powermail".
 *
 * Auto generated 12-02-2025 15:17
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
  'version' => '7.5.2',
  'state' => 'stable',
  'uploadfolder' => false,
  'createDirs' => 'uploads/tx_powermail,typo3temp/assets/tx_powermail',
  'clearcacheonload' => false,
  'author' => 'Powermail Development Team',
  'author_email' => 'alexander.kellner@in2code.de',
  'author_company' => 'in2code.de',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '8.7.0-9.5.99',
      'php' => '7.0.0-7.99.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  '_md5_values_when_last_written' => '',
);
