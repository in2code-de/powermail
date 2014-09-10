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


Add a new Form
--------------

Choose a page (could also be a folder) where to store the new form-record and change to the list view. Click
on the New Button to add a new record to this page and choose “Forms”.

|img-9|

Form Settings
^^^^^^^^^^^^^

|img-10|

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
      Add a title for your form.
   :Explanation:
      The title is used to find the form in the backend. You can also show the title in the frontend.
   :Tab:
      General

 - :Field:
      Pages
   :Description:
      Add one or more pages to a form.
   :Explanation:
      A form collects a couple of pages. You need minimum 1 page to show a form. If you choose a multistep form, every step is splitted in one page.
   :Tab:
      General

 - :Field:
      Layout
   :Description:
      Choose a layout.
   :Explanation:
      This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries and switch the frontend layout of your form.
   :Tab:
      Extended

 - :Field:
      Language
   :Description:
      Choose a language.
   :Explanation:
      Choose in which frontend language this form should be rendered.
   :Tab:
      Access

 - :Field:
      Hide
   :Description:
      Disable the form
   :Explanation:
      Enable or disable a form with all pages and fields.
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


Pages Settings
^^^^^^^^^^^^^^

|img-11|

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
      Add a title for your page.
   :Explanation:
      The title is used to find the page in the backend. You can also show the title in the frontend.
   :Tab:
      General

 - :Field:
      Fields
   :Description:
      Add one or more fields to this page.
   :Explanation:
      A page collects a couple of fields. You need minimum 1 field to show a form.
   :Tab:
      General

 - :Field:
      Note
   :Description:
      Just a small Note.
   :Explanation:
      This note shows you if there is no Sendermail or Sendername marked in the fields. Without this information powermail will set default values for the Sendername and Senderemail. If you are aware of this and you don't want to see this information in future (for this form), you can disable this note.
   :Tab:
      General

 - :Field:
      Layout
   :Description:
      Choose a layout.
   :Explanation:
      This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries.
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


Field Settings
^^^^^^^^^^^^^^


General
"""""""


Backend Configuration Example
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

|img-12|

|img-13|

Explanation
~~~~~~~~~~~

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
      Email of sender
   :Description:
      Check this if this field contains the email of the sender.
   :Explanation:
      This is needed to set the correct sender-email-address. If there is no field marked as Senderemail within the current form, powermail will use a default value for the Senderemail.
   :Tab:
      General

 - :Field:
      Name of sender
   :Description:
      Check this if this field contains the name (or a part of the name) of the sender.
   :Explanation:
      This is needed to set the correct sender-name. If there is no field marked as Sendername within the current form, powermail will use a default value for the Sendername.
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
      Validation
   :Description:
      Validate the user input with a validator.
   :Explanation:
      Possible Validation Methods are: Email, URL, Phone, Numbers only, Letters only, Min Number, Max Number, Range, Length, Pattern (RegEx)
   :Tab:
      Extended

 - :Field:
      Prefill with value
   :Description:
      Prefill field value with a static content.
   :Explanation:
      Other possibilities to prefill a field: TypoScript, GET or POST params
   :Tab:
      Extended

 - :Field:
      Placeholder
   :Description:
      Add a placeholder for this input field.
   :Explanation:
      A placeholder text is an example, that should help the user to fill out an input field. This text is shown in bright grey within the input field. If you have a name field, you could use the placeholder "John Doe".
   :Tab:
      Extended

 - :Field:
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
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


Field Types
~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Field:
      Field
   :Description:
      Description
   :HTML:
      HTML
   :Category:
      Tab
   :Example:
      Example
   :Ref:
      Details

 - :Field:
      Textfield (input)
   :Description:
      Simple text field (one line)
   :HTML:
      <input type=”text” />
   :Category:
      Standard
   :Example:
      |img-14|
   :Ref:
      :ref:`input`

 - :Field:
      Textfield with more rows (Textarea)
   :Description:
      Text field with more lines
   :HTML:
      <textarea></textarea>
   :Category:
      Standard
   :Example:
      |img-15|
   :Ref:
      :ref:`textarea`

 - :Field:
      Selectfield
   :Description:
      Selector box (Dropdown)
   :HTML:
      <select><option>X</option></select>
   :Category:
      Standard
   :Example:
      |img-16|
   :Ref:
      :ref:`select`

 - :Field:
      Checkboxes
   :Description:
      Checkbox (Possibility to select more than only one)
   :HTML:
      <input type=”checkbox” />
   :Category:
      Standard
   :Example:
      |img-17|
   :Ref:
      :ref:`check`

 - :Field:
      Radiobuttons
   :Description:
      Radio Buttons (Possibility to check only one)
   :HTML:
      <input type=”radio” />
   :Category:
      Standard
   :Example:
      |img-18|
   :Ref:
      :ref:`radio`

 - :Field:
      Submit
   :Description:
      Send Form
   :HTML:
      <input type=”submit” />
   :Category:
      Standard
   :Example:
      |img-19|
   :Ref:
      :ref:`submit`

 - :Field:
      Captcha
   :Description:
      Captcha Check against spam
   :HTML:
      <input type=”text” />
   :Category:
      Extended
   :Example:
      |img-20|
   :Ref:
      :ref:`captcha`

 - :Field:
      Reset
   :Description:
      Reset cleans all fieldvalues in the form
   :HTML:
      <input type=”reset” />
   :Category:
      Extended
   :Example:
      |img-21|
   :Ref:
      :ref:`reset`

 - :Field:
      Show some text
   :Description:
      This field let you show some text in the form
   :HTML:
      This is a Test
   :Category:
      Extended
   :Example:
      |img-22|
   :Ref:
      :ref:`text`

 - :Field:
      Content Element
   :Description:
      Show an existing Content Element
   :HTML:
      Text with <img src=”...” />
   :Category:
      Extended
   :Example:
      |img-23|
   :Ref:
      :ref:`contentElement`

 - :Field:
      Show HTML
   :Description:
      Add some html text. Text is automaticle parsed through a removeXSS-Function. If you are aware of what you are doing, you can disable the removeXSS function with TypoScript constants.
   :HTML:
      This is a <b>Test</b>
   :Category:
      Extended
   :Example:
      |img-24|
   :Ref:
      :ref:`html`

 - :Field:
      Password Field
   :Description:
      Two fields for a password check
   :HTML:
      <input type=”password” /> <input type=”password” />
   :Category:
      Extended
   :Example:
      |img-25|
   :Ref:
      :ref:`password`

 - :Field:
      File Upload
   :Description:
      Upload one or more files
   :HTML:
      <input type=”file” />
   :Category:
      Extended
   :Example:
      |img-26|
   :Ref:
      :ref:`file`

 - :Field:
      Hidden Field
   :Description:
      Renders a hidden field, where you can store some additional information within the form.
   :HTML:
      <input type=”hidden” />
   :Category:
      Extended
   :Example:
      -
   :Ref:
      :ref:`hidden`

 - :Field:
      Date
   :Description:
      Datepicker field (Date, Datetime or Time)
   :HTML:
      <input type=”date” />
   :Category:
      Extended
   :Example:
      |img-27|
   :Ref:
      :ref:`date`

 - :Field:
      Countryselection
   :Description:
      Choose a Country
   :HTML:
      <select><option>France</option><option>Germany</option></select>
   :Category:
      Extended
   :Example:
      |img-27b|
   :Ref:
      :ref:`country`

 - :Field:
      Location
   :Description:
      Location field. Browser will ask the user if it's ok to fill the field
      with his current location.
   :HTML:
      <input type=”text” />
   :Category:
      Extended
   :Example:
      |img-28|
   :Ref:
      :ref:`location`

 - :Field:
      TypoScript
   :Description:
      Fill values from TypoScript
   :HTML:
      This is a <b>Test</b>
   :Category:
      Extended
   :Example:
      |img-24|
   :Ref:
      :ref:`typoscript`


.. _input:

Textfield (Input)
~~~~~~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** An input field is the most used field for forms. The user can fill it in just one line.
- **Mandatory:** This field could be marked as mandatory, so the user must fill out this field, otherwise the form can not be submitted.
- **Validation:** This field can also be validated (HTML5, JavaScript and/or PHP) for different inputtypes (email, url, phone, numbers only, letters only, min number, max number, range, length, pattern)
- **Prefill:** An input field can be prefilled from FlexForm, TypoScript, GET/Post-Params or from FE_User table.

Frontend Output Example
'''''''''''''''''''''''

|img-29|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-12|

|img-13|

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
      Email of sender
   :Description:
      Check this if this field contains the email of the sender.
   :Explanation:
      This is needed to set the correct sender-email-address. If there is no field marked as Senderemail within the current form, powermail will use a default value for the Senderemail.
   :Tab:
      General

 - :Field:
      Name of sender
   :Description:
      Check this if this field contains the name (or a part of the name) of the sender.
   :Explanation:
      This is needed to set the correct sender-name. If there is no field marked as Sendername within the current form, powermail will use a default value for the Sendername.
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
      Validation
   :Description:
      Validate the user input with a validator.
   :Explanation:
      Possible Validation Methods are: Email, URL, Phone, Numbers only, Letters only, Min Number, Max Number, Range, Length, Pattern (RegEx)
   :Tab:
      Extended

 - :Field:
      Prefill with value
   :Description:
      Prefill field value with a static content.
   :Explanation:
      Other possibilities to prefill a field: TypoScript, GET or POST params
   :Tab:
      Extended

 - :Field:
      Placeholder
   :Description:
      Add a placeholder for this input field.
   :Explanation:
      A placeholder text is an example, that should help the user to fill out an input field. This text is shown in bright grey within the input field. If you have a name field, you could use the placeholder "John Doe".
   :Tab:
      Extended

 - :Field:
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
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


.. _textarea:

Text with more rows (Textarea)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** A texarea basicly works like a normal input field, but offers the possibility to write in multiple lines.
- **Mandatory:** This field could be marked as mandatory, so the user must fill out this field, otherwise the form can not be submitted.
- **Validation:** This field can also be validated (HTML5, JavaScript and/or PHP) for different inputtypes (email, url, phone, numbers only, letters only, min number, max number, range, length, pattern)
- **Prefill:** An input field can be prefilled from FlexForm, TypoScript, GET/Post-Params or from FE_User table.

Frontend Output Example
'''''''''''''''''''''''

|img-30|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-31|

|img-32|


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
      Email of sender
   :Description:
      Check this if this field contains the email of the sender.
   :Explanation:
      This is needed to set the correct sender-email-address. If there is no field marked as Senderemail within the current form, powermail will use a default value for the Senderemail.
   :Tab:
      General

 - :Field:
      Name of sender
   :Description:
      Check this if this field contains the name (or a part of the name) of the sender.
   :Explanation:
      This is needed to set the correct sender-name. If there is no field marked as Sendername within the current form, powermail will use a default value for the Sendername.
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
      Validation
   :Description:
      Validate the user input with a validator.
   :Explanation:
      Possible Validation Methods are: Email, URL, Phone, Numbers only, Letters only, Min Number, Max Number, Range, Length, Pattern (RegEx)
   :Tab:
      Extended

 - :Field:
      Prefill with value
   :Description:
      Prefill field value with a static content.
   :Explanation:
      Other possibilities to prefill a field: TypoScript, GET or POST params
   :Tab:
      Extended

 - :Field:
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
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


.. _select:

Selectfield
~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** A select field is also called "dropdown", "combobox" or "picklist". The user can choose an option. It's also possible to config a multiselectfield - the user can choose more than only one option by holding the CRTL-Key when clicking a second option. Add some options and separate it with a new line.
- **Mandatory:** This field could be marked as mandatory, so the user must fill out this field, otherwise the form can not be submitted.
- **Prefill:** The field can be preselected from FlexForm, TypoScript, GET/Post-Params or from FE_User table.
- **Special:** Options could also filled by TypoScript in powermail 2.1 and higher (static or dynamic)

Frontend Output Example
'''''''''''''''''''''''

|img-33|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-34|

|img-35|

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
      Options
   :Description:
      Options to select
   :Explanation:
      Separate each with a new line. **Note: see following
      table for examples, how to preselect or clean a value**
   :Tab:
      General

 - :Field:
      Email of sender
   :Description:
      Check this if this field contains the email of the sender.
   :Explanation:
      This is needed to set the correct sender-email-address. If there is no field marked as Senderemail within the current form, powermail will use a default value for the Senderemail.
   :Tab:
      General

 - :Field:
      Name of sender
   :Description:
      Check this if this field contains the name (or a part of the name) of the sender.
   :Explanation:
      This is needed to set the correct sender-name. If there is no field marked as Sendername within the current form, powermail will use a default value for the Sendername.
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
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
   :Tab:
      Extended

 - :Field:
      Create from TypoScript
   :Description:
      Fill Options from TypoScript
   :Explanation:
      If you want to create your options (see above) from TypoScript, you can use this field. Please split each line in your TypoScript with [\\n]


      Example:

      lib.options = TEXT

      lib.options.value = red[\\n]blue[\\n]pink
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
      Description
   :Description:
      Add a description for this field.
   :Explanation:
      Per default a description will be rendered as title-attribute in the labels in frontend.
   :Tab:
      Extended

 - :Field:
      Multiselect
   :Description:
      Choose a layout.
   :Explanation:
      This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries.
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


Option examples for selectbox
'''''''''''''''''''''''''''''

.. t3-field-list-table::
 :header-rows: 1

 - :Example:
     Example option
   :HTML:
     Generated HTML code in Frontend

 - :Example:
      Red
   :HTML:
      <option value=”Red”>Red</option>

 - :Example:
      Yellow \| 1
   :HTML:
      <option value=”1”>Yellow</option>

 - :Example:
      Blue \|
   :HTML:
      <option value=””>Blue</option>

 - :Example:
      Black Shoes \| black \| \*
   :HTML:
      <option value=”black” selected=”selected”>Black Shoes</option>

 - :Example:
      White \| \| \*
   :HTML:
      <option value=”” selected=”selected”>White</option>

 - :Example:
      Please choose... \|
      | red
      | blue
   :HTML:
      | <option value=””>Please choose...</option>
      | <option>red</option>
      | <option>blue</option>


.. _check:

Checkboxes
~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** Checkboxes are made for a multiselecting of options. The user can check one or more options.
- **Mandatory:** This field could be marked as mandatory, so the user must fill min 1 checkbox, otherwise the form can not be submitted.
- **Prefill:** The field can be preselected from FlexForm, TypoScript, GET/Post-Params or from FE_User table.
- **Special:** Options could also filled by TypoScript in powermail 2.1 and higher (static or dynamic)

Frontend Output Example
'''''''''''''''''''''''

|img-36|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-37|

|img-38|

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
      Options
   :Description:
      Options to check
   :Explanation:
      Separate each with a new line. **Note: see following
      table for examples, how to precheck or clean a value**
   :Tab:
      General

 - :Field:
      Email of sender
   :Description:
      Check this if this field contains the email of the sender.
   :Explanation:
      This is needed to set the correct sender-email-address. If there is no field marked as Senderemail within the current form, powermail will use a default value for the Senderemail.
   :Tab:
      General

 - :Field:
      Name of sender
   :Description:
      Check this if this field contains the name (or a part of the name) of the sender.
   :Explanation:
      This is needed to set the correct sender-name. If there is no field marked as Sendername within the current form, powermail will use a default value for the Sendername.
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
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
   :Tab:
      Extended

 - :Field:
      Create from TypoScript
   :Description:
      Fill Options from TypoScript
   :Explanation:
      If you want to create your options (see above) from TypoScript, you can use this field. Please split each line in your TypoScript with [\n]
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


Option examples for checkbox
''''''''''''''''''''''''''''

.. t3-field-list-table::
 :header-rows: 1

 - :Example:
     Example option
   :HTML:
     Generated HTML code in Frontend

 - :Example:
      Red
   :HTML:
      <label>Red</label><input value=”Red” />

 - :Example:
      Yellow \| 1
   :HTML:
      <label>Yellow</label><input value=”1” />

 - :Example:
      Blue \|
   :HTML:
      <label>Blue</label><input value=”” />

 - :Example:
      Black Shoes \| black \| \*
   :HTML:
      <label>Black Shoes</label><input value=”black” checked=”checked” />

 - :Example:
      White \| \| \*
   :HTML:
      <label>White</label><input value=”” checked=”checked” />

 - :Example:
      | Red Shoes \| red \| \*
      | Yellow Shoes \| yellow \| \*
   :HTML:
      | <label>Red Shoes</label><input value=”red” checked=”checked” />
      | <label>Yellow Shoes</label><input value=”yellow” checked=”checked” />


.. _radio:

Radiobuttons
~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** Radiobuttons are made for a single select of an options in the same layout as checkboxes.
- **Mandatory:** This field could be marked as mandatory, so the user must fill min 1 radiobutton, otherwise the form can not be submitted.
- **Prefill:** The field can be preselected from FlexForm, TypoScript, GET/Post-Params or from FE_User table.
- **Special:** Options could also filled by TypoScript in powermail 2.1 and higher (static or dynamic)

Frontend Output Example
'''''''''''''''''''''''

|img-39|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-40|

|img-41|

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
      Options
   :Description:
      Options to check
   :Explanation:
      Separate each with a new line. **Note: see following
      table for examples, how to precheck or clean a value**
   :Tab:
      General

 - :Field:
      Email of sender
   :Description:
      Check this if this field contains the email of the sender.
   :Explanation:
      This is needed to set the correct sender-email-address. If there is no field marked as Senderemail within the current form, powermail will use a default value for the Senderemail.
   :Tab:
      General

 - :Field:
      Name of sender
   :Description:
      Check this if this field contains the name (or a part of the name) of the sender.
   :Explanation:
      This is needed to set the correct sender-name. If there is no field marked as Sendername within the current form, powermail will use a default value for the Sendername.
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
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
   :Tab:
      Extended

 - :Field:
      Create from TypoScript
   :Description:
      Fill Options from TypoScript
   :Explanation:
      If you want to create your options (see above) from TypoScript, you can use this field. Please split each line in your TypoScript with [\n]
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

Option examples for radio buttons
'''''''''''''''''''''''''''''''''

.. t3-field-list-table::
 :header-rows: 1

 - :Example:
     Example option
   :HTML:
     Generated HTML code in Frontend

 - :Example:
      Red
   :HTML:
      <label>Red</label><input value=”Red” />

 - :Example:
      Yellow \| 1
   :HTML:
      <label>Yellow</label><input value=”1” />

 - :Example:
      Blue \|
   :HTML:
      <label>Blue</label><input value=”” />

 - :Example:
      Black Shoes \| black \| \*
   :HTML:
      <label>Black Shoes</label><input value=”black” checked=”checked” />

 - :Example:
      White \| \| \*
   :HTML:
      <label>White</label><input value=”” checked=”checked” />

 - :Example:
      | Red Shoes \| red \| \*
      | Yellow Shoes \| yellow \| \*
   :HTML:
      | <label>Red Shoes</label><input value=”red” checked=”checked” />
      | <label>Yellow Shoes</label><input value=”yellow” checked=”checked” />


.. _submit:

Submit
~~~~~~

What does it do?
''''''''''''''''

- **General:** A click on the submit field sends the form to the server.

Frontend Output Example
'''''''''''''''''''''''

|img-42|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-43|

|img-44|

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
      Layout
   :Description:
      Choose a layout.
   :Explanation:
      This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries.
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

.. _captcha:

Captcha Field
~~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** Want to prevent spam? Spamshield is integrated in powermail 2.x. In addtion you can add a calculating-captcha field to the form. The form can only be submitted if the result of the captcha is correct.

Frontend Output Example
'''''''''''''''''''''''

|img-45|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-46|

|img-47|

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


.. _reset:

Reset
~~~~~

What does it do?
''''''''''''''''

- **General:** The pendant to a submit field is the reset field. If the user clicks on reset, old input values are deleted from the current form.

Frontend Output Example
'''''''''''''''''''''''

|img-48|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-49|

|img-50|

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

.. _text:

Show some Text
~~~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** If you want to show some text in the form, use this field. This text is not submitted.

Frontend Output Example
'''''''''''''''''''''''

|img-51|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-52|

|img-53|

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
      Add some text
   :Description:
      This is the field for the text
   :Explanation:
      HTML Tags are not allowed for security reasons
   :Tab:
      General

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

.. _contentElement:

Content Element
~~~~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** If you want to show a content element within your form (text, text with image, etc...), use this field. An element browser allows you to select a tt_content record. This text is not submitted.

Frontend Output Example
'''''''''''''''''''''''

|img-54|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-55|

|img-56|

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
      Select Content Element
   :Description:
      Select an existing content element to show.
   :Explanation:
      Select any existing content element in the element browser. This Content Element will be rendered in the frontend.
   :Tab:
      General

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

.. _html:

Show HTML
~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** If you want to show some html-text in the form, use this field. Per default the text is parsed through a removeXSS-Function. If you are aware of possible security problems from your editors, the admin can disable removeXSS via TypoScript. This text is not submitted.

Frontend Output Example
'''''''''''''''''''''''

|img-57|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-58|

|img-59|

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
      Add some text
   :Description:
      This is the field for the html tags and text
   :Explanation:
      HTML Tags are not allowed for security reasons by default. Can be
      enabled from the administrator by TypoScript constants.
   :Tab:
      General

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

.. _password:

Password Field
~~~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** If you want to show two password field in the frontend, use this fieldtype. Password-fields can be set to mandatory fields. A validator checks if both values are the same. Passwords are stored as cleartext into database.

Frontend Output Example
'''''''''''''''''''''''

|img-25|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-60|

|img-59|

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


.. _file:

File Upload
~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** Enable a fileupload in frontend with this field. Multiupload is possible with modern browsers (HTML5 needed). Allowed filesize and fileextensions and a randomize Filename function can be set via TypoScript.

Frontend Output Example
'''''''''''''''''''''''

|img-61|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-62|

|img-62b|

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
      Layout
   :Description:
      Choose a layout.
   :Explanation:
      This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries.
   :Tab:
      Extended

 - :Field:
      Multiselect
   :Description:
      Allow upload of more than only one file.
   :Explanation:
      Multiupload via HTML5 - the visitors browser must support this feature. If not, only one file could be uploaded.
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

.. _hidden:

Hidden Field
~~~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** It could be useful to send some values within the form, that should not be displayed in frontend. Use a hidden field for this.
- **Prefill:** This field can be prefilled from FlexForm, TypoScript, GET/Post-Params or from FE_User table.

Frontend Output Example
'''''''''''''''''''''''

Because it is "hidden", there is no visible frontend output :)


Backend Configuration Example
'''''''''''''''''''''''''''''

|img-63|

|img-64|

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
      Email of sender
   :Description:
      Check this if this field contains the email of the sender.
   :Explanation:
      This is needed to set the correct sender-email-address. If there is no field marked as Senderemail within the current form, powermail will use a default value for the Senderemail.
   :Tab:
      General

 - :Field:
      Name of sender
   :Description:
      Check this if this field contains the name (or a part of the name) of the sender.
   :Explanation:
      This is needed to set the correct sender-name. If there is no field marked as Sendername within the current form, powermail will use a default value for the Sendername.
   :Tab:
      General

 - :Field:
      Prefill with value
   :Description:
      Prefill field value with a static content.
   :Explanation:
      Other possibilities to prefill a field: TypoScript, GET or POST params
   :Tab:
      Extended

 - :Field:
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
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

.. _date:

Date
~~~~

What does it do?
''''''''''''''''

- **General:** Do you want to render a datepicker for date (or datetime or time), you can use this field type. Per default html5 date fields are used with a JavaScript fallback. If you want to force the JavaScript Datepicker, you can use TypoScript. Dateformat will change by the frontend language. You can use TypoScript to use any dateformat (locallang.xlf).

Frontend Output Example
'''''''''''''''''''''''

|img-65|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-66|

|img-67|

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

.. _country:

Country
~~~~~~~

What does it do?
''''''''''''''''

- **General:** This field is rendered as selectfield (see above). But the field is filled with countries. Per default the countrylist is a static list. If you want to change the sorting, value or labels, use the extension static_info_tables and have a look into the country partial HTML-File.
- **Prefill:** Country field can be preselected with it's value (like DEU for Germany)

Frontend Output Example
'''''''''''''''''''''''

|img-country|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-country2|

|img-country3|

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
      Prefill with value
   :Description:
      Preselect field value with a static content
   :Explanation:
      Preselect field value with a static content (e.g. DEU for Germany, etc...)
   :Tab:
      Extended

 - :Field:
      Value from logged in Frontend User
   :Description:
      Check if field should be filled from the FE_Users table of a logged in fe_user.
   :Explanation:
      This value overwrites a static value, if set.
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


.. _location:

Location
~~~~~~~~

What does it do?
''''''''''''''''

- **General:** The location field is an input field, which could be prefilled with JavaScript. The user is asked if he wants to allow the filling with his current location. If he clicks on yes, the field is prefilled with Street, Streetnumber and Country.

Frontend Output Example
'''''''''''''''''''''''

|img-68|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-69|

|img-69b|

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
      Layout
   :Description:
      Choose a layout.
   :Explanation:
      This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries.
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

.. _typoscript:

TypoScript
~~~~~~~~~~

What does it do?
''''''''''''''''

- **General:** Do you want to display something special? Use TypoScript for the frontend rendering.

Frontend Output Example
'''''''''''''''''''''''

|img-70|

Backend Configuration Example
'''''''''''''''''''''''''''''

|img-71|

|img-71b|

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
      TypoScript Path
   :Description:
      Add TypoScript path to show in frontend.
   :Explanation:
      | Example TypoScript could be:
      | lib.test = TEXT
      | lib.test.value = xyz
   :Tab:
      General

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
