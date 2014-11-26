.. include:: ../../../Includes.txt

.. _goodtoknowdebug:

Debug Powermail
---------------

With TypoScript it's possible to enable some Devlog Output,
which could help you to fix problems or a misconfiguration.

You need an additional extension to show the debug output (e.g. "devlog").

Reference
^^^^^^^^^

.. container::ts-properties

=========================================================== ========================================== ===============================
Property                                                    Affected Views                              Default
=========================================================== ========================================== ===============================
:ref:`goodtoknow-debugsettings`                             All                                        0
:ref:`goodtoknow-debugvariables`                            Create View                                0
:ref:`goodtoknow-debugmail`                                 Create View                                0
:ref:`goodtoknow-debugsavetotable`                          Create View                                0
:ref:`goodtoknow-debugspamshield`                           Create View                                0
=========================================================== ========================================== ===============================


.. _goodtoknow-debugsettings:

debug.settings
""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.debug.settings =` 0 = disabled | 1 = enabled

Show Settings from TypoScript, Flexform and Extension Manager


.. _goodtoknow-debugvariables:

debug.variables
"""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.debug.variables =` 0 = disabled | 1 = enabled

Show submitted variables


.. _goodtoknow-debugmail:

debug.mail
""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.debug.mail =` 0 = disabled | 1 = enabled

Show mail arrays

.. _goodtoknow-debugsavetotable:

debug.saveToTable
"""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.debug.saveToTable =` 0 = disabled | 1 = enabled

Show saveToTable array

.. _goodtoknow-debugspamshield:

debug.spamshield
""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.debug.spamshield =` 0 = disabled | 1 = enabled

Show spamtest results


Comprehensive Example
"""""""""""""""""""""

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		debug {
			settings = 0
			variables = 0
			mail = 0
			SaveToTable = 0
			spamshield = 0
		}
	}
