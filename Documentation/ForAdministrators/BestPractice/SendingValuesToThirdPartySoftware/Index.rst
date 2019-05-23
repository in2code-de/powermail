.. include:: ../../../Includes.txt

.. _sendvaluestocrm:

Sending Values to a third-party Software (e.g. a CRM)
-----------------------------------------------------


Powermail is also able to send the values to a third-party-software
like a CRM or aMarketing-Automation-Tool (Salesforce, Eloqua, etc...).

Note: This is not a redirect, this feature send the values blind with
CURL to any script.

See TypoScript Settings example:

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		marketing {
			# Send Form values to CRM like salesforce or eloqua
			sendPost {
				# Activate sendPost (0/1)
				_enable = TEXT
				_enable.value = 0

				# Target URL for POST values (like http://www.target.com/target.php)
				targetUrl = http://eloqua.com/e/f.aspx
				
				# target url may just be a static string as well as a TS object like this, so wrap can be applied, .data or .field could be used
				# targetUrl = TEXT
				# targetUrl.value = foobar

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
	}

**Use case:** Editor wants to have the option to provide custom targets for a certain form, but use a given default for everything else.

**Solution:** Just add a hidden field to your form, for instance "custom_url". Provide your default target url as string as before, but add some lines of TS code to overwrite it, if the field is set in the form:

.. code-block:: text

	[globalString = GP:tx_powermail_pi1|field|custom_url = /.+/]
		plugin.tx_powermail.settings.setup.marketing.sendPost.targetUrl = TEXT
		plugin.tx_powermail.settings.setup.marketing.sendPost.targetUrl.field = custom_url
	[global]

Own implementation
------------------

If this configuration doesn't help you because you need an individual solution to send values to a third-party-sofware
or an API, please have a look into the "Finisher" part under "for Developers"
