

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

Overview
""""""""

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
      In2code\\Powermail\\Domain\\Validator\\CustomValidator
   :Name:
      isValid
   :File:
      CustomValidator.php
   :Method:
      isValid()
   :Description:
      Add your own serverside Validation

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      formActionBeforeRenderView
   :File:
      FormController.php
   :Method:
      formAction()
   :Description:
      Slot is called before the form is rendered

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      confirmationActionBeforeRenderView
   :File:
      FormController.php
   :Method:
      confirmationAction()
   :Description:
      Slot is called before the confirmation view is rendered

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionBeforeRenderView
   :File:
      FormController.php
   :Method:
      createAction()
   :Description:
      Slot is called before the answered are stored and the mails are sent

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionAfterMailDbSaved
   :File:
      FormController.php
   :Method:
      createAction()
   :Description:
      Slot ist called directly after the mail was stored in the db

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionAfterSubmitView
   :File:
      FormController.php
   :Method:
      createAction()
   :Description:
      Slot is called after the thx message was rendered

 - :Class:
      In2code\\Powermail\\Controller\\FormController
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
      In2code\\Powermail\\Controller\\FormController
   :Name:
      initializeObjectSettings
   :File:
      FormController.php
   :Method:
      initializeObject()
   :Description:
      Change Settings from Flexform or TypoScript before Action is called

 - :Class:
      In2code\\Powermail\\Utility\\SendMail
   :Name:
      sendTemplateEmailBeforeSend
   :File:
      SendMail.php
   :Method:
      sendTemplateEmail()
   :Description:
      Change the emails before sending

 - :Class:
      In2code\\Powermail\\Utility\\SendMail
   :Name:
      createEmailBodyBeforeRender
   :File:
      SendMail.php
   :Method:
      createEmailBody()
   :Description:
      Change the body of the mails

Example
"""""""

Introduction
~~~~~~~~~~~~

Let's say you want to change the receiver email - short before powermail sends the mail.

Add a new extension to your system and use the signal createEmailBodyBeforeRender for example.
See following code.

ext_emconf.php
~~~~~~~~~~~~~~

::

    <?php
    $EM_CONF[$_EXTKEY] = array (
        'title' => 'powermailextended',
        'description' => 'Sample Extension to extend powermail 2.1',
        'category' => 'plugin',
        'version' => '2.1.0',
        // ...
        'constraints' => array(
            'depends' => array(
                'typo3' => '6.2.0-6.2.99',
                'powermail' => '2.1.0-2.1.99',
            ),
            'conflicts' => array(),
            'suggests' => array(),
        ),
    );


ext_localconf.php
~~~~~~~~~~~~~~~~~

::

    <?php
    // enable SignalSlot
    /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
    $signalSlotDispatcher->connect(
        'In2code\Powermail\Utility\SendMail',
        'sendTemplateEmailBeforeSend',
        'In2code\Powermailextended\Utility\SendMail',
        'manipulateMail',
        FALSE
    );


Classes/Utility/SendMail.php
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    <?php
    namespace In2code\Powermailextended\Utility;

    /**
     * SendMail
     *
     * @package powermailextend
     */
    class SendMail {

        /**
         * @param \TYPO3\CMS\Core\Mail\MailMessage $message
         * @param array $email
         * @param \In2code\Powermail\Domain\Model\Mail $mail
         * @param array $settings
         * @param string $type Email to "sender" or "receiver"
         */
        public function manipulateMail($message, $email, $mail, $settings, $type) {
            $message->setTo(array('anotheremail@domain.org' => 'receiverName'));
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