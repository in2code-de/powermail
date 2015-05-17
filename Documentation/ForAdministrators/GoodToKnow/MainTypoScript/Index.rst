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
      Receiver Mail Reply Mail: Reply Email address for mail to receiver (e.g. reply@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      receiver.overwrite.replyToName
   :Description:
      Receiver Mail Reply Name: Reply Name for mail to receiver (e.g. Mr. Reply)
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
      sender.overwrite.replyToEmail
   :Description:
      Sender Mail Reply Mail: Reply Email address for mail to sender (e.g. reply@mail.com)
   :Type:
      text
   :Default:


 - :Constants:
      sender.overwrite.replyToName
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
      misc.showOnlyFilledValues
   :Description:
      Show only filled values: If the user submits a form, even not filled values are viewable. If you only want to show labels with filled values, use this setting
   :Type:
      bool
   :Default:
      1

 - :Constants:
      misc.disableRemoveXss
   :Description:
      HTML without RemoveXSS: Per default HTML-Output is parsed through a RemoveXSS-Function to avoid Cross-Site-Scripting for security reasons. If you are aware of possible XSS-Problems, caused by editors, you can disable removeXSS and your original HTML is shown in the Frontend.
   :Type:
      bool
   :Default:
      0

 - :Constants:
      misc.ajaxSubmit
   :Description:
      AJAX Submit Form: Submit Powermail Forms with AJAX (browser will not reload complete page)
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
      Misc Upload Filesize: Define the maximum filesize of file uploads in bytes (10000000 default -> 10 MByte)
   :Type:
      int+
   :Default:
      10000000

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
      0

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



Setup
^^^^^

.. code-block:: text

	##################
    # Frontend Plugin
    ##################
    plugin.tx_powermail {
        view {
            templateRootPath = {$plugin.tx_powermail.view.templateRootPath}
            partialRootPath = {$plugin.tx_powermail.view.partialRootPath}
            layoutRootPath = {$plugin.tx_powermail.view.layoutRootPath}
        }
        features {
            rewrittenPropertyMapper = 1
        }
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
                    # 			\In2code\Powermailextended\Domain\Validator\ZipValidator
                    #
                    # Add method to your class
                    # 		validate100($value, $validationConfiguration)
                    #
                    # Define your Errormessage with TypoScript Setup
                    # 		plugin.tx_powermail._LOCAL_LANG.default.validationerror_validation.100 = Error happens!
                    #
                    # ##########################################################
                    customValidation {
    #					100 = \In2code\Powermailextended\Domain\Validator\ZipValidator
                    }
                }

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

                        # Reply address
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

                        # Reply address
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
                    # enable or disabe spam regocnition
                    _enable = {$plugin.tx_powermail.settings.spamshield.enable}

                    # Spam Factor Limit in %
                    factor = {$plugin.tx_powermail.settings.spamshield.factor}

                    # Notification Email to Admin if spam recognized (empty disables email to admin)
                    email = {$plugin.tx_powermail.settings.spamshield.email}

                    # Subject for notification Email to Admin
                    emailSubject = {$plugin.tx_powermail.settings.spamshield.emailSubject}

                    # Template for notification Email to Admin
                    emailTemplate = {$plugin.tx_powermail.settings.spamshield.emailTemplate}

                    indicator {
                        # if this check failed - add this indication value to indicator (0 disables this check completely)
                        honeypod = 5

                        # if this check failed - add this indication value to indicator (0 disables this check completely)
                        link = 3
                        # Limit of links allowed
                        linkLimit = 2

                        # if this check failed - add this indication value to indicator (0 disables this check completely)
                        name = 3

                        # if this check failed - add this indication value to indicator (0 disables this check completely)
                        session = 5

                        # if this check failed - add this indication value to indicator (0 disables this check completely)
                        unique = 2

                        # if this check failed - add this indication value to indicator (0 disables this check completely)
                        blacklistString = 7
                        # blacklisted values (default values should be extended with your experience)
                        blacklistStringValues = viagra,sex,porn,p0rn

                        # if this check failed - add this indication value to indicator (0 disables this check completely)
                        blacklistIp = 7
                        # blacklisted values (default values should be extended with your experience)
                        blacklistIpValues = 123.132.125.123,123.132.125.124
                    }
                }



                # Misc Settings
                misc {
                    # Show only values if they are filled (for all views and for mails)
                    showOnlyFilledValues = {$plugin.tx_powermail.settings.misc.showOnlyFilledValues}

                    # HTML Output without removeXSS
                    disableRemoveXss = {$plugin.tx_powermail.settings.misc.disableRemoveXss}

                    # Submit Powermail Forms with AJAX (browser will not reload complete page)
                    ajaxSubmit = {$plugin.tx_powermail.settings.misc.ajaxSubmit}

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




                # Save values to any table (example for tt_adress)
                dbEntry {

                    #####################################################
                    ### EXAMPLE for adding values to table tt_address ###
                    #####################################################

                    # Enable or disable db entry for table tt_address
    #				tt_address._enable = TEXT
    #				tt_address._enable.value = 1

                    # Write only if any field is not yet filled with current value (e.g. test if an email is already in database)
                        # default: always add new records (don't care about existing values)
                        # update: update record if there is an existing entry (e.g. if email is already there)
                        # none: no entry if field is filled (do nothing if record already exists)
    #				tt_address._ifUnique.email = update

                    # Fill new record of table "tt_address" with field "email" with a static value => mail@mail.com
    #				tt_address.email = TEXT
    #				tt_address.email.value = mail@mail.com

                    # Fill new record of table "tt_address" with field "pid" with the current pid (e.g. 12)
    #				tt_address.pid = TEXT
    #				tt_address.pid.data = TSFE:id

                    # Fill new record of table "tt_address" with field "tstamp" with the current time as timestamp (like 123456789)
    #				tt_address.tstamp = TEXT
    #				tt_address.tstamp.data = date:U

                    # Fill new record of table "tt_address" with field "address" with the current formatted time (like "Date: 20.01.2013")
    #				tt_address.address = TEXT
    #				tt_address.address.data = date:U
    #				tt_address.address.strftime = Date: %d.%m.%Y

                    # Fill new record of table "tt_address" with field "name" with the value from powermail {firstname}
    #				tt_address.name = TEXT
    #				tt_address.name.field = firstname

                    # Fill new record of table "tt_address" with field "last_name" with the value from powermail {lastname}
    #				tt_address.last_name = TEXT
    #				tt_address.last_name.field = lastname

                    # Fill new record of table "tt_address" with field "company" with the value from powermail {company}
    #				tt_address.company = TEXT
    #				tt_address.company.field = company



                    ##############################################################
                    ### EXAMPLE for adding values to table tt_address_group_mm ###
                    ### Add relation to an existing address group with uid 123 ###
                    ##############################################################

                    # Enable or disable db entry for table tt_address_group_mm
    #				tt_address_group_mm._enable = TEXT
    #				tt_address_group_mm._enable.value = 1

                    # Fill new record of table "tt_address_group_mm" with field "uid_local" with uid of tt_address record that was just created before with .field=uid_[tablename]
    #				tt_address_group_mm.uid_local = TEXT
    #				tt_address_group_mm.uid_local.field = uid_tt_address

                    # Fill new record of table "tt_address_group_mm" with field "uid_foreign" with uid 123
    #				tt_address_group_mm.uid_foreign = TEXT
    #				tt_address_group_mm.uid_foreign.value = 123
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



    ############################
    # JavaScript and CSS section
    ############################

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
            powermailJQueryDatepicker = EXT:powermail/Resources/Public/JavaScripts/jquery.datetimepicker.js
            powermailJQueryFormValidation = EXT:powermail/Resources/Public/JavaScripts/parsley.min.js
            powermailJQueryTabs = EXT:powermail/Resources/Public/JavaScripts/tabs.js
            powermailForm = EXT:powermail/Resources/Public/JavaScripts/form.js
        }
    }
    [end]


Constants
^^^^^^^^^

.. code-block:: text

	plugin.tx_powermail {

		view {
			# cat=powermail_main/file; type=string; label= Path to template root (FE)
			templateRootPath = EXT:powermail/Resources/Private/Templates/

			# cat=powermail_main/file; type=string; label= Path to template partials (FE)
			partialRootPath = EXT:powermail/Resources/Private/Partials/

			# cat=powermail_main/file; type=string; label= Path to template layouts (FE)
			layoutRootPath = EXT:powermail/Resources/Private/Layouts/
		}

		settings {

			main {
				# cat=powermail_additional//0010; type=int+; label= Storage PID: Save mails in a defined Page (normally set via Flexform)
				pid =

				# cat=powermail_additional//0020; type=text; label= Form Uid: Commaseparated list of forms to show (normally set via Flexform)
				form =

				# cat=powermail_additional//0030; type=boolean; label= Confirmation Page Active: Activate Confirmation Page (normally set via Flexform)
				confirmation =

				# cat=powermail_additional//0040; type=boolean; label= Double Optin Active: Activate Double Optin for Mail sender (normally set via Flexform)
				optin =

				# cat=powermail_additional//0050; type=boolean; label= Morestep Active: Activate Morestep Forms (normally set via Flexform)
				moresteps =
			}

			validation {
				# cat=powermail_additional//0100; type=boolean; label= Native Browser Validation: Validate User Input with HTML5 native browser validation on clientside
				native = 1

				# cat=powermail_additional//0110; type=boolean; label= JavaScript Browser Validation: Validate User Input with JavaScript on clientside
				client = 1

				# cat=powermail_additional//0120; type=boolean; label= PHP Server Validation: Validate User Input with PHP on serverside
				server = 1
			}

			receiver {
				# cat=powermail_main/enable/0200; type=boolean; label= Receiver Mail: Enable Email to Receiver
				enable = 1

				# cat=powermail_main//0210; type=boolean; label= Receiver Attachments: Add uploaded files to emails
				attachment = 1

				# cat=powermail_main//0220; type=options[both,html,plain]; label= Receiver Mail Format: Change mail format
				mailformat = both

				default {
					# cat=powermail_additional//0230; type=text; label= Default Sender Name: Sendername if no sender name given
					senderName =

					# cat=powermail_additional//0240; type=text; label= Default Sender Email: Sender-email if no sender email given
					senderEmail =
				}

				overwrite {
					# cat=powermail_additional//0250; type=text; label= Receiver overwrite Email: Commaseparated list of mail receivers overwrites flexform settings (e.g. receiver1@mail.com, receiver1@mail.com)
					email =

					# cat=powermail_additional//0252; type=text; label= Receiver overwrite Name: Receiver Name overwrites flexform settings (e.g. Receiver Name)
					name =

					# cat=powermail_additional//0254; type=text; label= Receiver overwrite SenderName: Sender Name for mail to receiver overwrites flexform settings (e.g. Sender Name)
					senderName =

					# cat=powermail_additional//0256; type=text; label= Receiver overwrite SenderEmail: Sender Email for mail to receiver overwrites flexform settings (e.g. sender@mail.com)
					senderEmail =

					# cat=powermail_additional//0258; type=text; label= Receiver overwrite Mail Subject: Subject for mail to receiver overwrites flexform settings (e.g. New Mail from website)
					subject =

					# cat=powermail_additional//0260; type=text; label= Receiver CC Email Addresses: Commaseparated list of cc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
					cc =

					# cat=powermail_additional//0262; type=text; label= Receiver BCC Email Addresses: Commaseparated list of bcc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
					bcc =

					# cat=powermail_additional//0264; type=text; label= Receiver Mail Return Path: Return Path for emails to receiver (e.g. return@mail.com)
					returnPath =

					# cat=powermail_additional//0266; type=text; label= Receiver Mail Reply Mail: Reply Email address for mail to receiver (e.g. reply@mail.com)
					replyToEmail =

					# cat=powermail_additional//0268; type=text; label= Receiver Mail Reply Name: Reply Name for mail to receiver (e.g. Mr. Reply)
					replyToName =

					# cat=powermail_additional//0270; type=options[1,2,3,4,5]; label= Receiver Mail Priority: Set mail priority for mail to receiver (e.g. 3)
					priority = 3
				}
				senderHeader {
					# cat=powermail_additional//0060; type=text; label= Server-Mail: If set, the Mail-Header Sender is set (RFC 2822 - 3.6.2 Originator fields)
					email =

					# cat=powermail_additional//0070; type=text; label= Server-Name: you can define a name along with the mail address (optional)
					name =
				}
			}

			sender {
				# cat=powermail_main/enable/0400; type=boolean; label= Sender Mail: Enable Email to Sender
				enable = 1

				# cat=powermail_main//0410; type=boolean; label= Sender Attachments: Add uploaded files to emails
				attachment = 0

				# cat=powermail_main//0420; type=options[both,html,plain]; label= Sender Mail Format: Change mail format
				mailformat = both

				overwrite {
					# cat=powermail_additional//0450; type=text; label= Sender overwrite Email: Commaseparated list of mail receivers overwrites flexform settings (e.g. receiver1@mail.com, receiver1@mail.com)
					email =

					# cat=powermail_additional//0452; type=text; label= Sender overwrite Name: Receiver Name overwrites flexform settings (e.g. Receiver Name)
					name =

					# cat=powermail_additional//0454; type=text; label= Sender overwrite SenderName: Sender Name for mail to sender overwrites flexform settings (e.g. Sender Name)
					senderName =

					# cat=powermail_additional//0456; type=text; label= Sender overwrite SenderEmail: Sender Email for mail to sender overwrites flexform settings (e.g. sender@mail.com)
					senderEmail =

					# cat=powermail_additional//0458; type=text; label= Sender overwrite Mail Subject: Subject for mail to sender overwrites flexform settings (e.g. Thx for your mail)
					subject =

					# cat=powermail_additional//0460; type=text; label= Sender CC Email Addresses: Commaseparated list of cc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
					cc =

					# cat=powermail_additional//0462; type=text; label= Sender BCC Email Addresses: Commaseparated list of bcc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
					bcc =

					# cat=powermail_additional//0464; type=text; label= Sender Mail Return Path: Return Path for emails to sender (e.g. return@mail.com)
					returnPath =

					# cat=powermail_additional//0466; type=text; label= Sender Mail Reply Mail: Reply Email address for mail to sender (e.g. reply@mail.com)
					replyToEmail =

					# cat=powermail_additional//0468; type=text; label= Sender Mail Reply Name: Reply Name for mail to sender (e.g. Mr. Reply)
					replyToName =

					# cat=powermail_additional//0470; type=options[1,2,3,4,5]; label= Sender Mail Priority: Set mail priority for mail to sender (e.g. 3)
					priority = 3
				}
				senderHeader {
					# cat=powermail_additional//0060; type=text; label= Server-Mail: If set, the Mail-Header Sender is set (RFC 2822 - 3.6.2 Originator fields)
					email =

					# cat=powermail_additional//0070; type=text; label= Server-Name: you can define a name along with the mail address (optional)
					name =
				}
			}

			db {
				# cat=powermail_main/enable/0600; type=boolean; label= Mail Storage enabled: Store Mails in database
				enable = 1

				# cat=powermail_additional//0610; type=boolean; label= Hidden Mails in Storage: Add mails with hidden flag (e.g. 1)
				hidden = 0
			}

			marketing {
				# cat=powermail_additional//0700; type=boolean; label= Enable Google Conversion: Enable JavaScript for google conversion - This is interesting if you want to track every submit in your Google Adwords account for a complete conversion.
				enable = 0

				# cat=powermail_additional//0710; type=int+; label= Google Conversion Id: Add your google conversion id (see www.google.com/adwords for details)
				google_conversion_id = 1234567890

				# cat=powermail_additional//0720; type=text; label= Google Conversion Label: Add your google conversion label (see www.google.com/adwords for details)
				google_conversion_label = abcdefghijklmnopqrs

				# cat=powermail_additional//0730; type=text; label= Google Conversion Language: Add your google conversion language (see www.google.com/adwords for details)
				google_conversion_language = en
			}

			misc {
				# cat=powermail_additional//0800; type=boolean; label= Show only filled values: If the user submits a form, even not filled values are viewable. If you only want to show labels with filled values, use this setting
				showOnlyFilledValues = 1

				# cat=powermail_additional//0805; type=boolean; label= HTML without RemoveXSS: Per default HTML-Output is parsed through a RemoveXSS-Function to avoid Cross-Site-Scripting for security reasons. If you are aware of possible XSS-Problems, caused by editors, you can disable removeXSS and your original HTML is shown in the Frontend.
				disableRemoveXss = 0

				# cat=powermail_additional//0808; type=boolean; label= AJAX Submit Form: Submit Powermail Forms with AJAX (browser will not reload complete page)
				ajaxSubmit = 0

				# cat=powermail_additional//0810; type=text; label= Misc Upload Folder: Define the folder where files should be uploaded with upload fields (e.g. fileadmin/uploads/)
				uploadFolder = uploads/tx_powermail/

				# cat=powermail_additional//0820; type=int+; label= Misc Upload Filesize: Define the maximum filesize of file uploads in bytes (10000000 default -> 10 MByte)
				uploadSize = 10000000

				# cat=powermail_additional//0830; type=text; label= Misc Upload Fileextensions: Define the allowed filetypes with their extensions for fileuploads and separate them with commas (e.g. jpg,jpeg,gif)
				uploadFileExtensions = jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,flv,mpg,mpeg,avi,mp3,zip,rar,ace,csv

				# cat=powermail_additional//0840; type=boolean; label= Randomized Filenames: Uploaded filenames can be randomized to respect data privacy
				randomizeFileName = 0

				# cat=powermail_additional//0845; type=boolean; label= Force JavaScript Datepicker: Per default html5 Date or Datetime format is used. If you don't want to use it and want to have the same datepicker all over all browsers, you can enable this feature
				forceJavaScriptDatePicker = 0

				# cat=powermail_additional//0850; type=boolean; label= Debug Settings: Show all Settings from TypoScript, Flexform and Global Config in Devlog
				debugSettings = 0

				# cat=powermail_additional//0860; type=boolean; label= Debug Variables: Show all given Plugin variables from GET or POST in Devlog
				debugVariables = 0

				# cat=powermail_additional//0870; type=boolean; label= Debug Mails: Show all mail values in Devlog
				debugMail = 0

				# cat=powermail_additional//0880; type=boolean; label= Debug Save to Table: Show all values if you want to save powermail variables to another table in Devlog
				debugSaveToTable = 0

				# cat=powermail_additional//0890; type=boolean; label= Debug Spamshield: Show Spamshield Functions in Devlog
				debugSpamshield = 0
			}

			spamshield {
				# cat=powermail_spam//0900; type=boolean; label= SpamShield Active: En- or disable Spamshield for Powermail
				enable = 1

				# cat=powermail_spam//0910; type=int+; label= Spamshield Spamfactor in %: Set limit for spamfactor in powermail forms in % (e.g. 85)
				factor = 75

				# cat=powermail_spam//0920; type=text; label= Spamshield Notifymail: Admin can get an email if he/she wants to get informed if a mail failed. Let this field empty and no mail will be sent (e.g. admin@mail.com)
				email =

				# cat=powermail_spam//0930; type=text; label= Spamshield Notifymail Subject: Subject for notification Email to Admin
				emailSubject = Spam in powermail form recognized

				# cat=powermail_spam//0940; type=text; label= Spamshield Notifymail Template: Template for notification Email to Admin
				emailTemplate = EXT:powermail/Resources/Private/Templates/Mail/SpamNotification.html
			}

			captcha {
				# cat=powermail_spam//0930; type=text; label= Captcha Background: Set own captcha background image (e.g. fileadmin/bg.png)
				image = EXT:powermail/Resources/Private/Image/captcha_bg.png

				# cat=powermail_spam//0940; type=text; label= Captcha Font: Set TTF-Font for captcha image (e.g. fileadmin/font.ttf)
				font = EXT:powermail/Resources/Private/Fonts/ARCADE.TTF

				# cat=powermail_spam//0950; type=text; label= Captcha Text Color: Define your text color in hex code - must start with # (e.g. #ff0000)
				textColor = #444444

				# cat=powermail_spam//0960; type=int+; label= Captcha Text Size: Define your text size in px (e.g. 24)
				textSize = 32

				# cat=powermail_spam//0970; type=text; label= Captcha Text Angle: Define two different values (start and stop) for your text random angle and separate it with a comma (e.g. -10,10)
				textAngle = -5,5

				# cat=powermail_spam//0980; type=text; label= Captcha Text Distance Hor: Define two different values (start and stop) for your text horizontal random distance and separate it with a comma (e.g. 20,80)
				distanceHor = 20,80

				# cat=powermail_spam//0990; type=text; label= Captcha Text Distance Ver: Define two different values (start and stop) for your text vertical random distance and separate it with a comma (e.g. 30,60)
				distanceVer = 30,60
			}

			javascript {
				# cat=powermail_main//1000; type=boolean; label= Include jQuery From Google: Add jQuery JavaScript (will be loaded from ajax.googleapis.com)
				addJQueryFromGoogle = 0

				# cat=powermail_additional//1010; type=boolean; label= Include additional JavaScrpt: Add additional JavaScript and CSS Files (form validation, datepicker, etc...)
				addAdditionalJavaScript = 1

				# cat=powermail_additional//1020; type=text; label= jQuery Source: Change jQuery Source - per default it will be loaded from googleapis.com
				powermailJQuery = //ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js
			}

		}
	}
