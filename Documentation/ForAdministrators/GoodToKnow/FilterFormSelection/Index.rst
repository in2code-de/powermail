.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _filterFormSelection:

Filter Form Selection
---------------------

On large TYPO3 installations it is hard to keep an overview about all forms (see Backend Module "Form Overview"). Your editors may see forms from other trees, that are not relevant at the form chooser in the powermail plugin.

|img-formselection|

You can filter this to the current page or to a tree. Just use Page TSConfig for a filter.

.. code-block:: text

	# Show only Forms from the same page
	tx_powermail.flexForm.formSelection = current

	# Show Forms from page 46 (and all subpages)
	tx_powermail.flexForm.formSelection = 46

|img-formselectionpagetsconfig|

|img-formselectionfiltered|

