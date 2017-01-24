.. include:: ../../../Includes.txt

.. _removeValuesFromPowermailAll:

Remove single values from {powermail_all} marker
------------------------------------------------

If you don't want to show secondary values like captcha result or the value of your hiddenfields on the submitpage and in the mail to the user, you can configure fieldtypes or markernames, that should be excluded from {powermail_all}

You can separate between:

- Marker Names AND
- Field Types

on different views:

- Confirmation Page
- Submit Page
- Mail to Sender
- Mail to Receiver
- Opt-in Mail to Sender

See following TypoScript Setup example, how to avoid values from {adminonly} and {referrer}
and all fields of type hidden and captcha on all webviews and in the mail to the user.
In other words - those field values should only be seen by the admin in the mail to the receiver:

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

					# In double-opt-in Mail to sender (if activated)
					optinMail {
						# add some markernames (commaseparated) which should be excluded
						excludeFromMarkerNames = adminonly, referrer

						# add some fieldtypes (commaseparated) which should be excluded
						excludeFromFieldTypes = hidden, captcha
					}
				}
			}
		}
	}
