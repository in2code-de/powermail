.. include:: ../../../Includes.txt

.. _mainTypoScript:

Main TypoScript
---------------

Constants Overview
^^^^^^^^^^^^^^^^^^

.. t3-field-list-table::
 :header-rows: 1

 - :Constants:
      Constants (should be prefixed with plugin.tx_powermail.settings.)
   :Description:
      Description
   :Type:
      Type
   :Default:
      Default

 - :Constants:
      main.pid
   :Description:
      Storage PID: Save mails in a defined Page (normally set via Flexform)
   :Type:
      int+
   :Default:


 - :Constants:
      main.form
   :Description:
      Form Uid: Commaseparated list of forms to show (normally set via Flexform)
   :Type:
      text
   :Default:


 - :Constants:
      main.confirmation
   :Description:
      Confirmation Page Active: Activate Confirmation Page (normally set via Flexform)
   :Type:
      bool
   :Default:


 - :Constants:
      main.optin
   :Description:
      Double Optin Active: Activate Double Optin for Mail sender (normally set via Flexform)
   :Type:
      bool
   :Default:


 - :Constants:
      main.moresteps
   :Description:
      Morestep Active: Activate Morestep Forms (normally set via Flexform)
   :Type:
      bool
   :Default:


 - :Constants:
      validation.native
   :Description:
      Native Browser Validation: Validate User Input with HTML5 native browser validation on clientside
   :Type:
      bool
   :Default:
      1

 - :Constants:
      validation.client
   :Description:
      Native Browser Validation: JavaScript Browser Validation: Validate User Input with JavaScript on clientside
   :Type:
      bool
   :Default:
      1

 - :Constants:
      validation.server
   :Description:
      PHP Server Validation: Validate User Input with PHP on serverside
   :Type:
      bool
   :Default:
      1

 - :Constants:
      receiver.enable
   :Description:
      Receiver Mail: Enable Email to Receiver
   :Type:
      bool
   :Default:
      1

 - :Constants:
      receiver.attachment
   :Description:
      Receiver Attachments: Add uploaded files to emails
   :Type:
      bool
   :Default:
      1

 - :Constants:
      receiver.mailformat
   :Description:
      Receiver Mail Format: Change mail format
   :Type:
      options[both,html,plain]
   :Default:
      both

 - :Constants:
      receiver.default.senderName
   :Description:
      Default Sender Name: Sendername if no sender name given
   :Type:
      text
   :Default:

 - :Constants:
      receiver.default.senderEmail
   :Description:
      Default Sender Email: Sender-email if no sender email given
   :Type:
      text
   :Default:

 - :Constants:
      receiver.overwrite.email
   :Description:
      Receiver overwrite Email: Commaseparated list of mail receivers overwrites flexform settings (e.g. receiver1@mail.com, receiver1@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.name
   :Description:
      Receiver overwrite Name: Receiver Name overwrites flexform settings (e.g. Receiver Name)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.senderName
   :Description:
      Receiver overwrite SenderName: Sender Name for mail to receiver overwrites flexform settings (e.g. Sender Name)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.senderEmail
   :Description:
      Receiver overwrite SenderEmail: Sender Email for mail to receiver overwrites flexform settings (e.g. sender@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.subject
   :Description:
      Receiver overwrite Mail Subject: Subject for mail to receiver overwrites flexform settings (e.g. New Mail from website)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.cc
   :Description:
      Receiver CC Email Addresses: Commaseparated list of cc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.bcc
   :Description:
      Receiver BCC Email Addresses: Commaseparated list of bcc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.returnPath
   :Description:
      Receiver Mail Return Path: Return Path for emails to receiver (e.g. return@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.replyToEmail
   :Description:
      Receiver Mail Reply Mail: Reply Email address for mail to receiver (e.g. reply@mail.com) (Note: replyToName is also required for this feature)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.replyToName
   :Description:
      Receiver Mail Reply Name: Reply Name for mail to receiver (e.g. Mr. Reply) (Note: replyToEmail is also required for this feature)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.priority
   :Description:
      Receiver Mail Priority: Set mail priority for mail to receiver (e.g. 3)
   :Type:
      options[1,2,3,4,5]
   :Default:
      3

 - :Constants:
      receiver.senderHeader.email
   :Description:
      Server-Mail: If set, the Mail-Header Sender is set (RFC 2822 - 3.6.2 Originator fields)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.senderHeader.name
   :Description:
      Server-Mail: If set, the Mail-Header Sender is set (RFC 2822 - 3.6.2 Originator fields)
   :Type:
      text
   :Default:


 - :Constants:
      sender.enable
   :Description:
      Sender Mail: Enable Email to Sender
   :Type:
      bool
   :Default:
      1

 - :Constants:
      sender.attachment
   :Description:
      Sender Attachments: Add uploaded files to emails
   :Type:
      bool
   :Default:
      0

 - :Constants:
      sender.mailformat
   :Description:
      Sender Mail Format: Change mail format
   :Type:
      options[both,html,plain]
   :Default:
      both

 - :Constants:
      sender.addDisclaimerLink
   :Description:
      Add disclaimer link: Add disclaimer link to the sender email (also in optin mail)
   :Type:
      bool
   :Default:
      1

 - :Constants:
      sender.default.senderName
   :Description:
      Default Sender Name: Sendername if no sender name given
   :Type:
      text
   :Default:
      Powermail

 - :Constants:
      sender.overwrite.email
   :Description:
      Sender overwrite Email: Commaseparated list of mail receivers overwrites flexform settings (e.g. receiver1@mail.com, receiver1@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.name
   :Description:
      Sender overwrite Name: Receiver Name overwrites flexform settings (e.g. Receiver Name)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.senderName
   :Description:
      Sender overwrite SenderName: Sender Name for mail to sender overwrites flexform settings (e.g. Sender Name)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.senderEmail
   :Description:
      Sender overwrite SenderEmail: Sender Email for mail to sender overwrites flexform settings (e.g. sender@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.subject
   :Description:
      Sender overwrite Mail Subject: Subject for mail to sender overwrites flexform settings (e.g. Thx for your mail)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.cc
   :Description:
      Sender CC Email Addresses: Commaseparated list of cc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.bcc
   :Description:
      Sender BCC Email Addresses: Commaseparated list of bcc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.returnPath
   :Description:
      Sender Mail Return Path: Return Path for emails to sender (e.g. return@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.replyToEmail (Note: replyToName is also required for this feature)
   :Description:
      Sender Mail Reply Mail: Reply Email address for mail to sender (e.g. reply@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.replyToName (Note: replyToEmail is also required for this feature)
   :Description:
      Sender Mail Reply Name: Reply Name for mail to sender (e.g. Mr. Reply)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.priority
   :Description:
      Sender Mail Priority: Set mail priority for mail to sender (e.g. 3)
   :Type:
      options[1,2,3,4,5]
   :Default:
      3

 - :Constants:
      sender.senderHeader.email
   :Description:
      Server-Mail: If set, the Mail-Header Sender is set (RFC 2822 - 3.6.2 Originator fields)
   :Type:
      text
   :Default:


 - :Constants:
      sender.senderHeader.name
   :Description:
      Server-Name: you can define a name along with the mail address (optional)
   :Type:
      text
   :Default:


 - :Constants:
      db.enable
   :Description:
      Mail Storage enabled: Store Mails in database
   :Type:
      bool
   :Default:
      1

 - :Constants:
      db.hidden
   :Description:
      Hidden Mails in Storage: Add mails with hidden flag (e.g. 1)
   :Type:
      bool
   :Default:
      0

 - :Constants:
      marketing.enable
   :Description:
      Enable Google Conversion: Enable JavaScript for google conversion - This is interesting if you want to track every submit in your Google Adwords account for a complete conversion.
   :Type:
      bool
   :Default:
      0

 - :Constants:
      marketing.google_conversion_id
   :Description:
      Enable Google Conversion: Enable JavaScript for google conversion - This is interesting if you want to track every submit in your Google Adwords account for a complete conversion.
   :Type:
      int+
   :Default:
      1234567890

 - :Constants:
      marketing.google_conversion_label
   :Description:
      Google Conversion Label: Add your google conversion label (see www.google.com/adwords for details)
   :Type:
      text
   :Default:
      abcdefghijklmnopqrs

 - :Constants:
      marketing.google_conversion_language
   :Description:
      Google Conversion Language: Add your google conversion language (see www.google.com/adwords for details)
   :Type:
      text
   :Default:
      en

 - :Constants:
      misc.htmlForHtmlFields
   :Description:
      Allow html in html fields: Per default output of fields of type HTML is parsed through a htmlspecialchars() function to avoid Cross-Site-Scripting for security reasons. If you are aware of possible XSS-problems, caused by editors, you can enable it and your original HTML is shown in the Frontend.
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.htmlForLabels
   :Description:
      Allow html in field labels: Per default labels are generated with htmlspecialchars() to prevent xss. This also disables links in labels. If you aware of possible XSS-problems, caused by editors, you can enable it.
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.showOnlyFilledValues
   :Description:
      Show only filled values: If the user submits a form, even not filled values are viewable. If you only want to show labels with filled values, use this setting
   :Type:
      bool
   :Default:
      1

 - :Constants:
      misc.ajaxSubmit
   :Description:
      AJAX Submit Form: Submit Powermail Forms with AJAX (browser will not reload complete page)
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.addQueryString
   :Description:
      Enable AddQueryString: Keep GET-params in form Action (e.g. to use powermail on a tx_news detail page)
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.uploadFolder
   :Description:
      Misc Upload Folder: Define the folder where files should be uploaded with upload fields (e.g. fileadmin/uploads/)
   :Type:
      bool
   :Default:
      uploads/tx_powermail/

 - :Constants:
      misc.uploadSize
   :Description:
      Misc Upload Filesize: Define the maximum filesize of file uploads in bytes (10485760 Byte -> 10 MB)
   :Type:
      int+
   :Default:
      10485760

 - :Constants:
      misc.uploadFileExtensions
   :Description:
      Misc Upload Fileextensions: Define the allowed filetypes with their extensions for fileuploads and separate them with commas (e.g. jpg,jpeg,gif)
   :Type:
      text
   :Default:
      jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,flv,mpg,mpeg,avi,mp3,zip,rar,ace,csv

 - :Constants:
      misc.randomizeFileName
   :Description:
      Randomized Filenames: Uploaded filenames can be randomized to respect data privacy
   :Type:
      bool
   :Default:
      1

 - :Constants:
      misc.forceJavaScriptDatePicker
   :Description:
      Force JavaScript Datepicker: Per default html5 Date or Datetime format is used. If you don't want to use it and want to have the same datepicker all over all browsers, you can enable this feature
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.debugSettings
   :Description:
      Debug Settings: Show all Settings from TypoScript, Flexform and Global Config in Devlog
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.debugVariables
   :Description:
      Debug Variables: Show all given Plugin variables from GET or POST in Devlog
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.debugMail
   :Description:
      Debug Mails: Show all mail values in Devlog
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.debugSaveToTable
   :Description:
      Debug Save to Table: Show all values if you want to save powermail variables to another table in Devlog
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.debugSpamshield
   :Description:
      Debug Spamshield: Show Spamshield Functions in Devlog
   :Type:
      bool
   :Default:
      0

 - :Constants:
      spamshield.enable
   :Description:
      SpamShield Active: En- or disable Spamshield for Powermail
   :Type:
      bool
   :Default:
      1

 - :Constants:
      spamshield.factor
   :Description:
      Spamshield Spamfactor in %: Set limit for spamfactor in powermail forms in % (e.g. 85)
   :Type:
      int+
   :Default:
      75

 - :Constants:
      spamshield.email
   :Description:
      Spamshield Notifymail: Admin can get an email if he/she wants to get informed if a mail failed. Let this field empty and no mail will be sent (e.g. admin@mail.com)
   :Type:
      text
   :Default:

 - :Constants:
      spamshield.emailTemplate
   :Description:
      Spamshield Notifymail Template: Template for notification Email to Admin
   :Type:
      text
   :Default:
      EXT:powermail/Resources/Private/Templates/Mail/SpamNotification.html
      
 - :Constants:
      spamshield.senderEmail
   :Description:
      Spamshield Notifymail sender Email address: Define a specific Email address as sender of the notification Email
   :Type:
      text
   :Default:
      'powermail@' + the TYPO3 host (e.g. powermail@www.example.com)

 - :Constants:
      spamshield.logfileLocation
   :Description:
      Spamshield Log Template Location: Path of log file, ie. typo3temp/logs/powermail_spam.log, if empty, logging is deactivated
   :Type:
      text
   :Default:

 - :Constants:
      spamshield.logTemplate
   :Description:
      Spamshield Log Template: Template for entries written to log file
   :Type:
      text
   :Default:
      EXT:powermail/Resources/Private/Templates/Log/SpamNotification.html


 - :Constants:
      captcha.image
   :Description:
      Captcha Background: Set own captcha background image (e.g. fileadmin/bg.png)
   :Type:
      text
   :Default:
      EXT:powermail/Resources/Private/Image/captcha_bg.png

 - :Constants:
      captcha.font
   :Description:
      Captcha Font: Set TTF-Font for captcha image (e.g. fileadmin/font.ttf)
   :Type:
      text
   :Default:
      EXT:powermail/Resources/Private/Fonts/ARCADE.TTF

 - :Constants:
      captcha.textColor
   :Description:
      Captcha Text Color: Define your text color in hex code - must start with # (e.g. #ff0000)
   :Type:
      text
   :Default:
      #444444

 - :Constants:
      captcha.textSize
   :Description:
      Captcha Text Size: Define your text size in px (e.g. 24)
   :Type:
      int+
   :Default:
      32

 - :Constants:
      captcha.textAngle
   :Description:
      Captcha Text Angle: Define two different values (start and stop) for your text random angle and separate it with a comma (e.g. -10,10)
   :Type:
      text
   :Default:
      -5,5

 - :Constants:
      captcha.distanceHor
   :Description:
      Captcha Text Distance Hor: Define two different values (start and stop) for your text horizontal random distance and separate it with a comma (e.g. 20,80)
   :Type:
      text
   :Default:
      20,80

 - :Constants:
      captcha.distanceVer
   :Description:
      Captcha Text Distance Ver: Define two different values (start and stop) for your text vertical random distance and separate it with a comma (e.g. 30,60)
   :Type:
      text
   :Default:
      30,60

 - :Constants:
      javascript.addJQueryFromGoogle
   :Description:
      Include jQuery From Google: Add jQuery JavaScript (will be loaded from ajax.googleapis.com)
   :Type:
      bool
   :Default:
      0

 - :Constants:
      javascript.addAdditionalJavaScript
   :Description:
      Include additional JavaScrpt: Add additional JavaScript and CSS Files (form validation, datepicker, etc...)
   :Type:
      bool
   :Default:
      1

 - :Constants:
      javascript.powermailJQuery
   :Description:
      jQuery Source: Change jQuery Source - per default it will be loaded from googleapis.com
   :Type:
      text
   :Default:
      //ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js

 - :Constants:
      styles.framework.numberOfColumns
   :Description:
      Number of columns for responsive frontend columns. 0 disables this function completely.
   :Type:
      int+
   :Default:
      0

 - :Constants:
      styles.framework.rowClasses
   :Description:
      Framework classname(s) for containers to build rows
   :Type:
      text
   :Default:
      row

 - :Constants:
      styles.framework.formClasses
   :Description:
      Framework classname(s) for form "form-horizontal"
   :Type:
      text
   :Default:
      \-

 - :Constants:
      styles.framework.fieldAndLabelWrappingClasses
   :Description:
      Framework classname(s) for overall wrapping container of a field/label pair e.g. "row form-group"
   :Type:
      text
   :Default:
      \-

 - :Constants:
      styles.framework.fieldWrappingClasses
   :Description:
      Framework classname(s) for wrapping container of a field e.g. "row form-group"
   :Type:
      text
   :Default:
      powermail_field

 - :Constants:
      styles.framework.labelClasses
   :Description:
      Framework classname(s) for fieldlabels e.g. "col-md-2 col-md-3"
   :Type:
      text
   :Default:
      powermail_label

 - :Constants:
      styles.framework.fieldClasses
   :Description:
      Framework classname(s) for fields e.g. "form-control"
   :Type:
      text
   :Default:
      \-

 - :Constants:
      styles.framework.offsetClasses
   :Description:
      Framework classname(s) for fields with an offset e.g. "col-sm-offset-2"
   :Type:
      text
   :Default:
      \-

 - :Constants:
      styles.framework.radioClasses
   :Description:
      Framework classname(s) especially for radiobuttons e.g. "radio"
   :Type:
      text
   :Default:
      radio

 - :Constants:
      styles.framework.checkClasses
   :Description:
      Framework classname(s) especially for checkboxes e.g. "check"
   :Type:
      text
   :Default:
      checkbox


Setup
^^^^^

.. code-block:: text

    ##################
    # Frontend Plugin
    ##################
    plugin.tx_powermail {
        view {
            templateRootPaths {
                0 = EXT:powermail/Resources/Private/Templates/
                1 = {$plugin.tx_powermail.view.templateRootPath}
            }
            partialRootPaths {
                0 = EXT:powermail/Resources/Private/Partials/
                1 = {$plugin.tx_powermail.view.partialRootPath}
            }
            layoutRootPaths {
                0 = EXT:powermail/Resources/Private/Layouts/
                1 = {$plugin.tx_powermail.view.layoutRootPath}
            }
        }

        # Modify localization of labels
    #	_LOCAL_LANG {
    #		default {
    #			confirmation_message = Are these values correct?
    #		}
    #		de {
    #			confirmation_message = Sind diese Eingaben korrekt?
    #		}
    #	}

        # Main settings
        settings {
            setup {

                main {
                    pid = {$plugin.tx_powermail.settings.main.pid}
                    form = {$plugin.tx_powermail.settings.main.form}
                    confirmation = {$plugin.tx_powermail.settings.main.confirmation}
                    optin = {$plugin.tx_powermail.settings.main.optin}
                    moresteps = {$plugin.tx_powermail.settings.main.moresteps}
                }

                validation {
                    # enable native HTML5 validation
                    native = {$plugin.tx_powermail.settings.validation.native}

                    # enable clientside validation
                    client = {$plugin.tx_powermail.settings.validation.client}

                    # enable serverside validation
                    server = {$plugin.tx_powermail.settings.validation.server}

                    unique {
                        # Enable unique check for {email}
                        #email = 1

                        # Enable a max limit of 3 times for the same entry for {event}
                        #event = 3
                    }

                    ##########################################################
                    # CUSTOMVALIDATION EXAMPLE
                    #
                    # E.g. Validation was extended with Page TSconfig
                    # 		tx_powermail.flexForm.validation.addFieldOptions.100 = New Validation
                    #
                    # Register your Class and Method with TypoScript Setup
                    # 		plugin.tx_powermail.settings.setup.validation.customValidation.100 =
                    # 			In2code\Powermailextended\Domain\Validator\ZipValidator
                    #
                    # Add method to your class
                    # 		validate100($value, $validationConfiguration)
                    #
                    # Define your Errormessage with TypoScript Setup
                    # 		plugin.tx_powermail._LOCAL_LANG.default.validationerror_validation.100 = Error happens!
                    #
                    # ##########################################################
                    customValidation {
    #					100 = In2code\Powermailextended\Domain\Validator\ZipValidator
                    }
                }

                # All settings for mail to receiver
                receiver {
                    enable = {$plugin.tx_powermail.settings.receiver.enable}

                    # Following settings are normally set via Flexform
                    email =
                    subject =
                    body =

                    # add file attachments from upload fields
                    attachment = {$plugin.tx_powermail.settings.receiver.attachment}

                    # html, plain, both
                    mailformat = {$plugin.tx_powermail.settings.receiver.mailformat}

                    default {
                        senderName = TEXT
                        senderName.value = {$plugin.tx_powermail.settings.receiver.default.senderName}

                        senderEmail = TEXT
                        senderEmail.value = {$plugin.tx_powermail.settings.receiver.default.senderEmail}
                    }

                    # Predefine some receivers - selection in backend could be done via page TSConfig:
                    #		tx_powermail.flexForm.predefinedReceivers.addFieldOptions.receivers1 = receiver list #1
                    #			or with a locallang variable:
                    #		tx_powermail.flexForm.predefinedReceivers.addFieldOptions.receivers1 = LLL:fileadmin/locallang.xlf:key
                    predefinedReceiver {
                        # example for hard coded receivers
    #					receivers1 {
    #						email = TEXT
    #						email.value = email1@domain.org, email2@domain.org
    #					}

                        # example for dynamic receiver - depending on value in field {receiver}
    #					receivers2 {
    #						email = CASE
    #						email {
    #							key.data = GP:tx_powermail_pi1|field|receiver

    #							1 = TEXT
    #							1.value = email1@domain.org

    #							2 = TEXT
    #							2.value = email2@domain.org
    #						}
    #					}
                    }

                    # Normally you do not need to overwrite a flexform setting, but this allows you to use cObject functions
                    overwrite {
                        email = TEXT
                        email.value = {$plugin.tx_powermail.settings.receiver.overwrite.email}

                        name = TEXT
                        name.value = {$plugin.tx_powermail.settings.receiver.overwrite.name}

                        senderName = TEXT
                        senderName.value = {$plugin.tx_powermail.settings.receiver.overwrite.senderName}

                        senderEmail = TEXT
                        senderEmail.value = {$plugin.tx_powermail.settings.receiver.overwrite.senderEmail}

                        subject = TEXT
                        subject.value = {$plugin.tx_powermail.settings.receiver.overwrite.subject}

                        # Add further CC Receivers (split them via comma)
                        cc = TEXT
                        cc.value = {$plugin.tx_powermail.settings.receiver.overwrite.cc}

                        # Add further BCC Receivers (split them via comma)
                        bcc = TEXT
                        bcc.value = {$plugin.tx_powermail.settings.receiver.overwrite.bcc}

                        # Add return path
                        returnPath = TEXT
                        returnPath.value = {$plugin.tx_powermail.settings.receiver.overwrite.returnPath}

                        # Reply address (both required)
                        replyToEmail = TEXT
                        replyToEmail.value = {$plugin.tx_powermail.settings.receiver.overwrite.replyToEmail}
                        replyToName = TEXT
                        replyToName.value = {$plugin.tx_powermail.settings.receiver.overwrite.replyToName}

                        # Set mail priority from 1 to 5
                        priority = {$plugin.tx_powermail.settings.receiver.overwrite.priority}
                    }

                    # Add additional attachments to the mail (separate each with comma)
    #				addAttachment = TEXT
    #				addAttachment.value = fileadmin/file.jpg
    #				addAttachment.wrap = |,

                    # Mail Header "Sender:" see RFC 2822 - 3.6.2 Originator fields f.e. webserver@example.com, leave empty if you do not want to set a Sender-Header
                    senderHeader.email = TEXT
                    senderHeader.email.value = {$plugin.tx_powermail.settings.receiver.senderHeader.email}
                    # optional: f.e. Webserver
                    senderHeader.name = TEXT
                    senderHeader.name.value = {$plugin.tx_powermail.settings.receiver.senderHeader.name}
                }

                # All settings for mail to user
                sender {
                    enable = {$plugin.tx_powermail.settings.sender.enable}

                    # Following settings are normally set via Flexform
                    name =
                    email =
                    subject =
                    body =

                    # add file attachments from upload fields
                    attachment = {$plugin.tx_powermail.settings.sender.attachment}

                    # html, plain, both
                    mailformat = {$plugin.tx_powermail.settings.sender.mailformat}

                    addDisclaimerLink = {$plugin.tx_powermail.settings.sender.addDisclaimerLink}

                    default {
                        senderEmail = TEXT
                        senderEmail.value = {$plugin.tx_powermail.settings.sender.default.senderEmail}

                        senderName = TEXT
                        senderName.value = {$plugin.tx_powermail.settings.sender.default.senderName}
                    }

                    # Normally you do not need to overwrite a flexform settings, but this allows you to use cObject functions
                    overwrite {
                        email = TEXT
                        email.value = {$plugin.tx_powermail.settings.sender.overwrite.email}

                        name = TEXT
                        name.value = {$plugin.tx_powermail.settings.sender.overwrite.name}

                        senderName = TEXT
                        senderName.value = {$plugin.tx_powermail.settings.sender.overwrite.senderName}

                        senderEmail = TEXT
                        senderEmail.value = {$plugin.tx_powermail.settings.sender.overwrite.senderEmail}

                        subject = TEXT
                        subject.value = {$plugin.tx_powermail.settings.sender.overwrite.subject}

                        # Add further CC Receivers (split them via comma)
                        cc = TEXT
                        cc.value = {$plugin.tx_powermail.settings.sender.overwrite.cc}

                        # Add further BCC Receivers (split them via comma)
                        bcc = TEXT
                        bcc.value = {$plugin.tx_powermail.settings.sender.overwrite.bcc}

                        # Add return path
                        returnPath = TEXT
                        returnPath.value = {$plugin.tx_powermail.settings.sender.overwrite.returnPath}

                        # Reply address (both required)
                        replyToEmail = TEXT
                        replyToEmail.value = {$plugin.tx_powermail.settings.sender.overwrite.replyToEmail}
                        replyToName = TEXT
                        replyToName.value = {$plugin.tx_powermail.settings.sender.overwrite.replyToName}

                        # Set mail priority from 1 to 5
                        priority = {$plugin.tx_powermail.settings.sender.overwrite.priority}
                    }

                    # Add additional attachments to the mail (separate each with comma)
    #				addAttachment = TEXT
    #				addAttachment.value = fileadmin/file.jpg
    #				addAttachment.wrap = |,

                    # Mail Header "Sender:" see RFC 2822 - 3.6.2 Originator fields f.e. webserver@example.com, leave empty if you do not want to set a Sender-Header
                    senderHeader.email = TEXT
                    senderHeader.email.value = {$plugin.tx_powermail.settings.sender.senderHeader.email}
                    # optional: f.e. Webserver
                    senderHeader.name = TEXT
                    senderHeader.name.value = {$plugin.tx_powermail.settings.sender.senderHeader.name}
                }

                thx {
                    # Following settings are normally set via Flexform
                    body =
                    redirect =

                    overwrite {
                        # Overwrite redirect with TypoScript cObject
                        # 	Return a Number: Typolink to the pid
                        # 	Return a URL: Link to an intern or extern URL
                        # 	Return a File: Link to a file (within fileadmin folder)
    #					redirect = COA
    #					redirect {
    #						10 = TEXT
    #						10 {
    #							typolink.parameter = 2
    #							typolink.returnLast = url
    #							typolink.additionalParams = &x=y
    #						}
    #					}
                    }
                }

                db {
                    # Enable mail storage
                    enable = {$plugin.tx_powermail.settings.db.enable}

                    # Add new mails with hidden=1
                    hidden = {$plugin.tx_powermail.settings.db.hidden}
                }

                optin {
                    subject = TEXT
                    subject.data = LLL:EXT:powermail/Resources/Private/Language/locallang.xlf:optin_subject

                    overwrite {
    #					email = TEXT
    #					email.value = alexander.kellner@in2code.de

    #					name = TEXT
    #					name.value = Receivers Name

    #					senderName = TEXT
    #					senderName.value = Sender Name

    #					senderEmail = TEXT
    #					senderEmail.value = sender@mail.com
                    }
                }

                disclaimer {
                    subject = TEXT
                    subject.data = LLL:EXT:powermail/Resources/Private/Language/locallang.xlf:disclaimed_subject
                }




                # Captcha Settings
                captcha {
                    # Select "default" (on board calculating captcha) or "captcha" (needs extension captcha)
                    use = default

                    default {
                        image = {$plugin.tx_powermail.settings.captcha.image}
                        font = {$plugin.tx_powermail.settings.captcha.font}
                        textColor = {$plugin.tx_powermail.settings.captcha.textColor}
                        textSize = {$plugin.tx_powermail.settings.captcha.textSize}
                        textAngle = {$plugin.tx_powermail.settings.captcha.textAngle}
                        distanceHor = {$plugin.tx_powermail.settings.captcha.distanceHor}
                        distanceVer = {$plugin.tx_powermail.settings.captcha.distanceVer}

                        # You can force a fix captcha - operator must be "+" (for testing only with calculating captcha)
    #					forceValue = 1+1
                    }
                }


                # Spam Settings
                spamshield {
                    # enable or disable spam check
                    _enable = {$plugin.tx_powermail.settings.spamshield.enable}

                    # disable complete spam check on individual conditions (overrules ._enable=1)
    #				_disable {
    #					1 {
                            # Disable spamcheck if visitor is in IP-Range
    #						class = In2code\Powermail\Domain\Validator\SpamShield\Breaker\IpBreaker
    #						configuration {
    #							// Commaseparated list of IPs. Use * for wildcards in the IP-address
    #							ipWhitelist = 127.0.0.1,192.168.0.*
    #						}
    #					}

    #					2 {
                            # Disable spamcheck if any field contains a given value - like "powermailTestCase"
    #						class = In2code\Powermail\Domain\Validator\SpamShield\Breaker\ValueBreaker
    #						configuration {
    #							value = powermailTestCase
    #						}
    #					}
    #				}

                    # Spam Factor Limit in %
                    factor = {$plugin.tx_powermail.settings.spamshield.factor}

                    # Notification Email to Admin if spam recognized (empty disables email to admin)
                    email = {$plugin.tx_powermail.settings.spamshield.email}

                    # Email address sending out spam mail. Set this if your mail transport limits allowed sender addresses
                    senderEmail = {$plugin.tx_powermail.settings.spamshield.senderEmail}

                    # Subject for notification Email to Admin
                    emailSubject = {$plugin.tx_powermail.settings.spamshield.emailSubject}

                    # Template for notification Email to Admin
                    emailTemplate = {$plugin.tx_powermail.settings.spamshield.emailTemplate}

                    # Path to logfile
                    logfileLocation = {$plugin.tx_powermail.settings.spamshield.logfileLocation}

                    # Template for logging entry
                    logTemplate = {$plugin.tx_powermail.settings.spamshield.logTemplate}

                    methods {
                        # Honeypot check
                        1 {
                            _enable = 1

                            # Spamcheck name
                            name = Honey Pot

                            # Class
                            class = In2code\Powermail\Domain\Validator\SpamShield\HoneyPodMethod

                            # if this check failes - add this indication value to indicator (0 disables this check completely)
                            indication = 5

                            # method configuration
                            configuration {
                            }
                        }

                        # Link check
                        2 {
                            _enable = 1

                            # Spamcheck name
                            name = Link check

                            # Class
                            class = In2code\Powermail\Domain\Validator\SpamShield\LinkMethod

                            # if this check failes - add this indication value to indicator (0 disables this check completely)
                            indication = 3

                            # method configuration
                            configuration {
                                # number of allowed links
                                linkLimit = 2
                            }
                        }

                        # Name check
                        3 {
                            _enable = 1

                            # Spamcheck name
                            name = Name check

                            # Class
                            class = In2code\Powermail\Domain\Validator\SpamShield\NameMethod

                            # if this check failes - add this indication value to indicator (0 disables this check completely)
                            indication = 3

                            # method configuration
                            configuration {
                            }
                        }

                        # Session check: Enabling session check means to store a cookie on form load. If forms are submitted powermail checks for that cookie again. If this check is disabled, powermail will not set a cookie by default.
                        4 {
                            _enable = 0

                            # Spamcheck name
                            name = Session check

                            # Class
                            class = In2code\Powermail\Domain\Validator\SpamShield\SessionMethod

                            # if this check failes - add this indication value to indicator (0 disables this check completely)
                            indication = 5

                            # method configuration
                            configuration {
                            }
                        }

                        # Unique check
                        5 {
                            _enable = 1

                            # Spamcheck name
                            name = Unique check

                            # Class
                            class = In2code\Powermail\Domain\Validator\SpamShield\UniqueMethod

                            # if this check failes - add this indication value to indicator (0 disables this check completely)
                            indication = 2

                            # method configuration
                            configuration {
                            }
                        }

                        # Value blacklist check
                        6 {
                            _enable = 1

                            # Spamcheck name
                            name = Value blacklist check

                            # Class
                            class = In2code\Powermail\Domain\Validator\SpamShield\ValueBlacklistMethod

                            # if this check failes - add this indication value to indicator (0 disables this check completely)
                            indication = 7

                            # method configuration
                            configuration {
                                # Blacklisted values (could also get read from a file - simply with FLUIDTEMPLATE)
                                values = TEXT
                                values.value = viagra,sex,porn,p0rn
                            }
                        }

                        # IP blacklist check
                        7 {
                            _enable = 1

                            # Spamcheck name
                            name = IP blacklist check

                            # Class
                            class = In2code\Powermail\Domain\Validator\SpamShield\IpBlacklistMethod

                            # if this check failes - add this indication value to indicator (0 disables this check completely)
                            indication = 7

                            # method configuration
                            configuration {
                                # Blacklisted values (could also get read from a file - simply with FLUIDTEMPLATE)
                                values = TEXT
                                values.value = 123.132.125.123,123.132.125.124
                            }
                        }
                    }
                }



                # Misc Settings
                misc {
                    # HTML Output for type HMTL fields
                    htmlForHtmlFields = {$plugin.tx_powermail.settings.misc.htmlForHtmlFields}

                    # HTML for labels
                    htmlForLabels = {$plugin.tx_powermail.settings.misc.htmlForLabels}

                    # Show only values if they are filled (for all views and for mails)
                    showOnlyFilledValues = {$plugin.tx_powermail.settings.misc.showOnlyFilledValues}

                    # Submit Powermail Forms with AJAX (browser will not reload complete page)
                    ajaxSubmit = {$plugin.tx_powermail.settings.misc.ajaxSubmit}

                    # Keep third-party GET/POST variables on submit with addQueryString="1" in form
                    addQueryString = {$plugin.tx_powermail.settings.misc.addQueryString}

                    # File upload settings
                    file {
                        folder = {$plugin.tx_powermail.settings.misc.uploadFolder}
                        size = {$plugin.tx_powermail.settings.misc.uploadSize}
                        extension = {$plugin.tx_powermail.settings.misc.uploadFileExtensions}
                        randomizeFileName = {$plugin.tx_powermail.settings.misc.randomizeFileName}
                    }

                    datepicker {
                        # Per default html5 Date or Datetime format is used. If you don't want to use it and want to have the same datepicker all over all browsers, you can enable this feature
                        forceJavaScriptDatePicker = {$plugin.tx_powermail.settings.misc.forceJavaScriptDatePicker}
                    }
                }



                # Prefill fields with their marker - e.g. {firstname} (Fields available for prefill: input, textarea, select, select multi, radio, checkbox)
                prefill {
                    # example: fill with string
    #				firstname = Alex

                    # example: fill with TypoScript
    #				email = TEXT
    #				email.value = alex@in2code.de
    #				email.wrap = <b>|</b>

                    # example: fill checkboxes or multiselect with more values
    #				category.0 = TEXT
    #				category.0.value = IT
    #				category.1 = TEXT
    #				category.1.value = Real Estate

                    # example: fill with value from Field Record
                        # available: uid, title, type, settings, css, feuserValue, mandatory, marker, pid, prefillValue, senderEmail, senderName, sorting, validation
    #				comment = TEXT
    #				comment.field = type
                }



                # Exclude values from {powermail_all} by markername or fieldtype
                excludeFromPowermailAllMarker {
                    # On Confirmation Page (if activated)
                    confirmationPage {
                        # add some markernames (commaseparated) which should be excluded (e.g. firstname, email, referrer)
                        excludeFromMarkerNames =

                        # add some fieldtypes (commaseparated) which should be excluded (e.g. hidden, captcha)
                        excludeFromFieldTypes =
                    }

                    # On Submitpage
                    submitPage {
                        # add some markernames (commaseparated) which should be excluded (e.g. firstname, email, referrer)
                        excludeFromMarkerNames =

                        # add some fieldtypes (commaseparated) which should be excluded (e.g. hidden, captcha)
                        excludeFromFieldTypes =
                    }

                    # In Mail to receiver
                    receiverMail {
                        # add some markernames (commaseparated) which should be excluded (e.g. firstname, email, referrer)
                        excludeFromMarkerNames =

                        # add some fieldtypes (commaseparated) which should be excluded (e.g. hidden, captcha)
                        excludeFromFieldTypes =
                    }

                    # In Mail to sender (if activated)
                    senderMail {
                        # add some markernames (commaseparated) which should be excluded (e.g. firstname, email, referrer)
                        excludeFromMarkerNames =

                        # add some fieldtypes (commaseparated) which should be excluded (e.g. hidden, captcha)
                        excludeFromFieldTypes =
                    }

                    # In double-opt-in Mail to sender (if activated)
                    optinMail {
                        # add some markernames (commaseparated) which should be excluded (e.g. firstname, email, referrer)
                        excludeFromMarkerNames =

                        # add some fieldtypes (commaseparated) which should be excluded (e.g. hidden, captcha)
                        excludeFromFieldTypes =
                    }
                }



                # Manipulate values from {powermail_all} by markername
                manipulateVariablesInPowermailAllMarker {
                    # On Confirmation Page (if activated)
                    confirmationPage {
                        # manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
    #					markerName = CASE
    #					markerName {
    #						key.field = value
    #
    #						1 = TEXT
    #						1.value = red
    #
    #						default = TEXT
    #						default.value = blue
    #					}
                    }

                    # On Submitpage
                    submitPage {
                        # manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
    #					markerName = CASE
    #					markerName {
    #						key.field = value
    #
    #						1 = TEXT
    #						1.value = red
    #
    #						default = TEXT
    #						default.value = blue
    #					}
                    }

                    # In Mail to receiver
                    receiverMail {
                        # manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
    #					markerName = CASE
    #					markerName {
    #						key.field = value
    #
    #						1 = TEXT
    #						1.value = red
    #
    #						default = TEXT
    #						default.value = blue
    #					}
                    }

                    # In Mail to sender (if activated)
                    senderMail {
                        # manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
    #					markerName = CASE
    #					markerName {
    #						key.field = value
    #
    #						1 = TEXT
    #						1.value = red
    #
    #						default = TEXT
    #						default.value = blue
    #					}
                    }

                    # In double-opt-in Mail to sender (if activated)
                    optinMail {
                        # manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
    #					markerName = CASE
    #					markerName {
    #						key.field = value
    #
    #						1 = TEXT
    #						1.value = red
    #
    #						default = TEXT
    #						default.value = blue
    #					}
                    }
                }



                # Save submitted values in a session to prefill forms for further visits. Define each markername for all forms.
                saveSession {
                    # Method "temporary" means as long as the browser is open. "permanently" could be used together with a frontend-user session. If method is empty, saveSession is deactivated.
    #				_method = temporary
    #
    #				firstname = TEXT
    #				firstname.field = firstname
    #
    #				lastname = TEXT
    #				lastname.field = lastname
                }



                marketing {

                    # Use Google Adwords Conversion JavaScript on form submit
                    googleAdwords {
                        _enable = {$plugin.tx_powermail.settings.marketing.enable}
                        google_conversion_id = {$plugin.tx_powermail.settings.marketing.google_conversion_id}
                        google_conversion_label = {$plugin.tx_powermail.settings.marketing.google_conversion_label}
                        google_conversion_language = {$plugin.tx_powermail.settings.marketing.google_conversion_language}
                        google_conversion_format = 3
                    }

                    # Send Form values to a third party software (like a CRM - e.g. salesforce or eloqua)
                    sendPost {
                        # Activate sendPost (0/1)
                        _enable = TEXT
                        _enable.value = 0

                        # Target URL for POST values (like http://www.target.com/target.php)
                        targetUrl = http://eloqua.com/e/f.aspx

                        # Basic Auth Protection - leave empty if Target is not protected
                        username =
                        password =

                        # build your post values like &param1=value1&param2=value2
                        values = COA
                        values {
                            10 = TEXT
                            10 {
                                # value from field {firstname}
                                field = vorname
                                wrap = &firstname=|
                            }

                            20 = TEXT
                            20 {
                                # value from field {e_mail}
                                field = e_mail
                                wrap = &email=|
                            }

                            30 = TEXT
                            30 {
                                # value from field {comment}
                                field = comment
                                wrap = &text=|
                            }
                        }

                        # activate debug - log all configuration from curl settings to devlog (use extension devlog to view this values)
                        debug = 0
                    }
                }




                # Save values to any table (see following example)
                dbEntry {

                    #####################################################
                    ### EXAMPLE for adding values to table tt_address ###
                    #####################################################

                    1 {
                        # Enable or disable db entry for table tt_address
    #					_enable = TEXT
    #					_enable.value = 1

                        # Set tableName to "tt_address"
    #					_table = TEXT
    #					_table.value = tt_address

                        # Write only if any field is not yet filled with current value (e.g. test if an email is already in database)
                            # default: always add new records (don't care about existing values)
                            # update: update record if there is an existing entry (e.g. if email is already there)
                            # none: no entry if field is filled (do nothing if record already exists)
    #					_ifUnique.email = update

                        # optional: add additional where clause (only in mode "update") for search if a record still exists. You could use a plain string (see example below) or a cObject if needed
    #					_ifUniqueWhereClause = AND pid = 123

                        # Fill tt_address.email with a static value => mail@mail.com
    #					email = TEXT
    #					email.value = mail@mail.com

                        # Fill tt_address.pid with the current pid (e.g. 12)
    #					pid = TEXT
    #					pid.data = TSFE:id

                        # Fill tt_address.tstamp with the current time as timestamp (like 123456789)
    #					tstamp = TEXT
    #					tstamp.data = date:U

                        # Fill tt_address.address with the current formatted time (like "Date: 20.01.2013")
    #					address = TEXT
    #					address.data = date:U
    #					address.strftime = Date: %d.%m.%Y

                        # Fill tt_address.name with the value from powermail {firstname}
    #					name = TEXT
    #					name.field = firstname

                        # Fill tt_address.last_name with the value from powermail {lastname}
    #					last_name = TEXT
    #					last_name.field = lastname

                        # Fill tt_address.company with the value from powermail {company}
    #					company = TEXT
    #					company.field = company

                        # Fill tt_address.position with the uid of the mail record
    #					position = TEXT
    #					position.field = uid


                    }


                    ##############################################################
                    ### EXAMPLE for building a relation to tt_address_group    ###
                    ### over the MM table tt_address_group_mm                  ###
                    ### Add relation to an existing address group with uid 123 ###
                    ##############################################################

                    2 {
                        # Enable or disable db entry for table tt_address_group_mm
    #					_enable = TEXT
    #					_enable.value = 1

                        # Set tableName to "tt_address_group_mm"
    #					_table = TEXT
    #					_table.value = tt_address_group_mm

                        # Fill tt_address_group_mm.uid_local with uid of tt_address record from above configuration 1. (usage .field=uid_[key])
    #					uid_local = TEXT
    #					uid_local.field = uid_1

                        # Fill new record of table "tt_address_group_mm" with field "uid_foreign" with uid 123
    #					uid_foreign = TEXT
    #					uid_foreign.value = 123
                    }
                }




                # Add own validator classes that will be called before create action (if you want to validate user input with own PHP classes)
                validators {
    #				1 {
                        # Classname that should be called with method *Validator()
    #					class = Vendor\Ext\Domain\Model\DoSomethingValidator

                        # optional: Add configuration for your PHP
    #					config {
    #						foo = bar

    #						fooCObject = TEXT
    #						fooCObject.value = do something with this text
    #					}

                        # optional: If file will not be loaded from autoloader, add path and it will be called with require_once
    #					require = fileadmin/powermail/validator/DoSomethingValidator.php
    #				}
                }




                # dataProcessor classes that will be called before the mail object will be persisted and used in mails
                dataProcessors {
                    # Powermail data processors
                    10.class = In2code\Powermail\DataProcessor\UploadDataProcessor
                    20.class = In2code\Powermail\DataProcessor\SessionDataProcessor

                    # Add your own data processor classes (e.g. if you want to do something with form values by your own before they are used in powermail to persist or in mails)
    #				1 {
                        # Classname that should be called with method *Finisher()
    #					class = Vendor\Ext\Finisher\DoSomethingFinisher

                        # optional: Add configuration for your PHP
    #					config {
    #						foo = bar

    #						fooCObject = TEXT
    #						fooCObject.value = do something with this text
    #					}

                        # optional: If file will not be loaded from autoloader, add path and it will be called with require_once
    #					require = fileadmin/powermail/finisher/DoSomethingFinisher.php
    #				}
                }




                # Finisher classes that will be called after submit
                finishers {
                    # Powermail finishers
                    10.class = In2code\Powermail\Finisher\SaveToAnyTableFinisher
                    20.class = In2code\Powermail\Finisher\SendParametersFinisher
                    100.class = In2code\Powermail\Finisher\RedirectFinisher

                    # Add your own finishers classes (e.g. if you want to do something with form values by your own: Save into tables, call an API, make your own redirect etc...)
    #				1 {
                        # Classname that should be called with method *Finisher()
    #					class = Vendor\Ext\Finisher\DoSomethingFinisher

                        # optional: Add configuration for your PHP
    #					config {
    #						foo = bar

    #						fooCObject = TEXT
    #						fooCObject.value = do something with this text
    #					}

                        # optional: If file will not be loaded from autoloader, add path and it will be called with require_once
    #					require = fileadmin/powermail/finisher/DoSomethingFinisher.php
    #				}
                }




                # Switch on or off Debug mode (use extension devlog to view this values)
                debug {
                    # All views: Show Settings from TypoScript, Flexform and Extension Manager
                    settings = {$plugin.tx_powermail.settings.misc.debugSettings}

                    # Create view: Show submitted variables
                    variables = {$plugin.tx_powermail.settings.misc.debugVariables}

                    # Create view: Show complete mail settings
                    mail = {$plugin.tx_powermail.settings.misc.debugMail}

                    # Create view: Show saveToTable array
                    saveToTable = {$plugin.tx_powermail.settings.misc.debugSaveToTable}

                    # Create view: Show spamtest results
                    spamshield = {$plugin.tx_powermail.settings.misc.debugSpamshield}
                }



                # Don't touch this (this is just to let the extension know, that there is TypoScript included)
                staticTemplate = 1
            }
        }
    }

    # ParseFunc Configuration for using FAL links in receiver and sender mail
    lib.parseFunc_powermail < lib.parseFunc_RTE
    lib.parseFunc_powermail.tags.link.typolink.forceAbsoluteUrl = 1


    ############################
    # JavaScript and CSS section
    ############################

    # CSS classes for frameworks (add only if bootstrapClassesAndLayout is not added before)
    plugin.tx_powermail {
        settings.setup {
            styles {
                numberOfColumns = {$plugin.tx_powermail.settings.styles.framework.numberOfColumns}

                framework {
                    rowClasses = {$plugin.tx_powermail.settings.styles.framework.rowClasses}
                    formClasses = {$plugin.tx_powermail.settings.styles.framework.formClasses}
                    fieldAndLabelWrappingClasses = {$plugin.tx_powermail.settings.styles.framework.fieldAndLabelWrappingClasses}
                    fieldWrappingClasses = {$plugin.tx_powermail.settings.styles.framework.fieldWrappingClasses}
                    labelClasses = {$plugin.tx_powermail.settings.styles.framework.labelClasses}
                    fieldClasses = {$plugin.tx_powermail.settings.styles.framework.fieldClasses}
                    offsetClasses = {$plugin.tx_powermail.settings.styles.framework.offsetClasses}
                    radioClasses = {$plugin.tx_powermail.settings.styles.framework.radioClasses}
                    checkClasses = {$plugin.tx_powermail.settings.styles.framework.checkClasses}
                    submitClasses = {$plugin.tx_powermail.settings.styles.framework.submitClasses}
                    createClasses = {$plugin.tx_powermail.settings.styles.framework.createClasses}
                }
            }
        }
    }

    # Overwrite classes if bootrap classes given
    [globalVar = LIT:0 < {$plugin.tx_powermail.settings.styles.bootstrap.important}]
    plugin.tx_powermail {
        settings.setup {
            styles {
                numberOfColumns = {$plugin.tx_powermail.settings.styles.bootstrap.numberOfColumns}

                framework {
                    rowClasses = {$plugin.tx_powermail.settings.styles.bootstrap.rowClasses}
                    formClasses = {$plugin.tx_powermail.settings.styles.bootstrap.formClasses}
                    fieldAndLabelWrappingClasses = {$plugin.tx_powermail.settings.styles.bootstrap.fieldAndLabelWrappingClasses}
                    fieldWrappingClasses = {$plugin.tx_powermail.settings.styles.bootstrap.fieldWrappingClasses}
                    labelClasses = {$plugin.tx_powermail.settings.styles.bootstrap.labelClasses}
                    fieldClasses = {$plugin.tx_powermail.settings.styles.bootstrap.fieldClasses}
                    offsetClasses = {$plugin.tx_powermail.settings.styles.bootstrap.offsetClasses}
                    radioClasses = {$plugin.tx_powermail.settings.styles.bootstrap.radioClasses}
                    checkClasses = {$plugin.tx_powermail.settings.styles.bootstrap.checkClasses}
                    submitClasses = {$plugin.tx_powermail.settings.styles.bootstrap.submitClasses}
                    createClasses = {$plugin.tx_powermail.settings.styles.bootstrap.createClasses}
                }
            }
        }
    }
    [end]

    # add jQuery if it was turned on in the constants
    [globalVar = LIT:0 < {$plugin.tx_powermail.settings.javascript.addJQueryFromGoogle}]
    page.includeJSFooterlibs.powermailJQuery = {$plugin.tx_powermail.settings.javascript.powermailJQuery}
    page.includeJSFooterlibs.powermailJQuery.external = 1
    [end]

    # add jQuery if it was turned on in the constants
    [globalVar = LIT:0 < {$plugin.tx_powermail.settings.javascript.addAdditionalJavaScript}]
    page {
        # Inlude JavaScript files
        includeJSFooter {
            powermailJQueryDatepicker = EXT:powermail/Resources/Public/JavaScripts/Libraries/jquery.datetimepicker.min.js
            powermailJQueryFormValidation = EXT:powermail/Resources/Public/JavaScripts/Libraries/parsley.min.js
            powermailJQueryTabs = EXT:powermail/Resources/Public/JavaScripts/Powermail/Tabs.min.js
            powermailForm = EXT:powermail/Resources/Public/JavaScripts/Powermail/Form.min.js
        }
    }
    [end]
