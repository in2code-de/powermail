.. include:: ../../../Includes.txt

.. _templates:

Templates
---------

Using your own templates
^^^^^^^^^^^^^^^^^^^^^^^^

Powermail brings a lot of templates, layouts and partials to your
system. You can change the path the folder with all template via
TypoScript setup:

.. code-block:: text

	plugin.tx_powermail.view {
		templateRootPath = fileadmin/templates/powermailTemplates/
		partialRootPath = fileadmin/templates/powermailPartials/
		layoutRootPath = fileadmin/templates/powermailLayouts/
	}

Take care that all files and folders from the original path (e.g.
typo3conf/ext/powermail/Resources/Private/Templates) are copied to the
new location!


Since **TYPO3 6.2** it's possible to overwrite single files.
If you want to overwrite just one file (e.g. Resources/Private/Templates/Form/Form.html)
you can copy this file to a fileadmin folder (20) and set a fallback folder (10) for the non-existing files.

.. code-block:: text

	plugin.tx_powermail {
		view {
			templateRootPath >
			templateRootPaths {
				10 = EXT:powermail/Resources/Private/Templates/
				20 = fileadmin/templates/powermail/Resources/Private/Templates/
			}
		}
	}


Do not change the original templates of an extension, otherwise it's hard to update the extension!

.. _usingvariables:

Using Variables (former known as Markers)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In Fluid you can use all available fields (that you see in the
backend) and subtables like {firstname}, {label_firstname}, {mail.subject} or
{mail.answers.0.value}.

.. code-block:: text

	Dear Admin,

	there is a new mail from {firstname} {lastname}

	all values:
	{powermail_all}

See the hints in the template files or do a debug output with the
debug viewhelper

.. code-block:: text

	<f:debug>{firstname}</f:debug>
	<f:debug>{mail}</f:debug>
	<f:debug>{_all}</f:debug>

You can also use the variables in the RTE fields in backend:

.. code-block:: text

	Dear {firstname} {lastname},
	thank you for your mail.

	Your text was:
	{text -> f:format.nl2br()}

	All transmitted values are:
	{powermail_all}


.. _usingtyposcriptintemplates:

Using TypoScript in Templates or RTE fields
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Do you need some dynamic values from TypoScript in your Template or
RTE? Use a cObject viehelper:


.. code-block:: text

	{f:cObject(typoscriptObjectPath:'lib.test')}

