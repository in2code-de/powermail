# Signal Slots

## Overview

Powermail offers a lot of SignalSlots (Extbase pendant to Hooks) to
extend the functions from your extension.
Please report to https://github.com/einpraegsam/powermail/issues if you need a new signal.

| Signal Class Name | Signal Name | Located in Method | Passed arguments | Description |
|-------------------|-------------|-------------------|------------------|-------------|
| `In2code\Powermail\Domain\Validator\CustomValidator` | isValid | isValid() | $mail, $this | Add your own serverside Validation |
| `In2code\Powermail\Controller\FormController` | formActionBeforeRenderView | formAction() | $form, $this | Slot is called before the form is rendered |
| `In2code\Powermail\Controller\FormController` | createActionBeforeRenderView | createAction() | $mail, $hash, $this | Slot is called before the mail and answers are persisted and before the emails are sent |
| `In2code\Powermail\Controller\FormController` | createActionAfterMailDbSaved | createAction() | $mail, $this | Slot ist called directly after the mail was persisted |
| `In2code\Powermail\Controller\FormController` | createActionAfterSubmitView | createAction() | $mail, $hash, $this | Slot is called after the create message was rendered |
| `In2code\Powermail\Controller\FormController` | confirmationActionBeforeRenderView | confirmationAction() | $mail, $this | Slot is called before the confirmation view is rendered |
| `In2code\Powermail\Controller\FormController` | optinConfirmActionBeforeRenderView | optinConfirmAction() | $mail, $hash, $this | Slot is called before the optin confirmation view is rendered (only if Double-Opt-In is in use) |
| `In2code\Powermail\Controller\FormController` | initializeObjectSettings | initializeObject() | $this, &$settings | Change Settings from Flexform or TypoScript before Action is called |
| `In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper` | render | render() | $field, $mail, $default, $this | Prefill fields by your own magic |
| `In2code\Powermail\ViewHelpers\Misc\PrefillMultiFieldViewHelper` | render | render() | $field, $mail, $cycle, $default, $this | Prefill multiple fields by your own magic |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService` | setReceiverEmails | setReceiverEmails() | &$emailArray, $this | Manipulate receiver emails short before the mails will be send |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService` | getReceiverName | getReceiverName() | &$receiverName, $this | Manipulate receiver name when getting it |
| `In2code\Powermail\Domain\Service\Mail\SendMailService` | sendTemplateEmailBeforeSend | prepareAndSend() | $message, &$email, $this | Change the message object before sending |
| `In2code\Powermail\Domain\Service\Mail\SendMailService` | createEmailBodyBeforeRender | createEmailBody() | $standaloneView, $email, $this | Manipulate standaloneView-object before the mail object will be rendered |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService` | setReceiverEmails | setReceiverEmails() | &$emailArray, $this | Manipulate given receiver email addresses |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService` | getReceiverName | getReceiverName() | &$receiverName, $this | Manipulate given receiver name |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService` | getSenderEmail | getSenderEmail() | &$senderEmail, $this | Manipulate given sender email addresses |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService` | getSenderName | getSenderName() | &$senderName, $this | Manipulate given sender name |
| `In2code\Powermail\Domain\Service\UploadService` | preflight | preflight() | $this | Change files from upload-fields before they will be validated, stored and send |
| `In2code\Powermail\Domain\Service\UploadService` | getFiles | getFiles() | $this | Change files array from upload-fields whenever files will be read |
| `In2code\Powermail\Domain\Model\File` | getNewPathAndFilename | getNewPathAndFilename() | $pathAndFilename, $this | Change path and filename of a single file for uploading, attaching to email or something else |
| `In2code\Powermail\ViewHelpers\Validation\ValidationDataAttributeViewHelper` | render | render() | &$additionalAttributes, $field, $iteration, $this | Useful if you want to hook into additionalAttributes and set your own attributes to fields |
| `In2code\Powermail\Domain\Repository\MailRepository` | getVariablesWithMarkersFromMail | getVariablesWithMarkersFromMail() | &$variables, $mail, $this | If you want to register your own markers use this signal |


## Example

### Introduction

Let's say you want to change the receiver email - short before powermail sends the mail.

Add a new extension to your system and use the signal createEmailBodyBeforeRender for example.
See following code.

### ext_emconf.php

```
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
```

### ext_localconf.php

```
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
```

### Classes/Domain/Service/SendMailService.php

```
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
```

### Example Code
""""""""""""

Look at https://github.com/einpraegsam/powermailextended for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended/Classes/Controller/FormController.php)
