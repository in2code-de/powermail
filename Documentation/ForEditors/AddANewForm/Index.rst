.. include:: Images.txt

.. _addANewForm:

Add a new Form
--------------

Introduction
^^^^^^^^^^^^

A powermail forms is the main record which contains multiple pages and fields.
So if you create and store a form, you can use this on one or more pages.
A form that is included in a plugin/pagecontent (see :ref:`addANewPlugin`) will be shown
in frontend and can be used by website visitors.

First step
^^^^^^^^^^

Choose a page (could also be a folder) where to store the new form-record and change to the list view.
Click on the New Button to add a new record to this page and choose “Forms”.

|powermail_records|


Form Settings
^^^^^^^^^^^^^

|record_form_detail1|

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


Page Settings
^^^^^^^^^^^^^

|record_page_detail1|

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
      Just a small Note (in some cases).
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

|record_field_input_tab1|

|record_field_input_tab2|

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
      |example_field_input|
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
      |example_field_textarea|
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
      |example_field_select|
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
      |example_field_checkbox|
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
      |example_field_radio|
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
      |example_field_submit|
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
      |example_field_captcha|
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
      |example_field_reset|
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
      |example_field_label|
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
      |example_field_content|
   :Ref:
      :ref:`contentElement`

 - :Field:
      Show HTML
   :Description:
      Add some html text. Per default output of fields of type HTML is parsed through a htmlspecialchars() function to avoid Cross-Site-Scripting for security reasons. If you are aware of possible XSS-problems, caused by editors, you can enable it and your original HTML is shown in the Frontend.
   :HTML:
      This is a <b>Test</b>
   :Category:
      Extended
   :Example:
      |example_field_html|
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
      |example_field_password|
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
      |example_field_file|
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
      \-
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
      |example_field_date|
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
      |example_field_country|
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
      |example_field_location|
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
      |example_field_typoscript|
   :Ref:
      :ref:`typoscript`



**Fieldtype Details**

.. toctree::
    :maxdepth: 3
    :titlesonly:
    :glob:

    FieldInput/Index
    FieldTextarea/Index
    FieldSelect/Index
    FieldCheck/Index
    FieldRadio/Index
    FieldSubmit/Index
    FieldCaptcha/Index
    FieldReset/Index
    FieldText/Index
    FieldContentElement/Index
    FieldHtml/Index
    FieldPassword/Index
    FieldFile/Index
    FieldHidden/Index
    FieldDate/Index
    FieldCountry/Index
    FieldLocation/Index
    FieldTypoScript/Index
