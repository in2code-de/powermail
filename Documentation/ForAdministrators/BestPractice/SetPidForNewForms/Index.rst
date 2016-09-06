.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _setPidForNewForms:

Set PID for new forms
---------------------

Maybe you want to define where new forms should be saved if editors are using the add link in plugin, you can do this
with a bit of page TSConfig.

|bestpractice_setpidfornewforms|

.. code-block:: text

	# Save new forms on page 123
	tx_powermail.flexForm.newFormPid = 123
