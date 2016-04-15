.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _cleanTypo3tempPowermailFolder:

Clean typo3temp/tx_powermail/ folder
------------------------------------

Introduction
^^^^^^^^^^^^

If you want to clean the typo3temp/tx_powermail/ folder to remove generated export files
(see :ref:`generateExportMail`), you can use this scheduler task.

Image example
^^^^^^^^^^^^^

|scheduler_cleanexportfiles|

Console example
^^^^^^^^^^^^^^^

You can call a scheduler task directly from the console (if the backend user _cli_lowlevel exists) -
see this example (called from webroot):

.. code-block:: text

	typo3/cli_dispatch.phpsh extbase task:cleanexportfiles
