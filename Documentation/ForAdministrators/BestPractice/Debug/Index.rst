.. include:: ../../../Includes.txt

.. _goodtoknowdebug:

Debug Powermail
---------------

With TypoScript it's possible to enable some Devlog Output,
which could help you to fix problems or a misconfiguration.

You need an additional extension to show the debug output (e.g. "devlog").

Comprehensive Example
"""""""""""""""""""""

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		debug {
			settings = 0
			variables = 0
			mail = 0
			saveToTable = 0
			spamshield = 0
		}
	}


Configuration
^^^^^^^^^^^^^

.. t3-field-list-table::
 :header-rows: 1

 - :TyposcriptPath:
      Relative Typoscript path
   :Description:
      Description
   :Type:
      Type
   :Default:
      Default value

 - :TyposcriptPath:
      debug.settings
   :Description:
      Show Settings from TypoScript, Flexform and Extension Manager
   :Type:
      0/1
   :Default:
      0

 - :TyposcriptPath:
      debug.variables
   :Description:
      Show submitted variables
   :Type:
      0/1
   :Default:
      0

 - :TyposcriptPath:
      debug.mail
   :Description:
      Show mail arrays
   :Type:
      0/1
   :Default:
      0

 - :TyposcriptPath:
      debug.saveToTable
   :Description:
      Show saveToTable array
   :Type:
      0/1
   :Default:
      0

 - :TyposcriptPath:
      debug.spamshield
   :Description:
      Show spamtest results
   :Type:
      0/1
   :Default:
      0
