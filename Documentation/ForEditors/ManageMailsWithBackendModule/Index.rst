.. include:: Images.txt

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)

.. _manageMailsWithBackendModule:

Mail Backend Module
-------------------

Introduction
^^^^^^^^^^^^

Per default powermails adds a new backend module to the TYPO3 backend.
This module helps you to manage mails and forms.

First step
^^^^^^^^^^

Open the Mail Backend Module and choose a page with mails in the foldertree. You will see all stored mails.

|backend_module_menu|

The Backend Module will start with the mail listing.

|backend_module_menu2|

.. _manageMailsWithBackendModuleMailList:

Mail List
"""""""""

If the page contains mails, all mails will be listet. The area over the mail list is
splitted into two parts (Search part and Export part).

|backend_module_list|

Search Area
~~~~~~~~~~~

Search Area is useful to filter the mails (of the List Area) and to
manage the Export.

If you click on **Extended Search** some more fields which can be used for filtering will be shown.

|backend_module_filter|

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
      Field
   :Description:
      Description
   :Explanation:
      Explanation

 - :Field:
      Fulltext Search
   :Description:
      This is the main search field for a full text search.
   :Explanation:
      If you enter a searchterm all fields of the mail and of the answers
      are searched by your term (technical note: OR and LIKE %term%)

 - :Field:
      Filter List Button
   :Description:
      Submit Button for search.
   :Explanation:
      This is the main submit button which should be clicked if you're using
      the fulltext search, even if you use some other fields (like Start, Stop, etc...).

 - :Field:
      Start
   :Description:
      Choose a Start Date for the filter list.
   :Explanation:
      A datepicker will be opened on click (if the browser do not respect html5 datetime) to set Date and Time for the beginning of the timeframe.

 - :Field:
      Stop
   :Description:
      Choose a Stop Date for the filter list.
   :Explanation:
      A datepicker will be opened on click (if the browser do not respect html5 datetime) to set Date and Time for the ending of the timeframe.

 - :Field:
      Sender Name
   :Description:
      Search through the sender name field of the stored mail.
   :Explanation:
      All fields are related to each other with OR.

 - :Field:
      Sender Email
   :Description:
      Search through the sender email field of the stored mail.
   :Explanation:
      All fields are related to each other with OR.

 - :Field:
      Subject
   :Description:
      Search through the subjtect field of the stored mail.
   :Explanation:
      All fields are related to each other with OR.

 - :Field:
      Deactivated Mails
   :Description:
      Show only activated or deactivated mails.
   :Explanation:
      Deactivated mails could be interesting if you use Double-Opt-In e.g.

 - :Field:
      Additional Fields
   :Description:
      One or more fields - depending on the form - are listed (e.g.
      firstname, lastname, email, etc...).
   :Explanation:
      All fields are related to each other with OR.

Export Area
~~~~~~~~~~~

Export Area gives you the possibility to export your mails in XLS or
CSV format.
Note: For editors only enabled fields are free to export.

|backend_module_export|

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
      Field
   :Description:
      Description
   :Explanation:
      Explanation

 - :Field:
      XLS Button
   :Description:
      If you want to export the current list in XLS-Format, click the button.

      XLS-Files can be opened with Microsoft Excel or Open Office (e.g.).
   :Explanation:
      If you filter or sort the list before, the export will only export the
      filtered mails.

      See “Columns in Export File” if you want to change the export file
      columns.

 - :Field:
      CSV Button
   :Description:
      If you want to export the current list in CSV-Format, click the button.

      CSV-Files can be opened with Microsoft Excel or Open Office (e.g.).
   :Explanation:
      If you filter or sort the list before, the export will only export the
      filtered mails.

      See “Columns in Export File” if you want to change the export file
      columns.

 - :Field:
      Columns in Export File
   :Description:
      This area shows the columns and the ordering of the rows in the
      export-file. You can play around with drag and drop.
   :Explanation:
      Change sorting: Drag and drop a line up or down

      Add row: Choose a line of the “Available Columns” and drop on “Columns
      in Export File”

      Remove row: Drag line and move to the “Available Columns”

 - :Field:
      Available Columns
   :Description:
      This area shows the available columns that can be used in the export
      file.
   :Explanation:
      See Row before for an explanation.


.. _manageMailsWithBackendModuleToolsOverview:

Form Overview
"""""""""""""

This is a very helpful list with all powermail forms of your installation.
This table helps you to manage your forms, even in large installations.

|backend_module_formoverview|

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
      Field
   :Description:
      Description
   :Explanation:
      Explanation

 - :Field:
      Form Title
   :Description:
      Form Title
   :Explanation:
      Click on Form title will open the form edit view. Mouseover will show you the Form UID.

 - :Field:
      Stored on Page
   :Description:
      This form is stored in this Page
   :Explanation:
      Click on Page title will open the page in page view. Mouseover will show you the Page UID.

 - :Field:
      Used on Page
   :Description:
      This form can be used on different Pages. One line for one page.
   :Explanation:
      Click on a Page title will open the page in page view. Mouseover will show you the Page UID.

 - :Field:
      Powermail Pages
   :Description:
      Amount of Powermail Pages within this form
   :Explanation:
      Mouseover will show you the Powermail-Page-Names.

 - :Field:
      Powermail Fields
   :Description:
      Amount of Powermail Fields within this form
   :Explanation:
      Mouseover will show you the Powermail-Field-Names.

 - :Field:
      Edit Icon
   :Description:
      Edit the form
   :Explanation:
      Same function as click on Form name.


.. _manageMailsWithBackendModuleReporting:

Reporting (Form)
""""""""""""""""

This view helps you to get a small overview over form values.
Filter Mails in the same way as the listing with the filter area.
Below the Filter Area you will see some small diagrams (one diagram for each field in the form on this page).

|backend_module_reportingform|

Reporting (Marketing)
"""""""""""""""""""""

This view helps you to get a small overview over the most important information about your visitors.
Filter Mails in the same way as the listing with the filter area.
Below the Filter Area you will see some small diagrams (Referer Domain, Referer URI, Visitors Country, Visitor uses a Mobile Device, Website Language, Browser Language, Page Funnel).
Note: To activate the marketing information, please add the Powermail Marketing Static Template to your Root page.

|backend_module_reportingmarketing|

.. _manageMailsWithBackendModuleToolsFunctionCheck:

Function Check
~~~~~~~~~~~~~~

This views helps you to identify problems with your TYPO3-Installation and Powermail.
Beside some basic checks there is a mail function. This function basicly works like the main powermail mail function. Test this function if your forms don't send mails.

Note: This view is for admins only.

|backend_module_functioncheck|

.. t3-field-list-table::
 :header-rows: 1

 - :Title:
      Title
   :Description:
      Description
   :Additional:
      Additional

 - :Title:
      Version
   :Description:
      This line shows you the current version of powermail.
   :Additional:
      If there are values in tx_extensionmanager_domain_model_extension, you will see a note if there is a newer version of powermail available. If a powermail version was marked as unsecure, this is also shown.

 - :Title:
      TYPO3 Version
   :Description:
      This check compares if the installed powermail version fits to the support TYPO3 version.
   :Additional:
      -

 - :Title:
      Development Context
   :Description:
      This line gives you a hint if you use powermailDevelopContextEmail to send all mails to a defined receiver
   :Additional:
      This note is only shown, if your TYPO3 runs in Development Context

 - :Title:
      TypoScript Static Template
   :Description:
      Checks if TypoScript is available on current page
   :Additional:
      -

 - :Title:
      Frontend Session Check
   :Description:
      Check if Installation can work with FE-Sessions
   :Additional:
      -

 - :Title:
      Extension Manager Updated
   :Description:
      Check if all params from Extension Manager settings are available
   :Additional:
      -

 - :Title:
      Upload Folder
   :Description:
      Check if folder uploads/tx_powermail/ exists
   :Additional:
      If folder does not exist, you can create one, by clicking the "fix it"-button

 - :Title:
      Localized Forms
   :Description:
      Check if there are failures in localized forms
   :Additional:
      If there are failures (field pages must not be empty), there is an option to fix it (please backup your database before!)

 - :Title:
      Localized Fields
   :Description:
      Check if there are failures in localized fields
   :Additional:
      If there are failures (field marker should be empty), there is an option to fix it (please backup your database before!)

 - :Title:
      Test Email Sending
   :Description:
      If you are not sure if powermail can send mails, enter your email-address and press "Send Mail Now"
   :Additional:
      This test is similar to the test in install tool. Sender name and email will be used from LocalConfiguration settings defaultMailFromAddress and defaultMailFromName
