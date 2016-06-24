
Add own global clientside validators
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

A global validator is something that could normally not be selected by an editor.

Parsley Introduction
~~~~~~~~~~~~~~~~~~~~

Example form, validated with parsley.js, with a required and an email field. In addition to HTML5, this input fields are validated with parsley:
::

   <form data-parsley-validate>
        <input type="text" name="firstname" required="required" />

        <input type="email" name="email" />

        <input type="submit" />
   </form>


Own Parsley Validator
~~~~~~~~~~~~~~~~~~~~~

::

    <input type="text" data-parsley-multiple="3" data-parsley-error-message="Please try again" />
        [...]
    <script type="text/javascript">
        window.ParsleyValidator
            .addValidator('multiple', function (value, requirement) {
                return 0 === value % requirement;
            }, 32)
            .addMessage('en', 'multiple', 'This value should be a multiple of %s');
    </script>




Example Code
""""""""""""

Look at https://github.com/einpraegsam/powermailextended for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended/Classes/Controller/FormController.php)
