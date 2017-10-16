.. include:: ../../../Includes.txt

.. _manipulateValuesFromPowermailAll:

Manipulate values
-----------------

Manipulate values from {powermail_all} marker
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you want to manipulate some values (for {powermail_all} marker), in different views, you can simple use TypoScript stdWrap for this.

All you need is the:

- Marker name of the field that you want to manipulate - e.g. {markerName}

on differnt views:

- Confirmation Page
- Submit Page
- Mail to Sender
- Mail to Receiver
- Optin Mail to Sender

See following TypoScript Setup example, how to manipulate values. If the value for {markerName} is "1", the value "red" is shown. In all other cases the value "blue" will be shown.

Note: You have access to the user send values with .field=value in TypoScript.

.. code-block:: text

	plugin.tx_powermail {
		settings {
			setup {

				# Manipulate values from {powermail_all} by markername
				manipulateVariablesInPowermailAllMarker {
					# On Confirmation Page (if activated)
					confirmationPage {
						# manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
						markerName = CASE
						markerName {
							key.field = value

							1 = TEXT
							1.value = red

							default = TEXT
							default.value = blue
						}
					}

					# On Submitpage
					submitPage {
						# manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
						markerName = CASE
						markerName {
							key.field = value

							1 = TEXT
							1.value = red

							default = TEXT
							default.value = blue
						}
					}

					# In Mail to receiver
					receiverMail {
						# manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
						markerName = CASE
						markerName {
							key.field = value

							1 = TEXT
							1.value = red

							default = TEXT
							default.value = blue
						}
					}

					# In Mail to sender (if activated)
					senderMail {
						# manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
						markerName = CASE
						markerName {
							key.field = value

							1 = TEXT
							1.value = red

							default = TEXT
							default.value = blue
						}
					}

					# In double-opt-in Mail to sender (if activated)
					optinMail {
						# manipulate values by given marker (e.g. firstname, email, referrer) with TypoScript - available fieldnames (access with .field=): value, valueType, uid, pid
						markerName = CASE
						markerName {
							key.field = value

							1 = TEXT
							1.value = red

							default = TEXT
							default.value = blue
						}
					}
				}
			}
		}
	}



Manipulate single called values
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Of course you can use a combination of FLUID and TypoScript to also manipulate single values of variables.

Let's say the user should select a number as option from a selectbox (marker could be {receiver}) 1, 2 or 3 and on the submitpage you don't want to show the number, but a name.


FLUID (RTE or HTML-Template):

.. code-block:: text

	Thank you for your feedback

	Your mail will be send to {receiver -> f:cObject(typoscriptObjectPath:'lib.receiver')}

TypoScript setup:

.. code-block:: text

	lib.receiver = CASE
	lib.receiver {
		key.field = 0
		1 = TEXT
		1.value = Alex
		2 = TEXT
		2.value = Andreas
		3 = TEXT
		3.value = Tim
	}
