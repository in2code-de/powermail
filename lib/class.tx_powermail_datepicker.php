<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 26.07.11
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */
 
class tx_powermail_datepicker {
	function datepicker($PA, $fobj) {
		return '<input name="' . $PA['itemFormElName'] . '" value="' . htmlspecialchars(($PA['itemFormElValue'] ? $PA['itemFormElValue'] : 'Default value')) . '" onchange="' . htmlspecialchars(implode('',$PA['fieldChangeFunc'])) . '"' . $PA['onFocus'] . ' />';
	}
}
