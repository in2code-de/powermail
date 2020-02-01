# Main TypoScript

Constants are fix variables for your TYPO3 instance, that are often modified for an initial configuration.
While TypoScript setup include the complete frontend configuration for powermail where constants are included.

Setup and constants are located in powermail at EXT:powermail/Configuration/TypoScript/Main/*

Some additional and optional TypoScript is stored in sibling folders.

## TypoScript Constants

```
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

			# cat=powermail_main//0425; type=boolean; label= Add disclaimer link: Add disclaimer link to the sender email (also in optin mail)
			addDisclaimerLink = 1

			default {
				# cat=powermail_additional//0430; type=text; label= Sender Mail - Default Sender Name: Sendername if no sender name given
				senderName =

				# cat=powermail_additional//0432; type=text; label= Sender Mail - Default Sender Email: Sender email address if no sender email given
				senderEmail =
			}

			overwrite {
				# cat=powermail_additional//0450; type=text; label= Sender overwrite Email: Comma-separated list of mail receivers overwrites flexform settings (e.g. receiver1@mail.com, receiver1@mail.com)
				email =

				# cat=powermail_additional//0452; type=text; label= Sender overwrite Name: Receiver Name overwrites flexform settings (e.g. Receiver Name)
				name =

				# cat=powermail_additional//0454; type=text; label= Sender overwrite SenderName: Sender Name for mail to sender overwrites flexform settings (e.g. Sender Name)
				senderName =

				# cat=powermail_additional//0456; type=text; label= Sender overwrite SenderEmail: Sender Email for mail to sender overwrites flexform settings (e.g. sender@mail.com)
				senderEmail =

				# cat=powermail_additional//0458; type=text; label= Sender overwrite Mail Subject: Subject for mail to sender overwrites flexform settings (e.g. Thx for your mail)
				subject =

				# cat=powermail_additional//0460; type=text; label= Sender CC Email Addresses: Comma-separated list of cc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
				cc =

				# cat=powermail_additional//0462; type=text; label= Sender BCC Email Addresses: Comma-separated list of bcc mail receivers (e.g. rec2@mail.com, rec3@mail.com)
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
			# cat=powermail_additional//0800; type=boolean; label= Allow html in html fields: Per default output of fields of type HTML is parsed through a htmlspecialchars() function to avoid Cross-Site-Scripting for security reasons. If you are aware of possible XSS-problems, caused by editors, you can enable it and your original HTML is shown in the Frontend.
			htmlForHtmlFields = 0

			# cat=powermail_additional//0802; type=boolean; label= Allow html in field labels: Per default labels are generated with htmlspecialchars() to prevent xss. This also disables links in labels. If you aware of possible XSS-problems, caused by editors, you can enable it.
			htmlForLabels = 0

			# cat=powermail_additional//0803; type=boolean; label= Show only filled values: If the user submits a form, even not filled values are viewable. If you only want to show labels with filled values, use this setting
			showOnlyFilledValues = 1

			# cat=powermail_additional//0805; type=boolean; label= AJAX Submit Form: Submit Powermail Forms with AJAX (browser will not reload complete page)
			ajaxSubmit = 0

			# cat=powermail_additional//0808; type=boolean; label= Enable AddQueryString: Keep GET-params in form Action (e.g. to use powermail on a tx_news detail page)
			addQueryString = 0

			# cat=powermail_additional//0810; type=text; label= Misc Upload Folder: Define the folder where files should be uploaded with upload fields (e.g. fileadmin/uploads/)
			uploadFolder = uploads/tx_powermail/

			# cat=powermail_additional//0820; type=int+; label= Misc Upload Filesize: Define the maximum filesize of file uploads in bytes (10485760 Byte -> 10 MB)
			uploadSize = 10485760

			# cat=powermail_additional//0830; type=text; label= Misc Upload Fileextensions: Define the allowed filetypes with their extensions for fileuploads and separate them with commas (e.g. jpg,jpeg,gif)
			uploadFileExtensions = jpg,jpeg,gif,png,tif,txt,doc,docx,xls,xlsx,ppt,pptx,pdf,mpg,mpeg,avi,mp3,zip,rar,ace,csv,svg

			# cat=powermail_additional//0840; type=boolean; label= Randomized Filenames: Uploaded filenames can be randomized to respect data privacy
			randomizeFileName = 1

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

			# cat=powermail_spam//0925; type=text; label= Spamshield Notifymail sendermail: Define sender email address for mails
			senderEmail =

			# cat=powermail_spam//0930; type=text; label= Spamshield Notifymail Subject: Subject for notification Email to Admin
			emailSubject = Spam in powermail form recognized

			# cat=powermail_spam//0940; type=text; label= Spamshield Notifymail Template: Template for notification Email to Admin
			emailTemplate = EXT:powermail/Resources/Private/Templates/Mail/SpamNotification.html

			# cat=powermail_spam//0950; type=text; label= Spamshield Log Template Location: Path of log file, ie. typo3temp/logs/powermail_spam.log, if empty, logging is deactivated
			logfileLocation =

			# cat=powermail_spam//0960; type=text; label= Spamshield Log Template: Template for entries written to log file
			logTemplate = EXT:powermail/Resources/Private/Templates/Log/SpamNotification.html
		}

		captcha {
			# cat=powermail_spam//0930; type=text; label= Captcha Background: Set own captcha background image (e.g. fileadmin/bg.png)
			image = EXT:powermail/Resources/Private/Image/captcha_bg.png

			# cat=powermail_spam//0940; type=text; label= Captcha Font: Set TTF-Font for captcha image (e.g. fileadmin/font.ttf)
			font = EXT:powermail/Resources/Private/Fonts/Segment16cBold.ttf

			# cat=powermail_spam//0950; type=text; label= Captcha Text Color: Define your text color in hex code - must start with # (e.g. #ff0000)
			textColor = #111111

			# cat=powermail_spam//0960; type=int+; label= Captcha Text Size: Define your text size in px (e.g. 24)
			textSize = 32

			# cat=powermail_spam//0970; type=text; label= Captcha Text Angle: Define two different values (start and stop) for your text random angle and separate it with a comma (e.g. -10,10)
			textAngle = -5,5

			# cat=powermail_spam//0980; type=text; label= Captcha Text Distance Hor: Define two different values (start and stop) for your text horizontal random distance and separate it with a comma (e.g. 20,80)
			distanceHor = 20,100

			# cat=powermail_spam//0990; type=text; label= Captcha Text Distance Ver: Define two different values (start and stop) for your text vertical random distance and separate it with a comma (e.g. 30,60)
			distanceVer = 30,45
		}

		javascript {
			# cat=powermail_main//1000; type=boolean; label= Include jQuery From Google: Add jQuery JavaScript (will be loaded from ajax.googleapis.com)
			addJQueryFromGoogle = 0

			# cat=powermail_additional//1010; type=boolean; label= Include additional JavaScript: Add additional JavaScript and CSS Files (form validation, datepicker, etc...)
			addAdditionalJavaScript = 1

			# cat=powermail_additional//1020; type=text; label= jQuery Source: Change jQuery Source - per default it will be loaded from googleapis.com
			powermailJQuery = //ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js
		}

		# CSS classes for frameworks (add only if bootstrapClassesAndLayout is not added before)
		styles {
			framework {
				# cat=powermail_styles//0020; type=int+; label= Number of columns
				numberOfColumns = 0

				# cat=powermail_styles//0100; type=text; label= Framework classname(s) for containers to build rows
				rowClasses = row

				# cat=powermail_styles//0105; type=text; label= Framework classname(s) for form "form-horizontal"
				formClasses =

				# cat=powermail_styles//0110; type=text; label= Framework classname(s) for overall wrapping container of a field/label pair e.g. "row form-group"
				fieldAndLabelWrappingClasses =

				# cat=powermail_styles//0120; type=text; label= Framework classname(s) for wrapping container of a field e.g. "row form-group"
				fieldWrappingClasses = powermail_field

				# cat=powermail_styles//0130; type=text; label= Framework classname(s) for fieldlabels e.g. "col-md-2 col-md-3"
				labelClasses = powermail_label

				# cat=powermail_styles//0140; type=text; label= Framework classname(s) for fields e.g. "form-control"
				fieldClasses =

				# cat=powermail_styles//0150; type=text; label= Framework classname(s) for fields with an offset e.g. "col-sm-offset-2"
				offsetClasses =

				# cat=powermail_styles//0160; type=text; label= Framework classname(s) especially for radiobuttons e.g. "radio"
				radioClasses = radio

				# cat=powermail_styles//0170; type=text; label= Framework classname(s) especially for checkboxes e.g. "check"
				checkClasses = checkbox

				# cat=powermail_styles//0180; type=text; label= Framework classname(s) for the submit button e.g. "btn btn-primary"
				submitClasses = powermail_submit

				# cat=powermail_styles//0190; type=text; label= Framework classname(s) for "create" message after submit
				createClasses = powermail_create
			}
		}
	}
}
```


## TypoScript Setup

```
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
lib.parseFunc_powermail.tags.a.typolink.forceAbsoluteUrl = 1


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
[{$plugin.tx_powermail.settings.styles.bootstrap.important} == 1]
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

[{$plugin.tx_powermail.settings.javascript.addJQueryFromGoogle} == 1]
page.includeJSFooterlibs.powermailJQuery = {$plugin.tx_powermail.settings.javascript.powermailJQuery}
page.includeJSFooterlibs.powermailJQuery.external = 1
[end]

# add jQuery if it was turned on in the constants

[{$plugin.tx_powermail.settings.javascript.addAdditionalJavaScript} == 1]
page {
	# Inlude JavaScript files
	includeJSFooter {
		powermailJQueryDatepicker = EXT:powermail/Resources/Public/JavaScript/Libraries/jquery.datetimepicker.min.js
		powermailJQueryFormValidation = EXT:powermail/Resources/Public/JavaScript/Libraries/parsley.min.js
		powermailJQueryTabs = EXT:powermail/Resources/Public/JavaScript/Powermail/Tabs.min.js
		powermailForm = EXT:powermail/Resources/Public/JavaScript/Powermail/Form.min.js
	}
}
[end]

```
