.. include:: Images.txt
.. include:: ../../Includes.txt

Installation
^^^^^^^^^^^^


Import
""""""

Import Extension from the TYPO3 Extension Repository to your server.


Install
"""""""

Install the extension and follow the instructions (adding tables,
etc...).

|img-82|

Extension Manager Settings
""""""""""""""""""""""""""

Main configuration for powermail for CMS wide settings.

|img-83|

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
      Field
   :Description:
      Description
   :DefaultValue:
      Default Value

 - :Field:
      Disable IP logging
   :Description:
      If you generally don't want to save the sender IP address in the database, you can use this checkbox.
   :DefaultValue:
      0

 - :Field:
      Disable BE Module
   :Description:
      You can disable the backend module if you don't store mails in your database or if you don't need the module.
   :DefaultValue:
      0

 - :Field:
      Disable Plugin Information
   :Description:
      Below every powermail plugin is a short info table with form settings. You can disable theese information.
   :DefaultValue:
      0

 - :Field:
      Enable Form caching
   :Description:
      With this setting, you can enable the caching of the form generation, what speeds up sites with powermail forms in the frontend. On the other hand, some additional features (like prefilling values from GET paramter, etc...) are not working any more.
   :DefaultValue:
      0

 - :Field:
      Enable Merge for l10n_mode
   :Description:
      All fields with l10n\_mode exclude should change their translation behaviour to mergeIfNotBlank. This allows you to have different field values in different languages.
   :DefaultValue:
      0

 - :Field:
      ElementBrowser replaces IRRE
   :Description:
      Editors can add pages within a form table via IRRE. If this checkbox is enabled, an element browser replaces the IRRE Relation.
   :DefaultValue:
      0

Static Templates
""""""""""""""""

Add powermail static templates for full functions

|img-84|

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
      Field
   :Description:
      Description

 - :Field:
      Main Template (powermail)
   :Description:
      Main functions and settings for all powermail forms.

 - :Field:
      Add Demo CSS (powermail)
   :Description:
      If you want to include a default CSS-File for forms and lists, add this template.

 - :Field:
      Powermail_Frontend (powermail)
   :Description:
      If you want to use powermail_frontend (Pi2), choose this template.

 - :Field:
      Marketing Information (powermail)
   :Description:
      If you want to see some marketing information about your visitors, you have to add this Template to your root Template. An AJAX function (needs jQuery) sends basic information to a powermail script (Visitors Country, Page Funnel, etc...). Since powermail 2.1.x you can use static_filecache, because we removed the USER_INT function.