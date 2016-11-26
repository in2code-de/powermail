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


Own implementation
------------------

If this configuration doesn't help you because you need an individual solution to send values to a third-party-sofware
or an API, please have a look into the "Finisher" part under "for Developers"
