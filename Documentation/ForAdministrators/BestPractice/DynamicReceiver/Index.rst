.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _predefinedreceiver:

Predefined Receiver
-------------------

In some case it could be useful, that the email receiver should be chosen by a selectfield value.
E.g. if the visitor selects "receiver A" in a dropdown, powermail should use
receivera@domain.org and in all other cases receiverb@domain.org

|bestpractice_predefinedreceivers1|

Activate Predefined Receiver
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can add a new option to the predefined receiver with a bit of page TSConfig

.. code-block:: text

	tx_powermail.flexForm.predefinedReceivers.addFieldOptions.receivers1 = receivers #1

|bestpractice_predefinedreceivers2|


Conditional receiver via TypoScript
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


Small example
~~~~~~~~~~~~~

In a simple example rec1@domain.org and rec2@domain.org should be always used, if the editor chooses "receivers2"
in the plugin dropdown:

.. code-block:: text

	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver {
		receivers2.email = TEXT
		receivers2.email.value = rec1@domain.org, rec2@domain.org
	}


Dynamic example 1
~~~~~~~~~~~~~~~~~

In a bit more advanced example, we want to choose the receiver by a given value (e.g. of a select box).
If value 1 is given in a field with marker *{receiver}*, *receivera@domain.org* should be chosen
and if value 2 or something else is given, *receiverb@domain.org* should be chosen.
See following TypoScript setup example:

.. code-block:: text

	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver {

		receivers1.email = CASE
		receivers1.email {

			key.data = GP:tx_powermail_pi1|field|receiver

			1 = TEXT
			1.value = receivera@domain.org

			default = TEXT
			default.value = receiverb@domain.org
		}
	}


Dynamic example 2
~~~~~~~~~~~~~~~~~

Here is another advanced example how to set a different email address for the receiver, by changing an integer
into a frontend user email address (get it from database fe_users.email by given fe_users.uid).
We just use some lines of TypoScript with cObject CONTENT (example for TYPO3 8).
See following TypoScript setup example:

.. code-block:: text

    # Get Email address from fe_users by given POST-parameter
    lib.receiver = CONTENT
    lib.receiver {
        table = fe_users
        select {
            # Page with fe_users records
            pidInList = 33

            where {
                # UID of the fe_users record is given in field with marker {receiver}
                data = GP:tx_powermail_pi1|field|receiver

                wrap = uid=|
                intval = 1
            }
        }
        renderObj = TEXT
        renderObj {
                field = email
        }
    }

    plugin.tx_powermail.settings.setup.receiver.predefinedReceiver.receivers3.email < lib.receiver


Additional note
~~~~~~~~~~~~~~~

The lib.receiver from the last example can also be used with predefined receivers or directly via
cObjectViewHelper in the receiver field in FlexForm - like:
*{f:cObject(typoscriptObjectPath:'lib.receiver')}*
