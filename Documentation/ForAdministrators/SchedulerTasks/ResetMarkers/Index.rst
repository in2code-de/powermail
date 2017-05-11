.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _resetMarkers:

Reset all markers from fields within a form
--------------------------------------------

Introduction
^^^^^^^^^^^^

If you want to reset marker names, you can use this scheduler task. Normally it should not be possible to create forms
with duplicated or empty marker names in ``sys_language_uid=0`` but in some special cases (updates, imports or
misconfiguration) it could be, that there are forms with broken markers (see image below).
You can use this scheduler task to reset markers of one or all forms.

Note: If you open a form and see following notice, the marker names are broken:

| **Error: Non-Unique marker names in the fields of this form detected**
| This should not happen in powermail. Please check marker names of all fields to this form and fix it manually.

Image example
^^^^^^^^^^^^^

|scheduler_resetmarkers2|

|scheduler_resetmarkers1|

Console example
^^^^^^^^^^^^^^^

You can call a scheduler task directly from the console (if the backend user ``_cli_lowlevel`` exists) - see this example (called from webroot):

.. code-block:: text

	typo3/cli_dispatch.phpsh extbase task:resetmarkernamesinform --form-uid=254 --force-reset=0
