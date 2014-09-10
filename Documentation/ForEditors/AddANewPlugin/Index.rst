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


Add a new Plugin
^^^^^^^^^^^^^^^^

Choose a page where you want to show a powermail form in the Frontend
and go to the page module. Click on the New Button to add a new
content element to this page and choose “powermail”.

|img-72|

Plugin Settings
"""""""""""""""

You will find the plugin settings within the tab “Plugin”. In this
area you see another four tabs (Main Settings, Receiver, Sender,
Submit Page).

|img-72b|

Main Settings
~~~~~~~~~~~~~


Example Configuration
'''''''''''''''''''''

|img-73|

Explanation
'''''''''''

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
      Choose a Powermail Form
   :Description:
      Choose an existing powermail form.
   :Explanation:
      Choose an existing form or create a new one by clicking
      the plus symbol. With this the form (with all pages and fields) will be created on the same page.
   :Tab:
      Main Settings

 - :Field:
      Form Table
   :Description:
      The table shows some form information
   :Explanation:
      If you choose a form and save this content element, a table is visible which gives you some interesting information about the chosen form (Form Name, Form is stored in Page, Number of Pages, Number of Fields). You can edit the form, by clicking the edit icon or the Form Name. If you want to open the page, where the form is stored in, click the Page Title.
   :Tab:
      Main Settings

 - :Field:
      Confirmation Page
   :Description:
      Enable a confirmation page.
   :Explanation:
      This enables a confirmation page (Are theese values correct?) to the
      frontend.
   :Tab:
      Main Settings

 - :Field:
      Double-Opt-In
   :Description:
      Add Double-Opt-In feature to this form.
   :Explanation:
      A user has to confirm his email by clicking a link in a mail first
      before the main mail is sent. Note: You can overwrite the email to the
      user by administrators email address with TypoScript.
   :Tab:
      Main Settings

 - :Field:
      Step by step
   :Description:
      Enable morestep form.
   :Explanation:
      Each page (fieldset) will be splittet to one page in the frontend.
      With JavaScript the user can switch between the pages.
   :Tab:
      Main Settings

 - :Field:
      Where to save Mails
   :Description:
      Choose a page where to store the mails in the database.
   :Explanation:
      You can select a page or a folder. Leaving this empty will store the
      mails on the same page.
   :Tab:
      Main Settings

Receiver
~~~~~~~~


Example Configuration
'''''''''''''''''''''

|img-74|

Explanation
'''''''''''

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
      Receivers Name
   :Description:
      Add the name of the main receiver name.
   :Explanation:
      | - Add a static value
      | - Add a variable like {firstname}
      | - Add a viewhelper call like {f:cObject(typoscriptObjectPath:'lib.test')} to get a value from TypoScript or a userFunc
      | - or mix dynamic and static values
   :Tab:
      Mail to Receiver

 - :Field:
      Receivers Mail
   :Description:
      Add the email address of one or more receivers
   :Explanation:
      | - Add one or more static values (split with a new line)
      | - Add a variable like {email}
      | - Add a viewhelper call like {f:cObject(typoscriptObjectPath:'lib.test')} to get a value from TypoScript or a userFunc
      | - or mix dynamic and static values
   :Tab:
      Mail to Receiver

 - :Field:
      Frontend User Group
   :Description:
      Choose a Frontend User Group.
   :Explanation:
      Select an existing group to send the mail to all users of a given group.
   :Tab:
      Mail to Receiver

 - :Field:
      Subject
   :Description:
      Subject for mail to receiver.
   :Explanation:
      | - Add a static value
      | - Add a variable like {firstname}
      | - Add a viewhelper call like {f:cObject(typoscriptObjectPath:'lib.test')} to get a value from TypoScript or a userFunc
      | - or mix dynamic and static values
   :Tab:
      Mail to Receiver

 - :Field:
      Bodytext
   :Description:
      Add some text for the mail to the receiver.
   :Explanation:
      | - Add a static value
      | - Add {powermail_all} to get all values from the form in one table (with labels)
      | - Add a variable like {firstname}
      | - Add a viewhelper call like {f:cObject(typoscriptObjectPath:'lib.test')} to get a value from TypoScript or a userFunc
      | - or mix dynamic and static values
   :Tab:
      Mail to Receiver

Sender
~~~~~~


Example Configuration
'''''''''''''''''''''

|img-75|

Explanation
'''''''''''

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
      Senders Name
   :Description:
      Add the name of the sender for confirmation mail.
   :Explanation:
      Add the name of the sender for the mail that will be send as confirmation mail back to the user. That is normally a static value. If you want to overwrite it or set it dynamically, please use TypoScript (see Setup).
   :Tab:
      Mail to User

 - :Field:
      Sender Email
   :Description:
      Add the email address of the sender for confirmation mail.
   :Explanation:
      Add the email of the sender for the mail that will be send as confirmation mail back to the user. That is normally a static value. If you want to overwrite it or set it dynamically, please use TypoScript (see Setup).
   :Tab:
      Mail to User

 - :Field:
      Subject
   :Description:
      Subject for confirmation mail to sender. Leaving subject empty disables the mail to the sender.
   :Explanation:
      | - Add a static value
      | - Add a variable like {firstname}
      | - Add a viewhelper call like {f:cObject(typoscriptObjectPath:'lib.test')} to get a value from TypoScript or a userFunc
      | - or mix dynamic and static values
   :Tab:
      Mail to User

 - :Field:
      Bodytext
   :Description:
      Add some text for the confirmation mail to sender.
   :Explanation:
      | - Add a static value
      | - Add {powermail_all} to get all values from the form in one table (with labels)
      | - Add a variable like {firstname}
      | - Add a viewhelper call like {f:cObject(typoscriptObjectPath:'lib.test')} to get a value from TypoScript or a userFunc
      | - or mix dynamic and static values
   :Tab:
      Mail to User


Submit Page
~~~~~~~~~~~


Example Configuration
'''''''''''''''''''''

|img-76|

Explanation
'''''''''''

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
      Text on submit page
   :Description:
      Add some text for submit message. This text will be shown right after a successful submit.
   :Explanation:
      | - Add a static value
      | - Add {powermail_all} to get all values from the form in one table (with labels)
      | - Add a variable like {firstname}
      | - Add a viewhelper call like {f:cObject(typoscriptObjectPath:'lib.test')} to get a value from TypoScript or a userFunc
      | - or mix dynamic and static values
   :Tab:
      Submit Page

 - :Field:
      Redirect
   :Description:
      Add a redirect target instead of adding text (see row above).
   :Explanation:
      As soon as the form is submitted, the user will be redirected to the target
      (internal page, external URL, document, mail address), even if there are values in the field "Text on submit page"
   :Tab:
      Submit Page