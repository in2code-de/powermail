.. include:: Images.txt

Add new validation type
^^^^^^^^^^^^^^^^^^^^^^^

A new validation types is a validiation that can be selected for a single field by the editor.
The following example includes clientside and serverside validation.

Add new Option
~~~~~~~~~~~~~~

Per default, all standard validators are available for a field

|developer_new_validationtype1|

If you want to add a new validation, use Page TSConfig for this. In this case, we want to validate for a ZIP-Code which is greater than 79999 (for bavarian ZIP within Germany):
::

   tx_powermail.flexForm.validation.addFieldOptions.100 = Bavarian ZIP Code

This leads to a new validation option for the editors:

|developer_new_validationtype2|

Add new JavaScript Validation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You will see a HTML-Code like this for this field
::

   <input type="text" ... data-parsley-error-message="" data-parsley-custom100="80000" />

Add a new Extension or simply a JavaScript File with this content. Please pay attention to the ordering. This code must be included after all JavaScript of powermail.
::

   window.ParsleyValidator
   .addValidator('custom100', function (value, requirement) {
   	if (value >= 80000) {
   		return true;
   	}
   	return false;
   }, 32)
   .addMessage('en', 'custom100', 'Error');


See Extension powermailextended.zip in your powermail folder powermail/Resources/Private/Software/

Add new PHP Validation
~~~~~~~~~~~~~~~~~~~~~~

First of all, you have to register a PHP Class for your new validation via TypoScript (and an errormessage in case of a negative validation).
::

   plugin.tx_powermail {
    	settings.setup {
    		validation {
    			customValidation {
    				100 = In2code\Powermailextended\Domain\Validator\ZipValidator
    			}
    		}
    	}
    	_LOCAL_LANG.default.validationerror_validation.100 = Please add a ZIP with 8 begginning
   }

In this case we choose a further Extension "powermailextended" and add a new file and folders powermailextended/Classes/Domain/Validator/ZipValidator.php

The content:
::

	<?php
	namespace In2code\Powermailextended\Domain\Validator;

	/**
	* ZipValidator
	*/
	class ZipValidator
	{

		/**
		 * Check if given number is higher than in configuration
		 *
		 * @param string $value
		 * @param string $validationConfiguration
		 * @return bool
		 */
		public function validate100($value, $validationConfiguration)
		{
			if (is_numeric($value) && $value >= $validationConfiguration) {
				return TRUE;
			}
			return FALSE;
		}
	}


Example Code
""""""""""""

Look at https://github.com/einpraegsam/powermailextended for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended/Classes/Controller/FormController.php)
