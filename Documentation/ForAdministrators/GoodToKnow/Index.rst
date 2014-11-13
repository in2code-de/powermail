.. include:: Images.txt
.. include:: ../../Includes.txt

.. _goodtoknow:

Good to know
------------


:ref:`templates` | :ref:`changingLabels` | :ref:`removeValuesFromPowermailAll` | :ref:`uniqueValues` | :ref:`ajaxsubmit` | :ref:`filterFormSelection` | :ref:`spamprevention` | :ref:`savingvaluestothirdpartytables` | :ref:`sendvaluestocrm` | :ref:`goodtoknowdebug` | :ref:`mainTypoScript` | :ref:`removeUnusedImages`

.. _templates:

Templates
^^^^^^^^^



.. _usingowntemplates:

Using your own templates
""""""""""""""""""""""""

Powermail brings a lot of templates, layouts and partials to your
system. You can change the path the folder with all template via
TypoScript setup:

.. code-block:: text

	plugin.tx_powermail.view {
		templateRootPath = fileadmin/templates/powermailTemplates/
		partialRootPath = fileadmin/templates/powermailPartials/
		layoutRootPath = fileadmin/templates/powermailLayouts/
	}

Take care that all files and folders from the original path (e.g.
typo3conf/ext/powermail/Resources/Private/Templates) are copied to the
new location!


Since **TYPO3 6.2** it's possible to overwrite single files.
If you want to overwrite just one file (e.g. Resources/Private/Templates/Form/Form.html)
you can copy this file to a fileadmin folder (20) and set a fallback folder (10) for the non-existing files.

.. code-block:: text

	plugin.tx_powermail {
		view {
			templateRootPath >
			templateRootPaths {
				10 = EXT:powermail/Resources/Private/Templates/
				20 = fileadmin/templates/powermail/Resources/Private/Templates/
			}
		}
	}


Do not change the original templates of an extension, otherwise it's hard to update the extension!


.. _usingvariables:

Using Variables (former known as Markers)
"""""""""""""""""""""""""""""""""""""""""

In Fluid you can use all available fields (that you see in the
backend) and subtables like {firstname}, {mail.subject} or
{mail.answers.0.value}.

See the hints in the template files or do a debug output with the
debug viewhelper

.. code-block:: text

	<f:debug>{firstname}</f:debug>
	<f:debug>{mail}</f:debug>
	<f:debug>{_all}</f:debug>

You can also use the variables in the RTE fields in backend.




.. _usingtyposcriptintemplates:

Using TypoScript in Templates
"""""""""""""""""""""""""""""

Do you need some dynamic values from TypoScript in your Template or
RTE? Use a cObject viehelper:


.. code-block:: text

	{f:cObject(typoscriptObjectPath:'lib.test')}



.. _changingLabels:

Overwrite Labels and Validation messages
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can overwrite any label in powermail via TypoScript Setup. Have a look into locallang.xlf for getting the relevant keys.

.. code-block:: text

	plugin.tx_powermail {
		_LOCAL_LANG.default.validationerror_mandatory = Please insert a value
		_LOCAL_LANG.de.validationerror_mandatory = Bitte Pflichtfeld ausfüllen
	}



.. _removeValuesFromPowermailAll:

Remove single values from {powermail_all} marker
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you don't want to show secondary values like captcha result or the value of your hiddenfields on the submitpage and in the mail to the user, you can configure fieldtypes or markernames, that should be excluded from {powermail_all}

You can separate between:

- Marker names AND
- Field types

on differnt views:

- Confirmation Page
- Submit Page
- Mail to Sender
- Mail to Receiver

See following TypoScript Setup example, how to avoid values from {adminonly} and {referrer} and all fields of type hidden and captcha on all webviews and the mail to the user. In other words - those fieldvalues should only be seen by the admin in the mail to the receiver:

.. code-block:: text

	plugin.tx_powermail {
		settings {
			setup {

				# Exclude values from {powermail_all} by markername or fieldtype
				excludeFromPowermailAllMarker {
					# On Confirmation Page (if activated)
					confirmationPage {
						# add some markernames (commaseparated) which should be excluded
						excludeFromMarkerNames = adminonly, referrer

						# add some fieldtypes (commaseparated) which should be excluded
						excludeFromFieldTypes = hidden, captcha
					}

					# On Submitpage
					submitPage {
						# add some markernames (commaseparated) which should be excluded
						excludeFromMarkerNames = adminonly, referrer

						# add some fieldtypes (commaseparated) which should be excluded
						excludeFromFieldTypes = hidden, captcha
					}

					# In Mail to receiver
					receiverMail {
						# add some markernames (commaseparated) which should be excluded
						excludeFromMarkerNames =

						# add some fieldtypes (commaseparated) which should be excluded
						excludeFromFieldTypes =
					}

					# In Mail to sender (if activated)
					senderMail {
						# add some markernames (commaseparated) which should be excluded
						excludeFromMarkerNames = adminonly, referrer

						# add some fieldtypes (commaseparated) which should be excluded
						excludeFromFieldTypes = hidden, captcha
					}
				}
			}
		}
	}



.. _uniqueValues:

Unique Values
^^^^^^^^^^^^^

As you may know from powermail 1.x you can enforce that every value of a field should only exist once. This could be helpful if you want to start a competition with powermail and every email should only saved once.

.. code-block:: text

	plugin.tx_powermail.settings.setup.validation {
		unique {
			# Enable unique check for {email} - every email must be unique on the page where mails are stored
			email = 1

			# Enable a max limit of 3 times for the same entry for {event}
			event = 3
		}
	}



.. _ajaxsubmit:

AJAX Submit
^^^^^^^^^^^

If you want to use submit via AJAX, you can enable this in TypoScript Setup (jQuery is needed for AJAX functions)

.. code-block:: text

	plugin.tx_powermail.settings.setup.misc.ajaxSubmit = 1

|img-ajaxsubmit|



.. _filterFormSelection:

Filter Form Selection
^^^^^^^^^^^^^^^^^^^^^

On large TYPO3 installations it is hard to keep an overview about all forms (see Backend Module "Form Overview"). Your editors may see forms from other trees, that are not relevant at the form chooser in the powermail plugin.

|img-formselection|

You can filter this to the current page or to a tree. Just use Page TSConfig for a filter.

.. code-block:: text

	# Show only Forms from the same page
	tx_powermail.flexForm.formSelection = current

	# Show Forms from page 46 (and all subpages)
	tx_powermail.flexForm.formSelection = 46

|img-formselectionpagetsconfig|

|img-formselectionfiltered|


.. _spamprevention:

Spam Prevention
^^^^^^^^^^^^^^^


.. _spamprevention-intro:

Introduction
""""""""""""

|img-87|

We ported some spamcheck from wt\_spamshield in the core of powermail:

- Honeypod
- Linkcheck
- Namecheck
- Sessioncheck
- UniqueValues
- String Blacklist
- IP-Address Blacklist

Every submitted form will be checked with this methods. Every failed
method adds a Spam-Indication-Number to a storage. The sum of the
Spam-Indication-Numbers leads to a Spam-Factor (from 0 to 100%). Per
default every mail with a Spam-Factor of 75% is declined with a
message.


How is a Spam-Number related to the Spam-Factor?
""""""""""""""""""""""""""""""""""""""""""""""""

|img-88|

In this example leads a Spam-Indication from 4 to a 75% chance of spam
in the mail(3: 66%, 12: 92%, etc...)


Configure and enable your Spam Settings with TypoScript
"""""""""""""""""""""""""""""""""""""""""""""""""""""""

Reference
~~~~~~~~~


.. container::ts-properties

=========================================================== ========================================== ======================= ===============================
Property                                                    Data Type                                  :ref:`t3tsref:stdwrap`  Default
=========================================================== ========================================== ======================= ===============================
:ref:`goodtoknow-enable`                                    0 = disable | 1 = enable                   no                      1
factor_                                                     :ref:`t3tsref:data-type-integer`           no                      75
email_                                                      :ref:`t3tsref:data-type-string`            no                      *empty*
:ref:`goodtoknow-indicatorhoneypod`                         :ref:`t3tsref:data-type-integer`           no                      5
:ref:`goodtoknow-indicatorlink`                             :ref:`t3tsref:data-type-integer`           no                      3
:ref:`goodtoknow-indicatorlinklimit`                        :ref:`t3tsref:data-type-integer`           no                      2
:ref:`goodtoknow-indicatorname`                             :ref:`t3tsref:data-type-integer`           no                      3
:ref:`goodtoknow-indicatorsession`                          :ref:`t3tsref:data-type-integer`           no                      5
:ref:`goodtoknow-indicatorunique`                           :ref:`t3tsref:data-type-integer`           no                      2
:ref:`goodtoknow-indicatorblackliststring`                  :ref:`t3tsref:data-type-integer`           no                      7
:ref:`goodtoknow-indicatorblackliststringvalues`            :ref:`t3tsref:data-type-string`            no                      viagra,sex,porn,p0rn
:ref:`goodtoknow-indicatorblacklistip`                      :ref:`t3tsref:data-type-integer`           no                      7
:ref:`goodtoknow-indicatorblacklistipvalues`                :ref:`t3tsref:data-type-string`            no                      123.132.125.123,123.132.125.124

=========================================================== ========================================== ======================= ===============================

.. _goodtoknow-enable:

\_enable
~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.spamshield._enable =` 0 (disable) | 1 (enable)

Enable or disable the spamshield of powermail completely



.. _goodtoknow-factor:

factor
~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.spamshield.factor =` :ref:`t3tsref:data-type-integer`

Spam Factor Limit in %


.. _goodtoknow-email:

email
~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.spamshield.email =` :ref:`t3tsref:data-type-string`

Notification Email to Admin if spam recognized


.. _goodtoknow-indicatorhoneypod:

indicator.honeypod
~~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.honeypod =` :ref:`t3tsref:data-type-string`

A Honeypod is an invisible (CSS) field which should not filled with
any value. If it's even filled, it could be a machine.

If this check failed - add this indication value to indicator (0
disables this check completely)



.. _goodtoknow-indicatorlink:

indicator.link
~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.link =` :ref:`t3tsref:data-type-string`

Checks the number of Links in the mail. The number of links is a good
indication of a spammail.

If this check failed - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorlinklimit:

indicator.linkLimit
~~~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.linkLimit =` :ref:`t3tsref:data-type-string`

Limit of links allowed. If there are more links than allowed, the check fails.


.. _goodtoknow-indicatorname:

indicator.name
~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.name =` :ref:`t3tsref:data-type-integer`

Compares fields with marker “firstname” and “lastname” (or “vorname”
and “nachname”). The value may not be the same.

if this check failes - add this indication value to indicator (0
disables this check completely)

.. _goodtoknow-indicatorsession:

indicator.session
~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.session =` :ref:`t3tsref:data-type-integer`

If a user opens the form a timestamp is set in a browser-session. If
the session is empty on submit, it could be a machine.

if this check failes - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorunique:

indicator.unique
~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.unique =` :ref:`t3tsref:data-type-integer`

Compares the values of all fields. If different fields have the same
value, this could be spam.

If this check failes - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorblackliststring:

indicator.blacklistString
~~~~~~~~~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistString =` :ref:`t3tsref:data-type-integer`

Checks mails to not allowed string values.

If this check failes - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorblackliststringvalues:

indicator.blacklistStringValues
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistStringValues =` :ref:`t3tsref:data-type-string`

Define the string that are not allowed.

Blacklisted values (default values should be extended with your experience)



.. _goodtoknow-indicatorblacklistip:

indicator.blacklistIp
~~~~~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistIp =` :ref:`t3tsref:data-type-integer`

Checks if the sender is not in the IP-Blacklist.

If this check failes - add this indication value to indicator (0
disables this check completely)



.. _goodtoknow-indicatorblacklistipvalues:

indicator.blacklistIpValues
~~~~~~~~~~~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistIpValues =` :ref:`t3tsref:data-type-string`

Define the IP-Addreses that are not allowed.

Blacklisted values (default values should be extended with your
experience)

Comprehensive Example
"""""""""""""""""""""

.. code-block:: text

	plugin.tx_powermail {
		settings.setup {
			spamshield {
				_enable = 1
				factor = 75
				email = administrator@domain.org

				indicator {
					honeypod = 5
					link = 3
					linkLimit = 2
					name = 3
					session = 5
					unique = 2
					blacklistString = 7
					blacklistStringValues = viagra,sex,porn,p0rn
					blacklistIp = 7
					blacklistIpValues = 123.132.125.123
				}
			}
		}
	}



Debug and finetune the Spamsettings
"""""""""""""""""""""""""""""""""""

Its usefull to activate a adminmail (for an initial time period e.g.)
if a mail failed (see TypoScript Settings before). In the mail, you
see which checks failed and the overall Spam Factor.

::

   Possible spam in powermail form on page with PID 3

   Spamfactor of this mail: 92%


   Failed Spamchecks:
   0: nameCheck failed
   1: uniqueCheck failed
   2: blacklistStringCheck failed


   Given Form variables:
   2: Alex
   9: Alex
   10: alexander.kellner@in2code.de
   3: Viagra and Free P0rn
   See link on http://freeporn.de or http://freeporn.com

You can also enable the Spamshield Debug to see the Methods
which are failed above the form. Enable with TypoScript setup (Use extension devlog to see this settings):

::

	plugin.tx_powermail.settings.setup.debug.spamshield = 1


|img-89|


Captcha
"""""""

Using a captcha extension also helps to prevent spam. You can simply add a new field of type captcha. A build-in calculating captcha will be shown in frontend.
If you want to use another extension, you can install the extension "captcha" from TER and configure powermail to use this extension for every captcha:

::

	plugin.tx_powermail.settings.setup.captcha.use = captcha


.. _savingvaluestothirdpartytables:

Saving Values to Third Party Table
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Powermail is able to save the values from a submitted form into a
third-party-table (like tt\_news, tt\_address, tt_content, fe_users,
etc...).

This feature and its TypoScript settings are nearly the same as you
may know from powermail < 2.0

Example for tt\_address:

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		# Save values to any table (example for tt_adress)
		dbEntry {
			#####################################################
				### EXAMPLE for adding values to table tt_address ###
				#####################################################

				# Enable or disable db entry for table tt_address
				tt_address._enable = TEXT
				tt_address._enable.value = 1

				# Write only if any field is not yet filled with current value (e.g. test if an email is already in database)
					# default: always add new records (don't care about existing values)
					# update: update record if there is an existing entry (e.g. if email is already there)
					# none: no entry if field is filled (do nothing if record already exists)
				tt_address._ifUnique.email = update

				# Fill new record of table "tt_address" with field "email" with a static value => mail@mail.com
				tt_address.email = TEXT
				tt_address.email.value = mail@mail.com

				# Fill new record of table "tt_address" with field "pid" with the current pid (e.g. 12)
				tt_address.pid = TEXT
				tt_address.pid.data = TSFE:id

				# Fill new record of table "tt_address" with field "tstamp" with the current time as timestamp (like 123456789)
				tt_address.tstamp = TEXT
				tt_address.tstamp.data = date:U

				# Fill new record of table "tt_address" with field "address" with the current formatted time (like "Date: 20.01.2013")
				tt_address.address = TEXT
				tt_address.address.data = date:U
				tt_address.address.strftime = Date: %d.%m.%Y

				# Fill new record of table "tt_address" with field "name" with the value from powermail {firstname}
				tt_address.name = TEXT
				tt_address.name.field = firstname

				# Fill new record of table "tt_address" with field "last_name" with the value from powermail {lastname}
				tt_address.last_name = TEXT
				tt_address.last_name.field = lastname

				# Fill new record of table "tt_address" with field "company" with the value from powermail {company}
				tt_address.company = TEXT
				tt_address.company.field = company



				##############################################################
				### EXAMPLE for adding values to table tt_address_group_mm ###
				### Add relation to an existing address group with uid 123 ###
				##############################################################

				# Enable or disable db entry for table tt_address_group_mm
				tt_address_group_mm._enable = TEXT
				tt_address_group_mm._enable.value = 1

				# Fill new record of table "tt_address_group_mm" with field "uid_local" with uid of tt_address record that was just created before with .field=uid_[tablename]
				tt_address_group_mm.uid_local = TEXT
				tt_address_group_mm.uid_local.field = uid_tt_address

				# Fill new record of table "tt_address_group_mm" with field "uid_foreign" with uid 123
				tt_address_group_mm.uid_foreign = TEXT
				tt_address_group_mm.uid_foreign.value = 123
		}
	}


.. _sendvaluestocrm:

Sending Values to a CRM
^^^^^^^^^^^^^^^^^^^^^^^

Powermail is also able to send the values to a third-party-software
like a CRM or aMarketing-Automation-Tool (Salesforce, Eloqua, etc...).

Note: This is not a redirect, this feature send the values blind with
CURL to any script.

See TypoScript Settings example:

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		Marketing {
			# Send Form values to CRM like salesforce or eloqua
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
	}



.. _goodtoknowdebug:

Debug Powermail
^^^^^^^^^^^^^^^

With TypoScript it's possible to enable some Devlog Output,
which could help you to fix problems or a misconfiguration.

You need an additional extension to show the debug output (e.g. "devlog").

Reference
"""""""""

.. container::ts-properties

=========================================================== ========================================== ===============================
Property                                                    Affected Views                              Default
=========================================================== ========================================== ===============================
:ref:`goodtoknow-debugsettings`                             All                                        0
:ref:`goodtoknow-debugvariables`                            Create View                                0
:ref:`goodtoknow-debugmail`                                 Create View                                0
:ref:`goodtoknow-debugsavetotable`                          Create View                                0
:ref:`goodtoknow-debugspamshield`                           Create View                                0
=========================================================== ========================================== ===============================


.. _goodtoknow-debugsettings:

debug.settings
~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.debug.settings =` 0 = disabled | 1 = enabled

Show Settings from TypoScript, Flexform and Extension Manager


.. _goodtoknow-debugvariables:

debug.variables
~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.debug.variables =` 0 = disabled | 1 = enabled

Show submitted variables


.. _goodtoknow-debugmail:

debug.mail
~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.debug.mail =` 0 = disabled | 1 = enabled

Show mail arrays

.. _goodtoknow-debugsavetotable:

debug.saveToTable
~~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.debug.saveToTable =` 0 = disabled | 1 = enabled

Show saveToTable array

.. _goodtoknow-debugspamshield:

debug.spamshield
~~~~~~~~~~~~~~~~

:typoscript:`plugin.tx_powermail.settings.setup.debug.spamshield =` 0 = disabled | 1 = enabled

Show spamtest results


Comprehensive Example
~~~~~~~~~~~~~~~~~~~~~

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		debug {
			settings = 0
			variables = 0
			mail = 0
			SaveToTable = 0
			spamshield = 0
		}
	}


.. _mainTypoScript:

Main TypoScript
^^^^^^^^^^^^^^^


Constants Overview
""""""""""""""""""

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
"""""

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

		# Include CSS files
		includeCSS {
			powermailJQueryUiDatepicker = EXT:powermail/Resources/Public/Css/jquery.ui.datepicker.css
		}
	}
	[end]


Constants
"""""""""

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


.. _removeUnusedImages:

Remove unused images via Scheduler
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you want to remove unused, uploaded files from the server, you can use a scheduler task (Command Controller) for this.
Define a folder, where powermail should search for unused files. All file which have no relation to a Mail record and is older than 1h will be removed.
Note: This is irreversible - Please take care of a backup

|img-schedulertask|
