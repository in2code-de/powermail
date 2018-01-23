

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
`GitHub <https://github.com/einpraegsam/powermail/issues>`_ if you need a new signal.

.. t3-field-list-table::
 :header-rows: 1

 - :Class:
      Signal Class Name
   :Name:
      Signal Name
   :Method:
      Located in Method
   :Arguments:
      Passed arguments
   :Description:
      Description

 - :Class:
      In2code\\Powermail\\Domain\\Validator\\CustomValidator
   :Name:
      isValid
   :Method:
      isValid()
   :Arguments:
      $mail, $this
   :Description:
      Add your own serverside Validation

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      formActionBeforeRenderView
   :Method:
      formAction()
   :Arguments:
      $form, $this
   :Description:
      Slot is called before the form is rendered

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionBeforeRenderView
   :Method:
      createAction()
   :Arguments:
      $mail, $hash, $this
   :Description:
      Slot is called before the mail and answers are persisted and before the emails are sent

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionAfterMailDbSaved
   :Method:
      createAction()
   :Arguments:
      $mail, $this
   :Description:
      Slot ist called directly after the mail was persisted

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      createActionAfterSubmitView
   :Method:
      createAction()
   :Arguments:
      $mail, $hash, $this
   :Description:
      Slot is called after the create message was rendered

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      confirmationActionBeforeRenderView
   :Method:
      confirmationAction()
   :Arguments:
      $mail, $this
   :Description:
      Slot is called before the confirmation view is rendered

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      optinConfirmActionBeforeRenderView
   :Method:
      optinConfirmAction()
   :Arguments:
      $mail, $hash, $this
   :Description:
      Slot is called before the optin confirmation view is rendered (only if
      Double-Opt-In is in use)

 - :Class:
      In2code\\Powermail\\Controller\\FormController
   :Name:
      initializeObjectSettings
   :Method:
      initializeObject()
   :Arguments:
      $this
   :Description:
      Change Settings from Flexform or TypoScript before Action is called

 - :Class:
      In2code\\Powermail\\ViewHelpers\\Misc\\PrefillFieldViewHelper
   :Name:
      render
   :Method:
      render()
   :Arguments:
      $field, $mail, $default, $this
   :Description:
      Prefill fields by your own magic

 - :Class:
      In2code\\Powermail\\ViewHelpers\\Misc\\PrefillMultiFieldViewHelper
   :Name:
      render
   :Method:
      render()
   :Arguments:
      $field, $mail, $cycle, $default, $this
   :Description:
      Prefill multiple fields by your own magic

 - :Class:
      In2code\\Powermail\\Domain\\Service\\ReceiverMailReceiverPropertiesService
   :Name:
      setReceiverEmails
   :Method:
      setReceiverEmails()
   :Arguments:
      &$emailArray, $this
   :Description:
      Manipulate receiver emails short before the mails will be send

 - :Class:
      In2code\\Powermail\\Domain\\Service\\Mail\\ReceiverMailReceiverPropertiesService
   :Name:
      getReceiverName
   :Method:
      getReceiverName()
   :Arguments:
      &$receiverName, $this
   :Description:
      Manipulate receiver name when getting it

 - :Class:
      In2code\\Powermail\\Domain\\Service\\Mail\\SendMailService
   :Name:
      sendTemplateEmailBeforeSend
   :Method:
      prepareAndSend()
   :Arguments:
      $message, &$email, $this
   :Description:
      Change the message object before sending

 - :Class:
         In2code\\Powermail\\Domain\\Service\\Mail\\SendMailService
   :Name:
      createEmailBodyBeforeRender
   :Method:
      createEmailBody()
   :Arguments:
      $standaloneView, $email, $this
   :Description:
      Manipulate standaloneView-object before the mail object will be rendered

 - :Class:
      In2code\\Powermail\\Domain\\Service\\Mail\\ReceiverMailReceiverPropertiesService
   :Name:
      setReceiverEmails
   :Method:
      setReceiverEmails()
   :Arguments:
      &$emailArray, $this
   :Description:
      Manipulate given receiver email addresses

 - :Class:
      In2code\\Powermail\\Domain\\Service\\Mail\\ReceiverMailReceiverPropertiesService
   :Name:
      getReceiverName
   :Method:
      getReceiverName()
   :Arguments:
      &$receiverName, $this
   :Description:
      Manipulate given receiver name

 - :Class:
      In2code\\Powermail\\Domain\\Service\\Mail\\ReceiverMailSenderPropertiesService
   :Name:
      getSenderEmail
   :Method:
      getSenderEmail()
   :Arguments:
      &$senderEmail, $this
   :Description:
      Manipulate given sender email addresses

 - :Class:
      In2code\\Powermail\\Domain\\Service\\Mail\\ReceiverMailSenderPropertiesService
   :Name:
      getSenderName
   :Method:
      getSenderName()
   :Arguments:
      &$senderName, $this
   :Description:
      Manipulate given sender name

 - :Class:
      In2code\\Powermail\\Domain\\Service\\UploadService
   :Name:
      preflight
   :Method:
      preflight()
   :Arguments:
      $this
   :Description:
      Change files from upload-fields before they will be validated, stored and send

 - :Class:
      In2code\\Powermail\\Domain\\Service\\UploadService
   :Name:
      getFiles
   :Method:
      getFiles()
   :Arguments:
      $this
   :Description:
      Change files array from upload-fields whenever files will be read

 - :Class:
      In2code\\Powermail\\Domain\\Model\\File
   :Name:
      getNewPathAndFilename
   :Method:
      getNewPathAndFilename()
   :Arguments:
      $pathAndFilename, $this
   :Description:
      Change path and filename of a single file for uploading, attaching to email or something else

 - :Class:
      In2code\\Powermail\\ViewHelpers\\Validation\\ValidationDataAttributeViewHelper
   :Name:
      render
   :Method:
      render()
   :Arguments:
      &$additionalAttributes, $field, $iteration, $this
   :Description:
      Useful if you want to hook into additionalAttributes and set your own attributes to fields

 - :Class:
      In2code\\Powermail\\Domain\\Repository\\MailRepository
   :Name:
      getVariablesWithMarkersFromMail
   :Method:
      getVariablesWithMarkersFromMail()
   :Arguments:
      &$variables, $mail, $this
   :Description:
      If you want to register your own markers use this signal

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
        'description' => 'Sample Extension to extend powermail',
        'category' => 'plugin',
        'version' => '1.0.0',
        // ...
        'constraints' => array(
            'depends' => array(
                'typo3' => '7.6.1-8.99.99',
                'powermail' => '3.0.0-3.99.99',
            ),
            'conflicts' => array(),
            'suggests' => array(),
        ),
    );


ext_localconf.php
~~~~~~~~~~~~~~~~~

::

    <?php
    /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
    );
    $signalSlotDispatcher->connect(
        'In2code\Powermail\Domain\Service\Mail\SendMailService',
        'sendTemplateEmailBeforeSend',
        'In2code\Powermailextended\Domain\Service\Mail\SendMailService',
        'manipulateMail',
        FALSE
    );


Classes/Domain/Service/SendMailService.php
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    <?php
    namespace In2code\Powermailextended\Domain\Service\Mail;

    use In2code\Powermail\Domain\Service\Mail\SendMailService as SendMailServicePowermail;
    use TYPO3\CMS\Core\Mail\MailMessage;

    /**
     * SendMailService
     *
     * @package powermailextend
     */
    class SendMailService
    {

        /**
         * Manipulate message object short before powermail send the mail
         *
         * @param MailMessage $message
         * @param array $email
         * @param SendMailServicePowermail $originalService
         */
        public function manipulateMail($message, &$email, SendMailServicePowermail $originalService)
        {
            // overwrite the receiver in the email array to have it saved correctly
            $email['receiverName'] = 'John Mega';
            $email['receiverEmail'] = 'john@mega.com';

            $message->setTo([$email['receiverEmail'] => $email['receiverName']]);
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
