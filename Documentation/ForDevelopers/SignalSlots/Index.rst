

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


Signal Slots
^^^^^^^^^^^^

Powermail offers a lot of SignalSlots (Extbase pendant to Hooks) to
extend the functions from your extension. Please report to
forge.typo3.org if you need a new signal.

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
   :Description:
      Description

 - :Class:
      \\In2code\\Powermail\\Domain\\Validator\\CustomValidator
   :Name:
      isValid
   :File:
      CustomValidator.php
   :Method:
      isValid()
   :Description:
      Add your own serverside Validation

 - :Class:
      \\In2code\\Powermail\\Controller\\FormController
   :Name:
      formActionBeforeRenderView
   :File:
      FormController.php
   :Method:
      formAction()
   :Description:
      Slot is called before the form is rendered

 - :Class:
      \\In2code\\Powermail\\Controller\\FormController
   :Name:
      confirmationActionBeforeRenderView
   :File:
      FormController.php
   :Method:
      confirmationAction()
   :Description:
      Slot is called before the confirmation view is rendered

 - :Class:
      \\In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionBeforeRenderView
   :File:
      FormController.php
   :Method:
      createAction()
   :Description:
      Slot is called before the answered are stored and the mails are sent

 - :Class:
      \\In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionAfterMailDbSaved
   :File:
      FormController.php
   :Method:
      createAction()
   :Description:
      Slot ist called directly after the mail was stored in the db

 - :Class:
      \\In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionAfterSubmitView
   :File:
      FormController.php
   :Method:
      createAction()
   :Description:
      Slot is called after the thx message was rendered

 - :Class:
      \\In2code\\Powermail\\Controller\\FormController
   :Name:
      optinConfirmActionBeforeRenderView
   :File:
      FormController.php
   :Method:
      optinConfirmAction()
   :Description:
      Slot is called before the optin confirmation view is rendered (only if
      Double-Opt-In is in use)

 - :Class:
      \\In2code\\Powermail\\Controller\\FormController
   :Name:
      initializeObjectSettings
   :File:
      FormController.php
   :Method:
      initializeObject()
   :Description:
      Change Settings from Flexform or TypoScript before Action is called

 - :Class:
      \\In2code\\Powermail\\Utility\\SendMail
   :Name:
      createEmailBodyBeforeRender
   :File:
      SendMail.php
   :Method:
      createEmailBody()
   :Description:
      Change the body of the mails

Example Code
""""""""""""

Look at EXT:powermail/Resources/Private/Software/powermailextended.zip for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended\Classes\Controller\FormController.php)