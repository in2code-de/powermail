.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _filterFormSelection:

Filter Form Selection
---------------------

On large TYPO3 installations it is hard to keep an overview about all forms (see Backend Module "Form Overview")
Especially if an editor can select a lot of forms in the plugin.
Your editors may see forms from other trees, that are not relevant at the form chooser in the powermail plugin.
You can use Page TSConfig to filter the list to relevant forms.

Note: We don't want to implement the element browser because we think it is easier for editors to choose a form from
a select instead of opening a popup, clicks as long a s the relevant page is open and select a form. In short: We think
the current solution is more editor friendly.

|bestpractice_filterformselection1|

You can filter this to the current page or to a tree. Just use Page TSConfig for a filter.

.. code-block:: text

	# Show only Forms from the same page where the plugin is stored (and all subpages)
	tx_powermail.flexForm.formSelection = current

	# Show Forms from page 46 (and all subpages)
	tx_powermail.flexForm.formSelection = 46

	# Show Forms from page 46,49 and the current page where the plugin is stored (and all their subpages)
	tx_powermail.flexForm.formSelection = 46,49,current

|bestpractice_filterformselection2|

