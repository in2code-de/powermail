# Upgrade Instructions and breaking changes

## Version 12.0.0

### Upgrade - Wizards

Unfortunately the bugfix for https://github.com/in2code-pro/powermail/issues/56 introduced a breaking
change. There are now five submodules, instead of a single big one.

That means, permissions for backend usergroups must be changed in order to use the new modules.

The new version provides an upgrade wizard to migrate the old permission to the new submodules. Please visit
the upgrade wizard in the backend or run it via cli.

### Events

Many events can now modify the transferred mail object.

If you use events, please check the following ones for changed signatures

* FormControllerCreateActionAfterMailDbSavedEvent got the additional argument hash
* FormControllerOptinConfirmActionBeforeRenderViewEvent uses the mail object instead of the mail uid
* all setters in events do not return the event object (as stated in the official documenation)

## Version 11.1

In Version 11.1 the default behaviour for password fields is hashing the value with the default hashing algorithm before storing it in the database.
If you want to restore the old behaviour you have to apply the changes described [here](/ForAdministrators/BestPractice/PasswordField.md).

## Version 10.0

In version 10 we completely removed jQuery, jQuery UI, Datetimepicker, Parsley.js and other old JS stuff from frontend
rendering. We now use an own form framework, that runs with vanilla JS and can be included via async or defer and does
not need any old jQuery version.
To make the switch as smooth as possible for you, the validation output is nearly the same as with parsley.js.
As a new feature we now validate while the input is done from the user.

Nevertheless, some HTML templates have changed:
* Morestep validation is build in the HTML template:
  * EXT:powermail/Resources/Private/Partials/Form/Page.html
* ViewHelper name changed from {vh:validation.enableParsleyAndAjax(form:form)} to {vh:validation.enableJavascriptValidationAndAjax(form:form)}:
  * EXT:powermail/Resources/Private/Templates/Form/Form.html
  * EXT:powermail/Resources/Private/Templates/Output/Edit.html
  * EXT:powermail/Resources/Private/Templates/Form/Confirmation.html
* If you have added jQuery manually, you can remove the implementation (if it was only for powermail)

## Version 9.0

| Version                                         | Description                                                                                                                                                     |
|-------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Resources/Private/Partials/Form/Field/Html.html | Uses now <f:sanitize> instead of <f:format.raw>. This means, that forms which uses the html element, will now clean the HTML for incorrect / possibly bad code. |
