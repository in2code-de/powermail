.. include:: ../../../Includes.txt

.. _uniqueValues:

Unique Values
-------------

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
