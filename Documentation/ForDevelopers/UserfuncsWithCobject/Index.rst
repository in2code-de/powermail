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


userFuncs with cObject
^^^^^^^^^^^^^^^^^^^^^^


Introduction
""""""""""""

It's very easy to extend powermail with cObject Viewhelpers, which are
calling a userFunc from TypoScript.


Where can you use cObject Viewhelpers in powermail
""""""""""""""""""""""""""""""""""""""""""""""""""

- In every HTML Template (and Partial and Layout)

- In every RTE field in flexform

- In the subject, receiver, receiverName, sender, senderName field of
  the flexform


What can you do with this flexibility
"""""""""""""""""""""""""""""""""""""

If you need to call a userFunc after submitting a form or if you need
dynamic text in a view. Some examples:

- Create an extension for transaction keys in powermail

- Manipulate the receiver from a userFunc depending on a field value
  (see below)

- Log something special in a database table after a submit

- etc...


Example to manipulate the receiver of a form
""""""""""""""""""""""""""""""""""""""""""""


With pure TypoScript
~~~~~~~~~~~~~~~~~~~~


Example Call in Flexform Settings
'''''''''''''''''''''''''''''''''

|developer_cobject|

Example call in TypoScript
''''''''''''''''''''''''''

::

   # TypoScript Setup Example for ViewHelper {f:cObject(typoscriptObjectPath:'lib.test')}
   lib.test = TEXT
   lib.test.value = newReceiver@mail.com


With a userFunc on TypoScript depending on a field value
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


Example Call in Flexform Settings
'''''''''''''''''''''''''''''''''

|developer_cobject|

Example call in TypoScript
''''''''''''''''''''''''''

::

   # TypoScript Setup Example for ViewHelper {f:cObject(typoscriptObjectPath:'lib.test')}
   # Note: includeLibs does not work in TYPO3 8 any more. UserFuncs must be added in an own extension to use composer autoload.
   includeLibs.manipulatePowermailReceiver = typo3conf/ext/myext/Classes/ManipulateReceiver.php
   lib.test = USER
   lib.test.userFunc = Vendor\Myext\ManipulateReceiver->getEmail

Example PHP Script
''''''''''''''''''

::

   <?php
   namespace Vendor\Myext;

   class ManipulateReceiver
   {

           public function getEmail($content = '', $conf = array())
           {
                   $variables = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_powermail_pi1');
                   $email = 'email1@domain.org';

                   if ($variables['field']['firstname'] === 'Alex') {
                           $email = 'email2@domain.org';
                   }
                   return $email;
           }
   }


Extend FlexForm values
""""""""""""""""""""""


Add new fieldtypes to powermail
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Just use some lines of page TSConfig to add new fieldtypes to
powermail

::

   # Powermail will search for the Partial Newfield.html
   tx_powermail.flexForm.type.addFieldOptions.newfield = New Field Name
   tx_powermail.flexForm.type.addFieldOptions.new.dataType = 0
   tx_powermail.flexForm.type.addFieldOptions.new.export = 1



Add new validationtypes to powermail
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Just use some lines of page TSConfig to add new validationtypes to
powermail

::

   tx_powermail.flexForm.validation.addFieldOptions.newfield = New Validation Name


Add new fe\_user fields for prefill powermail fields
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Just use some lines of page TSConfig to add new fe\_user fields

::

   tx_powermail.flexForm.feUserProperty.addFieldOptions.newfield = New fe_user Property

