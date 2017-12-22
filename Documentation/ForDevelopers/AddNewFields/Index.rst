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


Add new Fields
^^^^^^^^^^^^^^

Keep it simple
""""""""""""""

Per default, there are a lot of Field-Types available in Powermail.

|developer_new_fields1|

If you want to add further fields, you can do this with a little bit of Page TSConfig.
::

   tx_powermail.flexForm.type.addFieldOptions.new = New Field

   # The label could also be written with LLL: to localize the label
   # Example to grab a value from locallang.xml or locallang.xlf
   #tx_powermail.flexForm.type.addFieldOptions.new = LLL:EXT:ext/Resources/Private/Language/locallang.xlf:label

   # Tell powermail that the new fieldtype will transmit anything else then a string (0:string, 1:array, 2:date, 3:file)
   # Example for dataType array
   #tx_powermail.flexForm.type.addFieldOptions.new.dataType = 1

   # The new field is not just a "show some text" field. It's a field where the user can send values and powermail stores the values?
   # You can tell powermail that this new field should be exportable in backend module and via CommandController
   #tx_powermail.flexForm.type.addFieldOptions.new.export = 1

With this TSConfig a new Option is available:

|developer_new_fields2|

If an editor chose the new field, powermail searches by default for a Partial with Name New.html (Default Path is powermail/Resources/Private/Partials/Form/New.html).

Because you should not modify anything within an extension-folder (because of upcoming extension-updates), you should Create a new File in your fileadmin folder - e.g.: fileadmin/powermail/Partials/Form/New.html

Example Content:
::

   <div>
      <h2>This is a complete new Field</h2>
   </div>

Let's take TypoScript Setup to tell powermail, where to find the new partial:
::

   plugin.tx_powermail.view.partialRootPaths {
      0 = EXT:powermail/Resources/Private/Partials/
      1 = fileadmin/powermail/Partials/
   }

Example Code
""""""""""""

Look at https://github.com/einpraegsam/powermailextended for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended/Classes/Controller/FormController.php)
