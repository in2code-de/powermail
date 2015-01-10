.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _prefillOrPreselectAField:

Prefill or preselect a field
----------------------------

Prefilling (input, textarea, hidden) or preselecting (select, check, radio)
of fields will be done by the prefillFieldsViewHelper. It
listen to the following methods and parameters (in this ordering):

1. GET/POST param like &tx\_powermail\_pi1[field][marker]=value

2. GET/POST param like &tx\_powermail\_pi1[marker]=value

3. GET/POST param like &tx\_powermail\_pi1[field][123]=value

4. GET/POST param like &tx\_powermail\_pi1[uid123]=value

5. If field should be filled with values from FE\_User (Flexform Settings)

6. If field should be prefilled from static Flexform Setting

7. Fill with TypoScript cObject like

.. code-block:: text

	plugin.tx_powermail.settings.setup.prefill {
		# Fill field with marker {email}
		email = TEXT
		email.value = mail@domain.org
	}

8. Fill with TypoScript like

.. code-block:: text

	plugin.tx_powermail.settings.setup.prefill {
		# Fill field with marker {email}
		email = mail@domain.org
	}

|img-prefill|
