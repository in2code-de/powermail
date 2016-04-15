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


.. _date:

Date
~~~~

What does it do?
''''''''''''''''

- **General:** Do you want to render a datepicker for date (or datetime or time), you can use this field type. Per default html5 date fields are used with a JavaScript fallback. If you want to force the JavaScript Datepicker, you can use TypoScript. Dateformat will change by the frontend language. You can use TypoScript to use any dateformat (locallang.xlf).

Frontend Output Example
'''''''''''''''''''''''

|example_field_date|

Backend Configuration Example
'''''''''''''''''''''''''''''

|record_field_date_tab1|

|record_field_date_tab2|

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
      Title
   :Description:
      Add a label for this field.
   :Explanation:
      The label is shown in the frontend near to this field.
   :Tab:
      General

 - :Field:
      Type
   :Description:
      Choose a fieldtype.
   :Explanation:
      See explanation below for a special fieldtype. Different fields are related to some fieldtypes – not all fields are shown on every type.
   :Tab:
      General

 - :Field:
      Mandatory Field
   :Description:
      This field must contain input.
   :Explanation:
      Check this if the field must contain input, otherwise submitting the form is not possible.
   :Tab:
      Extended

 - :Field:
      Layout
   :Description:
      Choose a layout.
   :Explanation:
      This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries.
   :Tab:
      Extended

 - :Field:
      Datepicker Mode
   :Description:
      Choose Date, Datetime or Time
   :Explanation:
      Choose the frontend datepicker with date only, time only or a mix of both
   :Tab:
      Extended

 - :Field:
      Description
   :Description:
      Add a description for this field.
   :Explanation:
      Per default a description will be rendered as title-attribute in the labels in frontend.
   :Tab:
      Extended

 - :Field:
      Variables – Individual Fieldname
   :Description:
      This is a marker of this field.
   :Explanation:
      Use a field variable with {marker} in any RTE or HTML-Template. The marker name is equal in any language.
   :Tab:
      Extended

 - :Field:
      Add own Variable
   :Description:
      Check this, if you want to set your own marker (see row before).
   :Explanation:
      After checking this button, TYPO3 ask you to reload. After a reload, you see a new field for setting an own marker.
   :Tab:
      Extended

 - :Field:
      Language
   :Description:
      Choose a language.
   :Explanation:
      Choose in which frontend language this record should be rendered.
   :Tab:
      Access

 - :Field:
      Hide
   :Description:
      Disable the form
   :Explanation:
      Enable or disable this record.
   :Tab:
      Access

 - :Field:
      Start
   :Description:
      Startdate for this record.
   :Explanation:
      Same function as known from default content elements or pages in TYPO3.
   :Tab:
      Access

 - :Field:
      Stop
   :Description:
      Stopdate for this record.
   :Explanation:
      Same function as known from default content elements or pages in TYPO3.
   :Tab:
      Access


Date Formats
''''''''''''

If you use a browser, that support modern date fields (e.g. Chrome),
JavaScript Datepicker will not be loaded by default.
In this case the Browser takes the configured language of the OS to set the date-format automaticly.
There is no change to define another dateformat.

But it's possible to enforce the JavaScript Datepicker even in Chrome with a line of TypoScript Constants:

:typoscript:`plugin.tx_powermail.settings.misc.forceJavaScriptDatePicker = 1`

Beside that, you can define the dateformat for the JavaScript Datepicker.
Depending on the datepicker settings (date, datetime, time), there are different entries in the locallang files.
You can overwrite that via TypoScript:

.. code-block:: text

  plugin.tx_powermail {
    _LOCAL_LANG {
      default {
        datepicker_format_date = Y/m/d
        datepicker_format_time = Y/m/d H:i:s
        datepicker_format_datetime = H:i:s
      }
      fr {
        datepicker_format_date = Y/m/d
        datepicker_format_time = Y/m/d H:i:s
        datepicker_format_datetime = H:i:s
      }
    }
  }

