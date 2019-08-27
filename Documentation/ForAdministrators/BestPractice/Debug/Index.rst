.. include:: ../../../Includes.txt

.. _goodtoknowdebug:

Debug Powermail
---------------

With TypoScript it's possible to enable some logging Output,
which could help you to fix problems or a misconfiguration.

The logging output will not be saved by default. You need to enable it (see example below).
https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Logging/Index.html

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

.. code-block:: php
   $GLOBALS['TYPO3_CONF_VARS']['LOG']['In2code']['Powermail']['writerConfiguration'] = [
      // configuration for WARNING severity, including all
      // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
      \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
      // add a SyslogWriter
         'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => [
            'logFile' => 'typo3temp/logs/powermail.log',
         ],
      ],
   ];


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
