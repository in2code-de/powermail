.. include:: Images.txt
.. include:: ../../Includes.txt

Powermail\_Frontend
^^^^^^^^^^^^^^^^^^^


Introduction
""""""""""""

powermail\_frontend gives you the possibility to show the stored mails
again in the frontend. With this additional plugin (Pi2), it's possible to create
a small guestbook or database listing. In addition some export methods
are included (XLS, CSV, RSS) or logged in FE\_Users can change the
values again.

|frontend_pi2|

Plugin Settings
"""""""""""""""

|plugin_pi2_tab1|

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
      Field
   :Description:
      Description
   :Explanation:
      Explanation
   :Tab:
      Tab

 - :Field:
      Choose your view
   :Description:
      Choose a view
   :Explanation:
      List or Detail, Edit, All Views
   :Tab:
      Main Settings

 - :Field:
      Choose a form
   :Description:
      Choose an existing form
   :Explanation:
      Select a Form and click save
   :Tab:
      Main Settings

 - :Field:
      Select a page with mails
   :Description:
      Select a page with mails (optionsl)
   :Explanation:
      Only mails which are stored in the given page are shown in the frontent (optional setting).
   :Tab:
      Main Settings

 - :Field:
      Choose Fields
   :Description:
      Choose Fields (Empty: All Fields)
   :Explanation:
      Let the selection empty if you want to see all form values.
   :Tab:
      Listview

 - :Field:
      Export Formats
   :Description:
      Add links to different export methods by adding some.
   :Explanation:
      XLS, CSV or RSS feed is possible in powermail\_frontend.
   :Tab:
      Listview

 - :Field:
      Show entries...
   :Description:
      If you want to show only mails within a timeperiod, add some seconds.
   :Explanation:
      If you want to show the mails of the last 24h add “86400”. Let this field empty to disable this function.
   :Tab:
      Listview

 - :Field:
      Show max. X entries...
   :Description:
      Limit for mail output.
   :Explanation:
      Add a number if you want to show only X entries.
   :Tab:
      Listview

 - :Field:
      Page with Plugin for list view...
   :Description:
      Select the page with the list plugin.
   :Explanation:
      This is needed if the plugin shows the edit or single view and it should link you back to the list view. Let this field empty means list view is on current page.
   :Tab:
      Listview

 - :Field:
      Own entries
   :Description:
      Show only my mails.
   :Explanation:
      Show only the mails that where submitted by the current logged in FE_User in the list view.
   :Tab:
      Listview

 - :Field:
      Choose Fields to show
   :Description:
      What field should be listed in the detail view?
   :Explanation:
      Let the selector empty if you want to see all form values.
   :Tab:
      Detailview

 - :Field:
      Page with Plugin for detail view
   :Description:
      Select the page with the detail plugin.
   :Explanation:
      This is needed if the plugin shows the list view and it should link
      you to the detail view. Let this field empty means detail view is on
      current page.
   :Tab:
      Detailview

 - :Field:
      Add searchfield
   :Description:
      Add some search fields above the list.
   :Explanation:
      Select a single field or choose [Fulltext Search] for an overall
      search
   :Tab:
      Searchsettings

 - :Field:
      Add ABC filter
   :Description:
      Add ABC filter list in frontend.
   :Explanation:
      Select a field with a leading letter to filter for it. Firstname
      means: When a user clicks on A, all mails with a beginning A in the
      firstname are shown (Alex, Andreas, Agnes, etc...)
   :Tab:
      Searchsettings

 - :Field:
      Choose Fields to edit
   :Description:
      What fields should be editable?
   :Explanation:
      Let the selector empty if you want to edit all fields.
   :Tab:
      Editview

 - :Field:
      Choose one or more Frontend-Users with permissions to change
   :Description:
      Choose a frontend user who is able to edit a mail.
   :Explanation:
      Value can be one or more static FE\_Users or the Creator of a mail
      [Owner]. You can select a group in addition (see next row).
   :Tab:
      Editview

 - :Field:
      Choose one or more Frontend-Groups with permissions to change
   :Description:
      Choose frontend users of a group which are able to edit a mail.
   :Explanation:
      Value can be one or more static FE\_User Groups or the Creator Group
      of a mail [Owner]. You can select some single FE\_Users in addition
      (see row before).
   :Tab:
      Editview

 - :Field:
      Page with Plugin for edit view
   :Description:
      Select the page with the edit plugin.
   :Explanation:
      This is needed if the plugin shows the list view and it should link
      you to the edit view. Let this field empty means edit view is on
      current page.
   :Tab:
      Editview
