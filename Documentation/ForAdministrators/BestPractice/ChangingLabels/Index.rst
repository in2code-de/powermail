.. include:: ../../../Includes.txt

.. _changingLabels:

Overwrite Labels and Validation messages
----------------------------------------

You can overwrite any label in powermail via TypoScript Setup.
Have a look into locallang.xlf (EXT:powermail/Resources/Private/Language/locallang.xlf) for getting the relevant keys,
that you want to overwrite (e.g. validationerror_mandatory).

.. code-block:: text

	plugin.tx_powermail {
		_LOCAL_LANG.default.validationerror_mandatory = Please insert a value
		_LOCAL_LANG.de.validationerror_mandatory = Bitte Pflichtfeld ausfüllen
	}

