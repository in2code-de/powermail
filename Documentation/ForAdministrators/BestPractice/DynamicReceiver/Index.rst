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

If value 1 is given in a field with marker {receiver}, receivera@domain.org and if value 2 or something else
is given, receiverb@domain.org should be chosen. See followint TypoScript setup example:

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

In a more simple example rec1@domain.org and rec2@domain.org should be always used, if the editor chooses "receivers2"
in the plugin dropdown:

.. code-block:: text

	plugin.tx_powermail.settings.setup.receiver.predefinedReceiver {
		receivers2.email = TEXT
		receivers2.email.value = rec1@domain.org, rec2@domain.org
	}
