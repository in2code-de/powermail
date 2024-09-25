# PSR-14 EventDispatcher

## Overview

Powermail offers a lot of events (modern pendant to hooks) to
extend functions and properties with your extension.
Please report to https://github.com/einpraegsam/powermail/issues if you need a new event anywhere.

| Location | Event | Description |
| -------- | ----- | ----------- |
| `In2code\Powermail\Domain\Validator\CustomValidator::isValid()` | `In2code\Powermail\Events\CustomValidatorEvent` | Add your own serverside Validation |
| `In2code\Powermail\Controller\FormController::formAction()` | `In2code\Powermail\Events\FormControllerFormActionEvent` | Listeners are called before the form is rendered |
| `In2code\Powermail\Controller\FormController::createAction()` | `In2code\Powermail\Events\FormControllerCreateActionBeforeRenderViewEvent` | Listeners are called before the mail and answers are persisted and before the emails are sent |
| `In2code\Powermail\Controller\FormController::createAction()` | `In2code\Powermail\Events\FormControllerCreateActionAfterMailDbSavedEvent` | Listeners are called directly after the mail was persisted |
| `In2code\Powermail\Controller\FormController::createAction()` | `In2code\Powermail\Events\FormControllerCreateActionAfterSubmitViewEvent` | Listeners are called after the create message was rendered |
| `In2code\Powermail\Controller\FormController::createAction()` | `In2code\Powermail\Events\CheckIfMailIsAllowedToSaveEvent` | It is possible to deny saving of the mail with this event |
| `In2code\Powermail\Controller\FormController::confirmationAction()` | `In2code\Powermail\Events\FormControllerConfirmationActionEvent` | Listeners are called before the confirmation view is rendered |
| `In2code\Powermail\Controller\FormController::optinConfirmAction()` | `In2code\Powermail\Events\FormControllerOptinConfirmActionBeforeRenderViewEvent` | Listeners are called before the optin confirmation view is rendered (only if Double-Opt-In is in use) |
| `In2code\Powermail\Controller\FormController::initializeObject()` | `In2code\Powermail\Events\FormControllerInitializeObjectEvent` | Change Settings from Flexform or TypoScript before Action is called |
| `In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper::render()` | `In2code\Powermail\Events\PrefillFieldViewHelperEvent` | Prefill fields by your own magic |
| `In2code\Powermail\ViewHelpers\Misc\PrefillMultiFieldViewHelper::render()` | `In2code\Powermail\Events\PrefillMultiFieldViewHelperEvent` | Prefill multiple fields by your own magic |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService::setReceiverEmails()` | `In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent` | Manipulate receiver emails short before the mails will be send |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService::getReceiverName()` | `In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent` | Manipulate receiver name when getting it |
| `In2code\Powermail\Domain\Service\Mail\SendMailService::prepareAndSend()` | `In2code\Powermail\Events\SendMailServicePrepareAndSendEvent` | Change the message object before sending |
| `In2code\Powermail\Domain\Service\Mail\SendMailService::createEmailBody()` | `In2code\Powermail\Events\SendMailServiceCreateEmailBodyEvent` | Manipulate standaloneView-object before the mail object will be rendered |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService::setReceiverEmails()` | `In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent` | Manipulate given receiver email addresses |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesServicegetReceiverName()` | `In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceGetReceiverNameEvent` | Manipulate given receiver name |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService::getSenderEmail()` | `In2code\Powermail\Events\ReceiverMailSenderPropertiesGetSenderEmailEvent` | Manipulate given sender email addresses |
| `In2code\Powermail\Domain\Service\Mail\ReceiverMailSenderPropertiesService::getSenderName()` | `In2code\Powermail\Events\ReceiverMailSenderPropertiesGetSenderNameEvent` | Manipulate given sender name |
| `In2code\Powermail\Domain\Service\UploadService::preflight()` | `In2code\Powermail\Events\UploadServicePreflightEvent` | Change files from upload-fields before they will be validated, stored and send |
| `In2code\Powermail\Domain\Service\UploadService::getFiles()` | `In2code\Powermail\Events\UploadServiceGetFilesEvent` | Change files array from upload-fields whenever files will be read |
| `In2code\Powermail\Domain\Model\File::getNewPathAndFilename()` | `In2code\Powermail\Events\GetNewPathAndFilenameEvent` | Change path and filename of a single file for uploading, attaching to email or something else |
| `In2code\Powermail\ViewHelpers\Validation\ValidationDataAttributeViewHelper::render()` | `In2code\Powermail\Events\ValidationDataAttributeViewHelperEvent` | Useful if you want to hook into additionalAttributes and set your own attributes to fields |
| `In2code\Powermail\Domain\Repository\MailRepository::getVariablesWithMarkersFromMail()` | `In2code\Powermail\Events\MailRepositoryGetVariablesWithMarkersFromMailEvent` | If you want to register your own markers use this event |


## How to add a listener

There is a very good documentation how to work with EventDispatcher in TYPO3:
https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/Events/EventDispatcher/Index.html

### Example Code

Look at https://github.com/einpraegsam/powermailextended for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended/Classes/Controller/FormController.php)
