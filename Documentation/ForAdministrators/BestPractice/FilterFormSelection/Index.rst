.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _filterFormSelection:

Filter Form Selection
---------------------

On large TYPO3 installations it is hard to keep an overview about all forms (see Backend Module "Form Overview")
Especially if an editor can select a lot of forms in the plugin.
Your editors may see forms from other trees, that are not relevant at the form chooser in the powermail plugin.
You can use Page TSConfig to filter the list to relevant forms.
Per default editors can only see forms, that are stored on pages where the editors have read access.

Note: We don't want to implement the element browser because we think it is easier for editors to choose a form from
a select instead of opening a popup, clicks as long a s the relevant page is open and select a form. In short: We think
the current solution is more editor friendly.

|bestpractice_filterformselection1|

You can filter this to the current page or to a tree. Just use Page TSConfig for a filter.

.. code-block:: text

	# Show only forms from the same page where the plugin is stored (and all subpages)
	tx_powermail.flexForm.formSelection = current

	# Show forms from page 46 (and all subpages)
	tx_powermail.flexForm.formSelection = 46

	# Show forms from page 46,49 and the current page where the plugin is stored (and all their subpages)
	tx_powermail.flexForm.formSelection = 46,49,current

	# Show all forms even for editors that may have no access to pages where forms are stored in
	tx_powermail.flexForm.formSelection = *

|bestpractice_filterformselection2|

