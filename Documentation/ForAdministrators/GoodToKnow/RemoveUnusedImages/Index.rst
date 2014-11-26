.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _removeUnusedImages:

Remove unused images via Scheduler
----------------------------------

If you want to remove unused, uploaded files from the server, you can use a scheduler task (Command Controller) for this.
Define a folder, where powermail should search for unused files. All file which have no relation to a Mail record and is older than 1h will be removed.
Note: This is irreversible - Please take care of a backup

|img-schedulertask|
