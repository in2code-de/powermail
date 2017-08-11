.. include:: ../../Includes.txt

Upgrade Powermail
-----------------

Introduction
^^^^^^^^^^^^

Maybe you need to upgrade powermail to this version. Following instructions should help you to upgrade as smooth as possible.
Note: Please do not modify any extension files to ensure easy upgrading.


Any Upgrade
^^^^^^^^^^^

Description
"""""""""""

Do you have **problems after a powermail upgrade**?

Follow this steps
"""""""""""""""""

Please follow this steps before you create a bug entry on GitHub:

* Clean all caches in Install Tool
* Remove all files and folders in typo3temp/
* Try to use the default TypoScript (comment out your TypoScript)
* Try to use default Templates (and Partials and Layouts)
* Reload Frontend
* Reload Backend
* Check again
* Still problems? Please report to `GitHub <https://github.com/einpraegsam/powermail/issues>`_


Bugfix Upgrade
^^^^^^^^^^^^^^

Example
"""""""

Upgrade from Powermail 3.0.x to 3.0.y

Details
"""""""

No breaking changes in database or HTML-Template-Files.

An upgrade should be very easy. Follow the steps:

* Upgrade powermail with Extension Manager (or manually)
* Clean all caches in Install Tool (just removing all files in typo3temp/* may not help you!)
* Reload Frontend
* Reload Backend
* Done


Minor Upgrade
^^^^^^^^^^^^^

Example
"""""""

Upgrade from Powermail 3.x.a to 3.y.b

Details
"""""""

Breaking changes in HTML-Templates. You have to upgrade your own HTML-Templates (and Partials and Layouts) by your own.
Probably there are changes in the Database, but the existing will work.

Follow the steps:

* Upgrade powermail with Extension Manager (or manually)
* Use original HTML-Files (Templates, Partials, Layouts) for testing
* Clean all caches in Install Tool (just removing all files in typo3temp/* may not help you!)
* Reload Frontend
* Reload Backend
* Update Backend User Rights for the new powermail version
* Done


Major Upgrade
^^^^^^^^^^^^^

Example 1
"""""""""

Upgrade from Powermail 2.x to 3.x

Details 1
"""""""""

Many breaking changes in database and all Template Files.

Follow the steps:

* Upgrade powermail with Extension Manager, manually or with composer
* Open the update script in the Extension Manager, to convert old table names to new table names
* Clean all caches in Install Tool (just removing all files in typo3temp/* may not help you!)
* Use original HTML-Files (Templates, Partials, Layouts) for testing
* Reload Frontend
* Reload Backend
* Update Backend User Rights for the new powermail version
* Done

Example 2
"""""""""

Upgrade from Powermail 1.x to 2.x

Details 2
"""""""""

Many breaking changes in database and all Template Files.

Follow the steps:

* Upgrade powermail with Extension Manager (or manually)
* Clean all caches in Install Tool (just removing all files in typo3temp/* may not help you!)
* Do not make a "compare database" with the remove-function. Old powermail tables are still need for a form-converting
* Use original HTML-Files (Templates, Partials, Layouts) for testing
* Reload Frontend
* Reload Backend
* Use the Converter-Tool in Powermail Backend Module to convert old forms to new ones
* Update Backend User Rights for the new powermail version
* Done
