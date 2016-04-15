.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _cleanUploadsPowermailFolder:

Clean uploads/tx_powermail/ folder
----------------------------------

Introduction
^^^^^^^^^^^^

If you want to clean the uploads/tx_powermail/ folder to remove uploaded files (maybe for privacy reasons),
you can use this scheduler task.

Image example
^^^^^^^^^^^^^

|scheduler_cleanupload|

Console example
^^^^^^^^^^^^^^^

You can call a scheduler task directly from the console (if the backend user _cli_lowlevel exists) -
see this example (called from webroot):

.. code-block:: text

	typo3/cli_dispatch.phpsh extbase task:cleanuploadsfiles
