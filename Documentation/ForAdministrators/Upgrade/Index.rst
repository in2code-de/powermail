.. include:: ../../Includes.txt

Upgrade Powermail
-----------------

Introduction
^^^^^^^^^^^^

Maybe you need to upgrade powermail to this version. Following instructions should help you to upgrade as smooth as possible.
Note: Please do not modify any extension files to ensure easy upgrading.


Bugfix Upgrade
^^^^^^^^^^^^^^

Example
"""""""

Upgrade from Powermail 2.1.x to 2.1.y or upgrade from 2.2.x to 2.2.y

Details
"""""""

No breaking changes in database or HTML-Template-Files.

An upgrade should be very easy. Follow the steps:

- Upgrade powermail with Extension Manager (or manually)
- Clean all caches in Install Tool (just removing all files in typo3temp/* may not help you!)
- Reload Frontend
- Reload Backend
- Done


Minor Upgrade
^^^^^^^^^^^^^

Example
"""""""

Upgrade from Powermail 2.0.x to 2.1.y

Details
"""""""

Breaking changes in HTML-Templates. You have to upgrade your own HTML-Templates (and Partials and Layouts) by your own. There are some new Fields in Database, but the existing will work.

Follow the steps:

- Upgrade powermail with Extension Manager (or manually)
- Use original HTML-Files (Templates, Partials, Layouts) for testing
- Clean all caches in Install Tool (just removing all files in typo3temp/* may not help you!)
- Reload Frontend
- Reload Backend
- Update Backend User Rights for the new powermail version
- Done


Major Upgrade
^^^^^^^^^^^^^

Example
"""""""

Upgrade from Powermail 1.6.x to 2.1.y

Details
"""""""

Many breaking changes in database and all Template Files.

Follow the steps:

- Upgrade powermail with Extension Manager (or manually)
- Clean all caches in Install Tool (just removing all files in typo3temp/* may not help you!)
- Do not make a "compare database" with the remove-function. Old powermail tables are still need for a form-converting
- Use original HTML-Files (Templates, Partials, Layouts) for testing
- Reload Frontend
- Reload Backend
- Use the Converter-Tool in Powermail Backend Module to convert old forms to new ones
- Update Backend User Rights for the new powermail version
- Done