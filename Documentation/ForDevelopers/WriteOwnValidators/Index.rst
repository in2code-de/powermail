.. include:: Images.txt

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)


.. _newvalidators:

Add new Validators
^^^^^^^^^^^^^^^^^^

Introduction
""""""""""""

Since powermail 2.1 a combination of different validation types is possible:

- Serverside Validation (PHP)
- Clientside Validation (JavaScript)
- Clientside Validation (Native with HTML5)

You can enable or disable or combine some of the validation via TypoScript

::

   plugin.tx_powermail.settings.setup {
   	    validation {
			native = 1
			client = 1
			server = 1
   	    }
   }

Parsley.js allows us to have a robust solution for JavaScript and Native HTML5 Validation

|img-serversidevalidation|

Serverside Validation Example


|img-clientsidevalidation|

Clientside Validation Example

Want to learn more about Parsley.js?
`http://parsleyjs.org/ <http://parsleyjs.org/>`_

Add own Validator
"""""""""""""""""

Add new Option
~~~~~~~~~~~~~~

Per default, all standard validators are available for a field

|img-validation1|

If you want to add a new validation, use Page TSConfig for this. In this case, we want to validate for a ZIP-Code which is greater than 79999 (for bavarian ZIP within Germany):
::

   tx_powermail.flexForm.validation.addFieldOptions.100 = Bavarian ZIP Code

This leads to a new validation option for the editors:

|img-validation2|

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
   class ZipValidator {

   	/**
   	 * Check if given number is higher than in configuration
   	 *
   	 * @param string $value
   	 * @param string $validationConfiguration
   	 * @return bool
   	 */
   	public function validate100($value, $validationConfiguration) {
   		if (is_numeric($value) && $value >= $validationConfiguration) {
   			return TRUE;
   		}
   		return FALSE;
   	}
   }

Example Code
""""""""""""

Look at EXT:powermail/Resources/Private/Software/powermailextended.zip for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended\Classes\Controller\FormController.php)

JavaScript Validation by Hand
"""""""""""""""""""""""""""""

Parsley Introduction
~~~~~~~~~~~~~~~~~~~~

Example form, validated with parsley.js, with a required and an email field. In addition to HTML5, this input fields are validated with parsley:
::

   <form data-parsley-validate>
        <input type="text" name="firstname" required="required" />

        <input type="email" name="email" />

        <input type="submit" />
   </form>


Own Parsley Validator
~~~~~~~~~~~~~~~~~~~~~

::

    <input type="text" data-parsley-multiple="3" data-parsley-error-message="Please try again" />
        [...]
    <script type="text/javascript">
        window.ParsleyValidator
            .addValidator('multiple', function (value, requirement) {
                return 0 === value % requirement;
            }, 32)
            .addMessage('en', 'multiple', 'This value should be a multiple of %s');
    </script>



PHP Validation by Hand
""""""""""""""""""""""

Introduction
""""""""""""

You can also use the CustomValidator (used twice in powermail
FormsController: confirmationAction and createAction) to write your
own field validation after a form submit.

The customValidator is located at
powermail/Classes/Domain/Validator/CustomValidator.php. A signalSlot
Dispatcher within the class waits for your extension.


SignalSlot in CustomValidator
"""""""""""""""""""""""""""""

.. t3-field-list-table::
 :header-rows: 1

 - :Class:
      Signal Class Name
   :Name:
      Signal Name
   :File:
      Located in File
   :Method:
      Located in Method

 - :Class:
      \\In2code\\Powermail\\Domain\\Validator\\CustomValidator
   :Name:
      isValid
   :File:
      CustomValidator.php
   :Method:
      isValid()

Call the Custom Validator from your Extension
"""""""""""""""""""""""""""""""""""""""""""""

Add a new extension (example key powermail_extend).

Example ext_localconf.php:

::

   $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
   $signalSlotDispatcher->connect(
        'In2code\Powermail\Domain\Validator\CustomValidator',
        'isValid',
        'Vendor\Extkey\Domain\Validator\CustomValidator',
        'addInformation',
        FALSE
   );

Example file:

::

   class \Vendor\Extkey\Domain\Validator\CustomValidator {
           public function addInformation($params, $obj) {
                   // $field failed - set error
                   $obj->setErrorAndMessage($field, 'error message');
           }
   }


