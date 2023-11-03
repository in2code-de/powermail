# Upgrade Instructions and breaking changes

## Version 10.7.4

If you want to contribute, the URLs for the development instance changed slightly. The TYPO3 version was added
to the url.

Please use https://powermail-v11.ddev.site or https://local.powermail-v11.de as URLs for the local dev environment.

This change eases the parallel execution of local dev environments for the various versions.

## Version 10.0.0

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
