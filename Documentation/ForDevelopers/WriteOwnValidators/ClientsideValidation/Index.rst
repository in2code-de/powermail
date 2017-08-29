
Add own global clientside validators
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

A global validator is something that could normally not be selected by an editor.

Parsley Introduction
~~~~~~~~~~~~~~~~~~~~

Example form, validated with parsley.js, with a required and an email field.
In addition to HTML5, this input fields are validated with parsley:

::

   <form data-parsley-validate>
        <input type="text" name="firstname" required="required" />

        <input type="email" name="email" />

        <input type="submit" />
   </form>


Own Parsley Validator
~~~~~~~~~~~~~~~~~~~~~

Quick example
^^^^^^^^^^^^^

See how you can relate a JavaScript validator to a field with a data-attribute. In addition we recommend to set the
message also via data-attribute:

::

    <input type="text" data-parsley-emailverification="@" data-parsley-emailverification-message="No email address" />
        [...]
    <script type="text/javascript">
        window.Parsley.addValidator('emailverification', {
            validateNumber: function(value, requirement) {
                return value.indexOf(requirement) !== -1;
            },
            requirementType: 'string'
        });
    </script>


Step by step
^^^^^^^^^^^^

Let's build an own field (look at the section how to easily extend powermail with own fieldtypes) with two fields
where the user should add his/her email address twice. A validator should check if both strings are identical.

The example fluid template (partial):

::

    {namespace vh=In2code\Powermail\ViewHelpers}

    <div class="powermail_fieldwrap powermail_fieldwrap_type_emailverification powermail_fieldwrap_{field.marker} {field.css} {settings.styles.framework.fieldAndLabelWrappingClasses}">
        <label for="powermail_field_{field.marker}" class="{settings.styles.framework.labelClasses}" title="{field.description}">
            <vh:string.RawAndRemoveXss>{field.title}</vh:string.RawAndRemoveXss><f:if condition="{field.mandatory}"><span class="mandatory">*</span></f:if>
        </label>

        <div class="{settings.styles.framework.fieldWrappingClasses}">
            <f:form.textfield
                    type="email"
                    id="powermail_field_{field.marker}"
                    property="{field.marker}"
                    value=""
                    additionalAttributes="{data-parsley-emailverification-message:'{f:translate(key:\'powermail.validation.emailverification\',extensionName:\'In2template\')}',data-parsley-emailverification:'{field.marker}_mirror'}"
                    class="powermail_emailverification {settings.styles.framework.fieldClasses} {vh:Validation.ErrorClass(field:field, class:'powermail_field_error')}" />
        </div>
    </div>

    <div class="powermail_fieldwrap powermail_fieldwrap_type_emailverification powermail_fieldwrap_{field.marker}_mirror {field.css} {settings.styles.framework.fieldAndLabelWrappingClasses}">
        <label for="powermail_field_{field.marker}_mirror" class="{settings.styles.framework.labelClasses}">
            <f:translate key="writePasswordAgain" /><f:if condition="{field.mandatory}"><span class="mandatory">*</span></f:if>
        </label>

        <div class="{settings.styles.framework.fieldWrappingClasses}">
            <f:form.textfield
                    type="email"
                    id="powermail_field_{field.marker}_mirror"
                    property="{field.marker}_mirror"
                    value=""
                    additionalAttributes="{data-parsley-emailverification-message:'{f:translate(key:\'powermail.validation.emailverification\',extensionName:\'In2template\')}',data-parsley-emailverification:'{field.marker}'}"
                    class="powermail_emailverification {settings.styles.framework.fieldClasses} {vh:Validation.ErrorClass(field:field, class:'powermail_field_error')}" />
        </div>
    </div>

The example JavaScript:

::

    /**
     * @returns {void}
     */
    var addEmailVerificationValidation = function() {
        window.Parsley.addValidator('emailverification', {
            validateString: function(value, markerMirror) {
                return value === getMirrorValue(markerMirror);
            },
            requirementType: 'string'
        });
    };

    /**
     * @param {string} marker
     * @returns {string}
     */
    var getMirrorValue = function(marker) {
        var elements = document.querySelectorAll('input[name="tx_powermail_pi1[field][' + marker + ']"]');
        return elements[0].value;
    };


Documentation
^^^^^^^^^^^^^

Look at http://parsleyjs.org/doc/examples/customvalidator.html for more examples of individual parsley.js validation


Example Code
""""""""""""

Look at https://github.com/einpraegsam/powermailextended for an example extension.
This extension allows you to:

- Extend powermail with a complete new field type (Just a small "Show Text" example)
- Extend powermail with an own Php and JavaScript validator (ZIP validator - number has to start with 8)
- Extend powermail with new field properties (readonly and prepend text from Textarea)
- Extend powermail with an example SignalSlot (see ext_localconf.php and EXT:powermailextended/Classes/Controller/FormController.php)
