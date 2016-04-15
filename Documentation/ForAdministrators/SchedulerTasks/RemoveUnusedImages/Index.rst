.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _removeUnusedImages:

Remove unused images via Scheduler
----------------------------------

Introduction
^^^^^^^^^^^^

If you want to remove unused, uploaded files from the server, you can use a scheduler task (Command Controller) for this.
Define a folder, where powermail should search for unused files. All file which have no relation to a Mail record and is older than 1h will be removed.
Note: This is irreversible - Please take care of a backup

Image example
^^^^^^^^^^^^^

|scheduler_cleanunusedfiles|

Console example
^^^^^^^^^^^^^^^

You can call a scheduler task directly from the console (if the backend user _cli_lowlevel exists) - see this example (called from webroot):

.. code-block:: text

	typo3/cli_dispatch.phpsh extbase task:cleanunuseduploads
	# typo3/cli_dispatch.phpsh extbase task:cleanunuseduploads --uploadPath="typo3temp/tx_powermail"
