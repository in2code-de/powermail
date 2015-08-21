.. include:: ../../../Includes.txt

.. _templates:

Templates
---------

Using your own templates
^^^^^^^^^^^^^^^^^^^^^^^^

Powermail brings a lot of templates, layouts and partials to your
system. You can add additional paths via TypoScript setup.
If you want to overwrite just one file (e.g. Resources/Private/Templates/Form/Form.html)
you can copy this single file to a fileadmin folder (see "20" below) and set a
fallback folder (see "10" below) for the non-existing files:

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


In older TYPO3 versions **TYPO3 7.3 and lower** it's possible to overwrite all files.
Take care that all files and folders from the original path
(e.g. typo3conf/ext/powermail/Resources/Private/Templates) are copied to the new location!

.. code-block:: text

	plugin.tx_powermail.view {
		templateRootPath = fileadmin/templates/powermailTemplates/
		partialRootPath = fileadmin/templates/powermailPartials/
		layoutRootPath = fileadmin/templates/powermailLayouts/
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

