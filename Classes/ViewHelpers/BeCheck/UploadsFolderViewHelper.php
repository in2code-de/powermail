<?php

/**
 * Backend Check Viewhelper: Check if uploads folder exists
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_BeCheck_UploadsFolderViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Upload Filder
	 *
	 * @var		string
	 */
	public $folder = 'uploads/tx_powermail/';

    /**
     * Check if uploads folder exists
     *
     * @return 	boolean
     */
    public function render() {
		return file_exists(t3lib_div::getFileAbsFileName($this->folder));
    }
}

?>