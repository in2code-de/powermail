<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 26.07.11
 * Time: 08:09
 */
class tx_powermail_datepicker_hook {
	function getSingleField_preProcess($table, $field, $row, $altName, $palette, $extra, $pal, $pObj) {
		global $pmpreprocounter;
		$pmpreprocounter++;
		if($table == 'tx_powermail_fields' && $pmpreprocounter <= 1) {
			t3lib_div::devlog('getSingleField_preProcess', 'powermail', 0, $row);
			t3lib_div::devlog('Tabelle: ' . $table, 'powermail', 0);
		}
	}

	function getSingleField_postProcess($table, $field, $row, $out, $PA, $pObj) {
		global $pmpostprocounter;
		$pmpostprocounter++;
		if($table == 'tx_powermail_fields' && $pmpostprocounter <= 1) {
			t3lib_div::devlog('getSingleField_postProcess', 'powermail', 0, $row);
		}
	}
}
?>
