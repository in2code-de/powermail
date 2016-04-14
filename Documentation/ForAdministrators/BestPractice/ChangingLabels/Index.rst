.. include:: ../../../Includes.txt

.. _changingLabels:

Overwrite Labels and Validation messages
----------------------------------------

You can overwrite any label in powermail via TypoScript Setup. Have a look into locallang.xlf for getting the relevant keys.

.. code-block:: text

	plugin.tx_powermail {
		_LOCAL_LANG.default.validationerror_mandatory = Please insert a value
		_LOCAL_LANG.de.validationerror_mandatory = Bitte Pflichtfeld ausfüllen
	}

