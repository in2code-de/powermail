<?php
/*
 * Register necessary class names with autoloader
 */

$powermailExtPath = t3lib_extMgm::extPath('powermail');

$arr = array(

		/* ajax actions*/
	'tx_powermail_action' => $powermailExtPath . 'mod1/class.tx_powermail_action.php',

		/* ajax repositories */
	'tx_powermail_repository' => $powermailExtPath . 'mod1/class.tx_powermail_repository.php',
	'tx_powermail_export' => $powermailExtPath . 'mod1/class.tx_powermail_export.php',

		/* div */
	'tx_powermail_functions_div' => $powermailExtPath . 'lib/class.tx_powermail_functions_div.php',
	
		/* scheduler */
	'tx_powermail_scheduler' => $powermailExtPath . 'cli/class.tx_powermail_scheduler.php',
	'tx_powermail_scheduler_addFields' => $powermailExtPath . 'cli/class.tx_powermail_scheduler_addFields.php'

);

return $arr;
?>
