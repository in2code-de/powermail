.. include:: Images.txt
.. include:: ../../Includes.txt

Installation
^^^^^^^^^^^^


Import
""""""

Import Extension from the TYPO3 Extension Repository to your server or use composer for that.


Install
"""""""

Install the extension and follow the instructions in TYPO3.

|extension_manager|

Note: If you **migrate from powermail 2 to powermail 3**, you should click the settings icon in
the Extension Manager. Powermail will convert old tables to new tables (but only if the new
tables are still empty)

|extension_manager3|

Extension Manager Settings
""""""""""""""""""""""""""

Main configuration for powermail for CMS wide settings.

|extension_manager2|

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
      Disable marketing information
   :Description:
      If you want to disable all marketing relevant information of powermail, you can enable this checkbox (effected: mail to admin, backend module, mail records, no static typoscript template).
   :DefaultValue:
      0

 - :Field:
      Disable BE module
   :Description:
      You can disable the backend module if you don't store mails in your database or if you don't need the module.
   :DefaultValue:
      0

 - :Field:
      Disable plugin information
   :Description:
      Below every powermail plugin is a short info table with form settings. You can disable these information.
   :DefaultValue:
      0

 - :Field:
      Disable plugin information mail preview
   :Description:
      The plugin information shows 3 latest mails. If you want to disable this preview, you can check the button.
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
      Editors can add pages within a form table via IRRE. If this checkbox is enabled, an element browser replaces the IRRE Relation. Note: this is a beta-feature and not completely tested!
   :DefaultValue:
      0

Static Templates
""""""""""""""""

Add powermail static templates for full functions

|static_templates|

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
      Powermail_Frontend (powermail)
   :Description:
      If you want to use show mails in frontend (Pi2), choose this template.

 - :Field:
      Add classes and CSS based on bootstrap (powermail)
   :Description:
      If you want to add default bootstrap classes to all forms, pages and fields in frontend, you should
      add this static template. Note: If you want to add bootstrap.css from powermail, see constant editor.

 - :Field:
      Marketing Information (powermail)
   :Description:
      If you want to see some marketing information about your visitors, you have to add this
      Template to your root Template. An AJAX function (needs jQuery) sends basic information to a
      powermail script (Visitors Country, Page Funnel, etc...).
